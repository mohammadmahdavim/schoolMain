<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trust extends Model
{
    protected $guarded = [];

    public function user(){
        return  $this->belongsTo(User::class);
    }
    public function library(){
        return  $this->belongsTo(Library::class,'library_id');
    }
}
