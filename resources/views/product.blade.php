@extends('layouts.master')


@section('title', 'Товар')

@section('content')
    <div class="starter-template">
        <h1>{{$product->name}}</h1>
        <h2>{{$product->category->name}}</h2>
        <p>Цена: <b>71990 руб.</b></p>
        <img src="{{Storage::url($product->image)}}" alt="">
        <p>{{$product->description}}</p>




        @if($product->isAvailable())
            <form action="{{ route('basket-add', $product) }}" method="post">
                <button type="submit" class="btn btn-primary" role="button">В корзину</button>
                @csrf
            </form>
        @else
            <span>Нет в наличии</span>
            <br>
            <br>
            <br>
            <span>Сообщить мне когда товар появистя в наличии</span>

            <div class="warning">
                @if($errors->get('email'))
                    {!! $errors->get('email')[0] !!}
                @endif
            </div>

            <form action="{{ route('subscription', $product) }}" method="post">
                <input type="text" name="email">
                <button type="submit">Отправить</button>
                @csrf
            </form>
        @endif


    </div>
@endsection
