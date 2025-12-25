<?php

namespace App;

use App\Step;
use App\Progress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'step_id',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function step() {
        return $this->belongsTo(Step::class)->withTrashed();
    }

    public function progresses() {
        return $this->hasMany(Progress::class);
    }
}
