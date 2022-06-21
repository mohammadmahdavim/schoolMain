<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class student extends Model
{
    protected $guarded=[];

    public function claases()
    {
return $this->belongsTo(clas::class);
    }

    public function users()
    {
        return $this->hasOne(User::class);
    }
}
