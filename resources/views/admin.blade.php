<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BIDROHI Admin</title>
     <link rel="shortcut icon" href="{{ asset('images/bidrohi-logo.png') }}"></link>
    @viteReactRefresh
    @vite(['resources/js/admin/main.jsx'])
</head>
<body>
    <div id="admin-root"></div>
</body>
</html>