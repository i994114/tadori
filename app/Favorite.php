<?php

namespace App;

use App\Step;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
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
}
