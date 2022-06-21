<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelectionMember extends Model
{
    protected $guarded=[];

    public function selection()
    {
        return $this->belongsTo(Selection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
