<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TotalMark extends Model
{
    protected $table = 'total_marks';
    protected $guarded = [];

    public function dars(){
      return  $this->belongsTo(dars::class,'id');
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

}
