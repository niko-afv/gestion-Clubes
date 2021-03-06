<?php

namespace App;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model implements Jsonable
{
    protected $table = 'club_participations';
    protected $fillable = ['event_id', 'status'];

    public function club(){
        return $this->belongsTo(Club::class);
    }

    public function event(){
        return $this->belongsTo(Event::class);
    }

    public function invoice(){
        return $this->hasOne(Invoice::class);
    }

    public function isFinished(){
        return ($this->status == 3)?true:false;
    }

    public function isJustPaid(){
        return ($this->status >= 2)?true:false;
    }


    public function finish(){
        return tap($this)->update([
            'status' => 3
        ]);
    }

    public function unfinish(){
        return tap($this)->update([
            'status' => 2
        ]);
    }
}
