@extends('layouts.master')


@section('title', 'Товар')

@section('content')
    <div class="starter-template">
        <h1>{{$product->name}}</h1>
        <h2>{{$product->category->name}}</h2>
        <p>Цена: <b>71990 руб.</b></p>
        <img src="{{Storage::url($product->image)}}" alt="">
        <p>{{$product->description}}</p>


        <form action="{{ route('basket-add', $product) }}" method="post">

            @if($product->isAvailable())
                <button type="submit" class="btn btn-primary" role="button">В корзину</button>
            @else
                <span>Нет в наличии</span>
            @endif

            @csrf
        </form>

    </div>
@endsection
