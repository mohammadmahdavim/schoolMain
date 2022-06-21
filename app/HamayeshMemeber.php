<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HamayeshMemeber extends Model
{
    protected $guarded=[];
    public function hamayesh()
    {
        return $this->belongsTo(Hamayesh::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
