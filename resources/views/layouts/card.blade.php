<div class="col-sm-6 col-md-4">

    <div class="labels">
    @if($product->isNew())
        <span class="badge badge-success">{{ __('main.properties.new') }}</span>
    @endif

    @if($product->isRecommend())
        <span class="badge badge-warning">{{ __('main.properties.recommend') }}</span>
    @endif

    @if($product->isHit())
        <span class="badge badge-danger">{{ __('main.properties.hit') }}</span>
    @endif
</div>

    <div class="thumbnail">
        <img src="{{Storage::url($product->image)}}" alt="">
        <div class="caption">

            <h3>{{$product->name}}</h3>

            <p>{{$product->price}} руб.</p>

            {{--<p>{{$product->getCategory()->name}}</p>--}}
            <p>{{isset($category) ? $category->name : $product->category->name}}</p>


            <form action="{{ route('basket-add', $product) }}" method="post">

                @if($product->isAvailable())
                    <button type="submit" class="btn btn-primary" role="button">В корзину</button>
                @else
                    <span>Нет в наличии</span>
                @endif

                <a href="{{ route('product', [isset($category) ? $category->code : $product->category->code, $product->code]) }}" class="btn btn-default" role="button">Подробнее</a>
                @csrf
            </form>

        </div>
    </div>
</div>