<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
</head>
<body class="bg-light border-0">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">Dashboard</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="notifications" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="true" v-pre>
                                    Notifications <span class="caret"></span>
                                </a>
{{--                                <ul class="dropdown-menu" aria-labelledby="notificationsMenu" id="notificationsMenu">--}}
{{--                                    @php($notifications = auth()->user()->notifications()->limit(5)->get())--}}
{{--                                    @if(count($notifications) > 0)--}}
{{--                                        @foreach($notifications as $notification)--}}
{{--                                            <li class="dropdown-header border-bottom">--}}
{{--                                                <p>{{ $notification->data['message'] }}</p>--}}
{{--                                                <a href="{{ $notification->data['link'] }}"--}}
{{--                                                >{{ $notification->data['link'] }}</a>--}}
{{--                                                <form action="/notifications/{{ $notification->data['notification_id'] }}/delete"--}}
{{--                                                      method="post">--}}
{{--                                                    @method('delete')--}}
{{--                                                    <button class="btn btn-danger" type="submit">Delete</button>--}}
{{--                                                </form>--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    @endif--}}
{{--                                </ul>--}}
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ auth()->user()->name }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ url('/profile') }}">Profile</a>
                                    <a class="dropdown-item" href="{{ url('/notifications') }}">Notifications</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                    >{{ __('Logout') }}</a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container py-5">
            @yield('content')
        </main>
    </div>
    @auth
        <script>
            const userId = {{ auth()->user()->getAuthIdentifier() }};
            const JWTToken = '{{ auth()->user()->jwt_token }}';
        </script>
    @endauth
</body>
</html>
