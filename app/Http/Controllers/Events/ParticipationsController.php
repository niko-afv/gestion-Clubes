<?php

namespace App\Http\Controllers\Events;


use App\Club;
use App\Event;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipationsController extends Controller
{
    public function index(Event $event, Club $club){
        if (!$club->hasParticipation($event->id)){
            abort(403,'No es posible mostra la página solicitada.');
        }

        $breadcrumb = collect([
            route('home') => 'Inicio',
            route('events_list') => 'Eventos',
            route('event_detail', $event->id) => $event->name,
            'active' => 'Inscripción',

        ]);



        $participation = $club->participations()->with(['club', 'event', 'club.participants'])->where('event_id', $event->id)->first();
        $participant = $participation->club->participants()->where('event_id', $event->id)->first();
        $members_participate = collect($this->getAllMembers($participant->pivot->snapshot))->count();
        $members_no_participate = $club->members->count() - $members_participate;
        $total = $this->getRegistrations($event, $club)->get('total');


        return view('modules.participation.index',[
            'club' => $club,
            'event' => $event,
            'participation' => $participation,
            'members_participate' => $members_participate,
            'members_no_participate' => $members_no_participate,
            'total' => $total,
            'participation_status' => $participation->status,
            'breadcrumb' => $breadcrumb
        ]);
    }




    public function getUnits($snapshot){
        $units_list = [];
        $units = $this->__getUnits($snapshot);
        if ($units){
            foreach ($units as $unit){
                $units_list[] = $unit->id;
            }
        }
        return $units_list;
    }
    public function getMembers($snapshot){
        $members_list = [];
        $members = $this->__getMembers($snapshot);
        if ($members){
            foreach ($members as $member){
                $members_list[] = $member->id;
            }
        }
        return $members_list;
    }
    public function __getMembers($snapshot){
        $snapshot = \GuzzleHttp\json_decode($snapshot);
        if ($snapshot->members) {
            return $snapshot->members;
        }
        return [];
    }
    public function __getUnits($snapshot){
        $snapshot = \GuzzleHttp\json_decode($snapshot);
        if ($snapshot->units){
            return $snapshot->units;
        }
        return false;
    }
    public function getMembersFromUnits($units){
        $members = [];
        if ($units){
            foreach ($units as $unit){
                $unit_members = $unit->members;
                foreach ($unit_members as $member){
                    $members[] = $member;
                }
            }
        }
        return $members;
    }
    public function getAllMembers($snapshot){
        $members = $this->__getMembers($snapshot);
        $unit_members = $this->getMembersFromUnits($this->__getUnits($snapshot));
        return array_merge($members, $unit_members);
    }
    public function getRegistrations($event, $club){
        $participant = $event->participants()->where('eventable_id', $club->id)->where('eventable_type', 'App\Club')->whereNotNull('snapshot');
        $snapshot = $participant->first()->snapshot;
        $members = collect($this->getAllMembers($snapshot));

        $grouped = $members->mapToGroups(function ($item, $key) use ($event){
            $preference_registrations = $event->registrations()->preference();
            $positions = $item->positions;
            foreach ($positions as $position){
                $registration_position = $preference_registrations->where('position_id', $position->id)->first();
                if($registration_position){
                    return [$position->id => $item->name];
                }
            }
            return [0 => $item->name];
        });

        $grouped_with_price = collect();
        foreach ($event->registrations as $registration){
            foreach ($grouped as $key => $participants) {
                if ($registration->type == 2) {
                    if ($registration->position->id == $key) {
                        $grouped_with_price[$key] = collect([
                            'price' => $registration->price,
                            'count' => $participants->count(),
                            'subtotal' => ($registration->price * $participants->count()),
                            'description' => Position::find($key)->name
                        ]);
                    }
                } else {
                    $grouped_with_price[$key] = collect([
                        'price' => $registration->price,
                        'count' => $participants->count(),
                        'subtotal' => ($registration->price * $participants->count()),
                        'description' => 'General'
                    ]);
                }
            }
        }

        return collect([
            'total' => $grouped_with_price->sum('subtotal'),
            'items' => $grouped_with_price
        ]);
    }
}