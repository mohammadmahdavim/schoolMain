<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    protected $guarded = [];
    public function user(){
        return  $this->belongsTo(User::class);
    }

    public function CDisciplines(){
        return  $this->belongsTo(CDiscipline::class,'disciplines_id');
    }
}
