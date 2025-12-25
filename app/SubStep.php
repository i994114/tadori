<?php

namespace App;

use App\Step;
use App\Progress;
use App\Constants\DefaultStepImg;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubStep extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sub_step_name', 'sub_step_detail', 'sub_step_img', 'estimated_time',
    ];

    public function progresses() {
        return $this->hasMany(Progress::class, 'sub_step_id');
    }

    public function step() {
        return $this->belongsTo(Step::class);
    }

}
