<!--
    機能：パスワードリセットメール内のリンククリックで表示される画面
-->
@extends('layouts.app')

@section('content')
    <div class="c-container c-container--colored">

        <!-- ページタイトル -->
        <h2 class="c-title c-title--inner">パスワードリセット</h2>

        <div class="c-formGroup">
            以下のボタンでフォームを表示し、新しいパスワードを入力してください。
            
            <!-- SPAに遷移するためのリンク -->
            <div class="c-area c-area__resetEmailBtnArea">
                <a href="{{ url('/password-reset-form/'.$token) . '?email=' . urlencode($email) }}">
                    パスワードリセットを続行する
                </a>
            </div>
        </div>
    </div>
@endsection