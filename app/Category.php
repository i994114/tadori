<?php

namespace App;

use App\Step;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_name',
    ];

    public function steps() {
        return $this->hasMany(Step::class)->withTrashed();
    }
}
