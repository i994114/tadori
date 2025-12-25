<?php

namespace App;

use App\User;
use App\SubStep;
use App\Favorite;
use App\Challenge;
use App\Constants\DefaultStepImg;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'step_name', 'step_detail', 'step_img', 'total_estimated_time',
    ];

    protected $appends = ['progress_count'];

    public function subSteps()
     {
        return $this->hasMany(SubStep::class, 'step_id');
     }

     public function challenges()
     {
        return $this->hasMany(Challenge::class, 'step_id');
     }

    public function category() {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function favorites() {
        return $this->hasMany(Favorite::class, 'step_id');
    }

    //STEPのもつ子STEPの進捗率を算出
    public function getProgressCountAttribute()
    {
        $user = auth()->user();

        // ユーザ未ログイン時はゼロ返却
        if (!$user) {
            return ['total' => 0, 'cleared' => 0, 'rate' => 0];
        }
        
        //当該STEPのもつ子STEP数算出
        $total = $this->subSteps()->count();

        if ($total === 0) {
            return ['total' => 0, 'cleared' => 0 ];
        }

        //当該STEPがチャレンジをもつか
        $challenge = $this->challenges()->where('user_id', $user->id)->first();

        //抽出したチャレンジidをもつprogressの数を計算(これが当該STEPの子STEPクリア数)
        $cleared = $challenge? $challenge->progresses()->count() : 0;

        //進捗率
        $rate = round(($cleared / $total) * 100, 2);

        return ['total' => $total, 'cleared' => $cleared, 'rate' => $rate];

    }

}
