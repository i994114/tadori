<?php

namespace App;

use App\Step;
use App\Favorite;
use App\Progress;
use App\Constants\DefaultIcon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPassword;
use App\Notifications\VerifyEmailJapanese;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 本登録メール送信用
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailJapanese);
    }

    //リセットパスワードメール送信用
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
    
    //Tymon-jwt関連
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    //JWTのペイロードになにか値を追加したい場合はここに追加
    public function getJWTCustomClaims()
    {
        return [];
    }

    //ログインユーザがお気に入りしたSTEP情報取得
    public function favorites() {
        return $this->belongsToMany(Step::class, 'favorites', 'user_id', 'step_id');
    }

    public function steps() {
        return $this->hasMany(Step::class, 'owner_id')->withTrashed();
    }

    //ユーザのもつチャレンジのSTEP情報
    public function challenges() {
        return $this->belongsToMany(Step::class, 'challenges', 'user_id', 'step_id')->where('challenges.deleted_at', null);
    }

    //ユーザのもつチャレンジ情報
    public function userChallenges()
    {
        return $this->hasMany(Challenge::class, 'user_id');
    }

    public function progresses()
    {
        return $this->hasManyThrough(
            Progress::class,     // 中間テーブルの先の最終モデル
            Challenge::class,    // 中間テーブル（リレーション経由元）
            'user_id',           // Challengeテーブル上のUser外部キー
            'challenge_id',      // Progressテーブル上のChallenge外部キー
            'id',                // Userテーブルの主キー
            'id'                 // Challengeテーブルの主キー
        );
    }

    // ユーザ名を取得する際に、退会していれば「退会済ユーザ」に変更
    public function getNameAttribute($value)
    {
        return $this->deleted_at? '退会済ユーザ' : $value;
    }

    //退会または自己紹介アイコンを載せていなければ、デフォルトアイコンを表示
    public function getUserImgAttribute($value)
    {  
        return  !$value || $this->deleted_at? DefaultIcon::DEFAULT_ICON_FILE : $value;
    }
}
