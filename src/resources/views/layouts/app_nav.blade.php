<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>flea-market-app</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/app_nav.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
    @yield('css')
</head>
<body>
    <div class="app-nav">
        <div class="header-nav">
            <a href="/" class="header-logo">
                <img class="header-logo-img" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
            </a>
            <h1 class="header-logo_visually-hidden">
                COACHTECH
            </h1>
            <label class="header-search-label visually-hidden" for="header-search">検索</label>
            <input class="header-search" type="text" id="header-search" placeholder="なにをお探しですか？">
            <nav class="header-navigation">
                <ul class="header-navigation-list">
                    <li class="header-navigation-item">
                        <a href="#" class="header-navigation-link">ログアウト</a>
                    </li>
                    <li class="header-navigation-item">
                        <a href="#" class="header-navigation-link">マイページ</a>
                    </li>
                    <li class="header-navigation-item">
                        <a href="#" class="header-navigation-link  header-navigation-button">出品</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            @yield('content')
        </div>
    </div>
</body>
</html>