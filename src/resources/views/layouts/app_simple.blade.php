<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flea-market-app</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/app_simple.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
    @yield('css')
</head>
<body>
    <div class="app-simple">
        <div class="header-simple">
            <a href="/" class="header-logo">
                <img class="header-logo-img" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
            </a>
            <h1 class="header-logo_visually-hidden">
                COACHTECH
            </h1>
        </div>
        <div class="main-content">
            @yield('content')
        </div>
    </div>
</body>
</html>