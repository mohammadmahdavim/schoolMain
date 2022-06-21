<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelectionOption extends Model
{
    protected  $table='selection_optionss';
    protected $guarded = [];

    public function selection()
    {
        return $this->belongsTo(Selection::class);
    }

    public function selectionitem()
    {
        return $this->belongsTo(SelectionItem::class);
    }
}
