<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'description', 'start', 'end'];

    public function zones(){
        return $this->morphedByMany(Zone::class,'eventable');
    }

    public function fields(){
        return $this->morphedByMany(Field::class,'eventable');
    }

    public function clubs(){
        return $this->morphedByMany(Club::class,'eventable');
    }

    public function units($club_id = null){
        $query = $this->morphedByMany(Unit::class,'eventable');

        if (! is_null($club_id)){
            $query->where('units.club_id', $club_id);
        }
        return $query;
    }

    public function members($club_id = null, $position_ids = null){
        $query = $this->morphedByMany(Member::class,'eventable');

        if (! is_null($club_id)){
            $query->where('members.institutable_id', $club_id);
        }

        if (! is_null($position_ids)){
            $query->whereHas('positions',function($query) use ($position_ids){
                $query->whereIn('positions.id',$position_ids);
            });
        }

        return $query;
    }

    public function logs(){
        return $this->morphMany(Log::class, 'loggable');
    }

    public function activities(){
        return $this->hasMany(Activity::class);
    }

    public function enable(){
        $this->active = 1;
        $this->save();
    }

    public function disable(){
        $this->active = 0;
        $this->save();
    }

    public function toggle(){
        if ($this->active == 1){
            $this->disable();
        }else{
            $this->enable();
        }
        return $this->active;
    }


    public function scopeByZone($query, $zone_ids){
        return $query;
        return $query->orWhere(function ($query) use($zone_ids){
            $query
                ->wherein('eventable_id', $zone_ids)
                ->where('eventable_type', '\\App\\Zone');
        });
    }

    public function scopeByField($query, $field_id){
        return $query;
        return $query->orWhere([
            ['eventable_id','=', $field_id],
            ['eventable_type', '=','\\App\\Field']
        ]);
    }
}
