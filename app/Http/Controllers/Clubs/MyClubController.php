<?php

namespace App\Http\Controllers\Clubs;

use App\Events\AddedMemberEvent;
use App\Events\CreatedUnitEvent;
use App\Events\DeletedMemberEvent;
use App\Events\UpdatedMemberEvent;
use App\Events\UpdatedUnitEvent;
use App\Http\Requests\MyClubRequest;
use App\Imports\SGCMembersImport;
use App\Imports\SGCToMembersImport;
use App\Unit;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddMemberRequest;
use App\Http\Requests\AddUnitRequest;
use App\Member;
use App\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Matrix\Exception;
use Morrislaptop\Firestore\Firestore;

class MyClubController extends Controller
{

    public function index(MyClubRequest $request){
        $club = Auth::user()->member->institutable;
        return view('modules.clubes.detail',[
            'club' => $club
        ]);
    }

    public function showAddMember(MyClubRequest $request){
        $position = Position::all();
        return view('modules.clubes.member_form', [
            'positions' => $position
        ]);
    }

    public function saveMember(AddMemberRequest $request){

        $oMember = Member::create([
            'name' => $request->name,
            'birth_date' => Carbon::create($request->birthdate)->format('Y/m/d'),
            'email' => $request->email,
            'phone' => $request->phone,
            'dni' => $request->dni,
            'institutable_id' => $request->club_id,
            'institutable_type' => 'App\\Club',
            'active' => 1
        ]);

        event(new AddedMemberEvent($oMember));

        if ($request->has('positions')){
            foreach ($request->positions as $position_id){
                $oMember->positions()->save(Position::find($position_id));
            }
        }

        return redirect(route('my_club'));
    }

    public function showUpdateMember(MyClubRequest $request, Member $member){
        $newarray = [];
        foreach ($member->positions as $position){
            $newarray[] = $position->id;
        }

        $position = Position::all()->except($newarray)->all();
        return view('modules.clubes.member_form', [
            'positions' => $position,
            'member' => $member
        ]);
    }

    public function updateMember(AddMemberRequest $request, Member $member){
        $member->name = $request->name;
        $member->birth_date = Carbon::create($request->birthdate)->format('Y/m/d');
        $member->email = $request->email;
        $member->phone = $request->phone;
        $member->dni = $request->dni;
        $member->save();

        event(new UpdatedMemberEvent($member));

        if ($request->has('positions')){
            foreach ($request->positions as $position_id){
                $member->positions()->save(Position::find($position_id));
            }
        }

        return redirect(route('my_club'));
    }

    public function removePosition(MyClubRequest $request, Member $member){
        $member->positions()->detach([$request->position]);
        $response = $member->save();

        if ($response){
            $error = false;
            $message ='El cargo fue desviculado con éxito.';
        }else{
            $error = true;
            $message ='Ocurrió un problema al intentar desvincular el cargo del miembro.';
        }


        return response()->json([
            'error'=> $error,
            'message' => $message
        ]);
    }

    public function showAddUnit(MyClubRequest $request){
        $members = Auth::user()->member->institutable->members()->loose()->get();
        return view('modules.clubes.unit_form', [
            'members' => $members
        ]);
    }

    public function showUpdateUnit(MyClubRequest $request, Unit $unit){
        $members = Auth::user()->member->institutable->members()->loose()->unitable()->get();
        return view('modules.clubes.unit_form', [
            'members' => $members,
            'unit' => $unit
        ]);
    }

    public function saveUnit(AddUnitRequest $request){

        $oUnit = Unit::create([
            'name' => $request->name,
            'description' => $request->description,
            'club_id' => $request->club_id
        ]);

        event(new CreatedUnitEvent($oUnit));

        $oUnit->generateCode()->save();

        if ($request->has('members')){
            foreach ($request->members as $member_id){
                $oUnit->members()->save(Member::find($member_id));
            }
        }

        return redirect(route('my_club'));
    }

    public function updateUnit(AddUnitRequest $request, Unit $oUnit){
        $oUnit->name = $request->name;
        $oUnit->description = $request->description;
        $oUnit->save();

        event(new UpdatedUnitEvent($oUnit));

        if ($request->has('members')) {
            foreach ($request->members as $member_id) {
                $oUnit->members()->save(Member::find($member_id));
            }
        }

        return redirect(route('my_club'));
    }

    public function removeMember(MyClubRequest $request, Unit $unit){
        $oMember = Member::find($request->member)->unit()->dissociate();
        $response = $oMember->save();

        if ($response){
            $error = false;
            $message ='El miembro <strong>' . $oMember->name . '</strong> fue desviculado con éxito de la unidad.';
        }else{
            $error = true;
            $message ='Ocurrió un problema al intentar desvincular a <strong>' . $oMember->name . '</strong> de la unidad.';
        }


        return response()->json([
            'error'=> $error,
            'message' => $message
        ]);
    }


    public function deleteMember(MyClubRequest $request){
        $member = Member::find($request->member_id);
        $response = $member->delete();

        event(new DeletedMemberEvent($member));

        if ($response){
            $error = false;
            $message ='El miembro <strong>' . $member->name . '</strong> fue eliminado con éxito.';
        }else{
            $error = true;
            $message ='Ocurrió un problema al intentar eliminar a <strong>' . $member->name . '</strong> del sistema.';
        }


        return response()->json([
            'error'=> $error,
            'message' => $message
        ]);
    }



    public function showMemberImport(MyClubRequest $request){
        $members = Auth::user()->member->institutable->members()->get();
        return view('modules.clubes.member_import', [
            'members' => $members
        ]);
    }

    public function uploadMembers(MyClubRequest $request){
        $file = $request->file('file');

        $path = $file->storeAs('csv', Str::slug($file->getClientOriginalName()) . '.'. $file->getClientOriginalExtension());
        $file_path = storage_path('app/'.$path);

        $collection = Excel::toCollection(new SGCMembersImport(), $file_path);
        $imported = $collection->collapse();
        return response()->json([
            'error' => false,
            'data' => $imported,
            'file_path' => $file_path
        ]);
    }

    public function importMembers(MyClubRequest $request){
        $file_path = $request->file_path;
        try{
            Excel::import(new SGCToMembersImport(), $file_path);
        }catch (Exception $exception){
            return response()->json([
                'error' => true,
                'message' => 'Lo sentimos ha ocurrido un error inesperado al procesar el archivo, solicite ayuda.'
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => 'Los datos se han importado correctamente!'
        ]);

    }

    public function unitSync(Firestore $firestore, Unit $unit){
        $fsUnits = $firestore->collection('units');
        $newFsUnits = $fsUnits->document(Str::random(20));
        $snapshot = $newFsUnits->set([
            'databaseID' => $unit->id,
            'name' => $unit->name,
            'active' => true,
            'address' => '',
            'description' => $unit->description,
            'image' => $unit->image,
            'clubName' => $unit->club->name,
            'zoneName' => $unit->club->zone->name

        ])->snapshot();

        $data = $snapshot->data();
        return response()->json([
            'error' => false,
            'data' => $data,
            'message' => 'La <strong> unidad ' . $data['name']. '</strong> se ha sicnronizado con éxito.'
        ]);
    }
}