<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'description', 'club_id'];

    public function club(){
        return $this->belongsTo(Club::class);
    }

    public function members(){
        return $this->hasMany(Member::class);
    }

    public function events(){
        return $this->morphToMany(Event::class, 'eventable','participants');
    }

    public function activeEvents(){
        return $this->events()->where('active',1);
    }

    public function generateCode(){
        if(! is_null($this->code)){
            return $this;
        }
        do{
            $code  = '';
            $code .= strtoupper(substr($this->club->name, '0','2'));
            $code .= rand(10,99);
            $code .= strtoupper(substr($this->club->zone->name,0,2));
            var_dump($code);
        }while (\App\Unit::where('code',$code)->count() > 0);

        $this->code = $code;
        return $this;
    }

    public function participate($event_id){
        return ($this->events()->where('event_id',$event_id)->count())?true:false;
    }

    public function isLocked(){
        return ($this->activeEvents()->count() > 0)?true:false;
    }
}
