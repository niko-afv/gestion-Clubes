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
        return $this->morphedByMany(Field::class,'eventable');
    }

    public function units(){
        return $this->morphedByMany(Unit::class,'eventable');
    }

    public function members(){
        return $this->morphedByMany(Member::class,'eventable');
    }

    public function logs(){
        return $this->morphMany(Log::class, 'loggable');
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
