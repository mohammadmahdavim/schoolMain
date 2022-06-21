<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAnswer extends Model
{
    use SoftDeletes;
    protected $table='exam_answerss';
    protected $guarded = [];

    public function question()
    {
        $this->belongsTo(ExamQuestion::class);
    }

    public function option()
    {
        $this->belongsTo(Option::class);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
