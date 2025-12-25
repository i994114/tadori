<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'STEP') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <!-- ヘッダー -->
        <header-component></header-component>

        <!-- メイン -->
        <main id="main" :class="{ 'l-main': $route.name !== 'welcome' }">
            @yield('content')
        </main>

        <!-- フッター -->
        <footer-component></footer-component>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const main = document.getElementById('main');

        //ページ判定して l-main を付け外しする関数
        function updateMainClass() {
            if (window.location.pathname === '/') {
                main.classList.remove('l-main'); //トップページはl-mainのmarginなし(hero画像を画面いっぱいに出したいため)
            } else {
                main.classList.add('l-main'); //それ以外は l-main
            }
        }

        // 初回ロード時
        updateMainClass();

        //SPAやブラウザ履歴操作（戻る／進む）時にも更新する
        //(popstate イベントはブラウザ履歴の変更時に発火)
        window.addEventListener('popstate', updateMainClass);
    });
    </script>
</body>
</html>
