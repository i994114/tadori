<p>{{ $admin_name }}さん</p>

<p>
    ホームページから問い合わせがありました。<br>
    詳細をご確認ください。<br>

    氏名：{{ $inquired_name }}<br>
    アドレス：{{ $email }}<br>
    内容：{{ $comment }}<br>
</p>

<p>////////////////////////////////////////</p>
<p>
    TADORIカスタマーセンター<br>
    URL: {{ env('APP_URL') }}<br>
    E-mail: contact@yk-lab.jp
</p>
<p>////////////////////////////////////////</p>
