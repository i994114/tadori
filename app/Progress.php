<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Progress extends Model
{
    use SoftDeletes;
    
    protected $table = 'progresses';
    
    protected $fillable = [
        'challenge_id', 'sub_step_id'
    ];

    public function challenge() {
        return $this->belongsTo(Challenge::class);
    }

    public function subStep() {
        return $this->belongsTo(SubStep::class);
    }
}
