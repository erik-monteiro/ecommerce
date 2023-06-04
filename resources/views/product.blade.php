@extends('layouts.frontend')

@section('content')
    <div class="container mt-4 text-white">
        <h1 class="text-center">Produtos</h1>
        
        <div class="row">
            @foreach ($products as $product)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="{{ $product->image_name }}" class="card-img-top" alt="Imagem do Produto" height="320">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ $product->description }}</p>
                            <h6 class="card-subtitle mb-2 text-muted">R$ {{ $product->sale_price }}</h6>
                            <a href="{{ $product->slug }}" class="card-link">Ver Detalhes</a>
                            <add-to-cart-button product-id="{{ $product->id }}" user-id="{{ auth()->user()->id ?? 0}}"/>       
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
       
    </div>
@endsection

