<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>BIDROHI — Admin Access</title>

    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Noto+Sans+Bengali:wght@700&display=swap" rel="stylesheet"/>
    
    <link rel="shortcut icon" href="{{ asset('images/bidrohi-logo.png') }}"></link>
    <!-- External CSS -->
    <link rel="stylesheet" href="/CSS/admin-login.css"/>
</head>
<body>

    <div class="box">
        <div class="logo">
            <img src="/images/bidrohi-logo.png" alt="BIDROHI"
            style="width: 90px; height: 90px; object-fit: contain;"/>
            <div class="bn">বিদ্রোহী</div>
            <div class="sub">ADMIN ACCESS</div>
        </div>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            <div class="label">PASSWORD</div>
            <input type="password" name="password" placeholder="••••••••" required autofocus/>
            <button type="submit">ENTER →</button>
        </form>

        <div class="warn">
            This is a secured terminal-access panel.<br/>
            Unauthorized access attempts are logged.
        </div>
    </div>

</body>
</html>