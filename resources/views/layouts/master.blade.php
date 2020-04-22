<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@lang('main.online_shop'): @yield('title')</title>

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/starter-template.css" rel="stylesheet">
    <link href="/css/custom-style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{route('index')}}">{{__('main.online_shop')}}</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li @routeactive('index')><a href="{{route('index')}}">{{__('main.all_products')}}</a></li>
                <li @routeactive('categor*')><a href="{{route('categories')}}">{{__('main.categories')}}</a></li>
                <li @routeactive('basket*')><a href="{{route('basket')}}">{{__('main.cart')}}</a></li>
                <li><a href="{{ route('locale', __('main.set_lang')) }}">{{__('main.set_lang')}}</a></li>
                {{--<li><a href="http://laravel-diplom-1.rdavydov.ru/reset">Сбросить проект в начальное состояние</a></li>--}}

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Currency <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @foreach( App\Services\CurrencyConversion::getCurrencies() as $currency )
                            <li><a href="{{ route('currency', $currency->code) }}">{{ $currency->symbol }}</a></li>
                        @endforeach
                    </ul>
                </li>

            </ul>

            <ul class="nav navbar-nav navbar-right">
                {{--<li><a href="http://laravel-diplom-1.rdavydov.ru/login">Панель администратора</a></li>--}}

                @guest
                    <li><a href="{{route('login')}}">{{__('main.sign_in')}}</a></li>
                @endguest


                @auth

                    @admin
                        <li><a href="{{ route('home') }}">Панель администратора</a></li>
                    @else
                        <li><a href="{{ route('person.orders.index') }}">Мои заказы</a></li>
                    @endadmin

                    <li><a href="{{route('get-logout')}}">Выйти</a></li>
                @endauth


            </ul>

        </div>
    </div>
</nav>

{{session('orderId')}}
<div class="container">
    @if(session()->has('success'))
        <p class="alert alert-success">{{session()->get('success')}}</p>
    @endif

    @if(session()->has('warning'))
        <p class="alert alert-warning">{{session()->get('warning')}}</p>
    @endif


    @yield('content')
</div>
</body>
</html>