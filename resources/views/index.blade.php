@extends('layouts.master')

@section('title', 'Главная')

@section('content')



    <div class="starter-template">
        <h1>Всего товаров: {{ $allProductsCount }}</h1>

        <form method="GET" class="filters_form" action="{{route("index")}}">

            <div class="filters row">

                <div class="col-sm-6 col-md-3">
                    <label for="price_from"> Цена от
                        <input type="text" name="price_from" id="price_from" size="6" value="{{ request()->price_from }}">
                    </label>
                    <label for="price_to"> до
                        <input type="text" name="price_to" id="price_to" size="6" value="{{ request()->price_to }}">
                    </label>
                </div>

                <div class="col-sm-2 col-md-2">
                    <label for="hit">
                        <input type="checkbox" name="hit" id="hit" @if( request()->has('hit') ) checked @endif> {{ __('main.properties.hit') }}
                    </label>
                </div>

                <div class="col-sm-2 col-md-2">
                    <label for="new">
                        <input type="checkbox" name="new" id="new" @if( request()->has('new') ) checked @endif> {{ __('main.properties.new') }}
                    </label>
                </div>

                <div class="col-sm-2 col-md-2">
                    <label for="recommend">
                        <input type="checkbox" name="recommend" id="recommend" @if( request()->has('recommend') ) checked @endif> {{ __('main.properties.recommend') }}
                    </label>
                </div>

                <div class="col-sm-6 col-md-3">
                    <button type="submit" class="btn btn-primary">Фильтр</button>
                    <a href="{{ route("index") }}" class="btn btn-warning">Сброс</a>
                </div>

            </div>

        </form>

        <div class="row">

            @foreach($products as $product)

            @include('layouts.card' , ['product' => $product])

            @endforeach

        </div>

        {{$products->links()}}

    </div>

@endsection