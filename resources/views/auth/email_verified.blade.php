<!--
    機能：メール認証後に表示される画面
-->
@php
    use App\Constants\EmailVerificationStatus;
@endphp

@extends('layouts.app')

@section('content')
<div class="c-container c-container--colored">

    <!-- ページタイトル -->
    <h2 class="c-title c-title--inner">メール認証</h2>

    <div class="c-formGroup">
        <div class="c-information">
            @if($status === EmailVerificationStatus::VERIFIED)
                メール認証が完了しました。以下のボタンからマイページに進んでください。
            @elseif($status === EmailVerificationStatus::ALREADY_VERIFIED)
                すでに認証済みのメールアドレスです。以下のボタンからマイページに進んでください。
            @else
                認証に失敗しました。以下のボタンでログイン画面に遷移します。
            @endif
        </div>

        <!-- SPAに遷移するためのリンク（常に同じルートだが文言だけ変更） -->
        <div class="p-area p-area__resetEmailBtnArea">
            <a href="{{ url('/email-verified-redirect') . '?status=' . urlencode($status) }}">
                @if($status === EmailVerificationStatus::FAILED)
                    ログイン画面へ進む
                @else
                    マイページへ進む
                @endif
            </a>
        </div>
    </div>
</div>
@endsection
