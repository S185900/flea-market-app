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
                    <!-- ログイン状態に応じてログイン/ログアウトを切り替え -->
                    @auth
                        <li class="header-navigation-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="header-navigation-link">ログアウト</button>
                            </form>
                        </li>
                    @endauth

                    @guest
                        <li class="header-navigation-item">
                            <a href="{{ route('login') }}" class="header-navigation-link">ログイン</a>
                        </li>
                    @endguest

                    <!-- ログイン前(ログイン状態に関係なく表示するリンク) -->
                    <li class="header-navigation-item">
                        <a href="{{ auth()->check() ? route('mypage.profile') : route('login') }}" class="header-navigation-link">マイページ</a>
                    </li>
                    <li class="header-navigation-item">
                        <a href="{{ auth()->check() ? route('sell') : route('login') }}" class="header-navigation-link header-navigation-button">出品</a>
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