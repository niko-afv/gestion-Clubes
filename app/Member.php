<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['name', 'dni', 'email', 'phone', 'birth_date', 'institutable_id','institutable_type'];

    public function positions(){
        return $this->belongsToMany(Position::class,'member_positions');
    }

    public function institutable(){
        return $this->morphTo();
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function scopeLoose($query){
        return $query->whereNull('unit_id');
    }

    public function age(){
        return Carbon::parse($this->birth_date)->age;
    }

    public function events(){
        return $this->morphToMany(Event::class, 'eventable');
    }

    public function participate(){
        return ($this->events()->where('event_id',9)->count())?true:false;
    }


    public function getName(){
        $name = explode(' ', $this->name);
        if (count($name) <=2){
            return $this->name;
        }elseif (count($name) >=3){
            return $name[0] . ' ' . $name[2];
        }
    }
}
