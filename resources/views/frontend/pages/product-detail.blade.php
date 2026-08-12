@extends('frontend.layouts.app')

@section('title', $product->name)

@section('content')
    <div class="min-h-screen bg-wood-bg pt-24 sm:pt-28">
        <livewire:frontend.product-detail :product="$product" />
    </div>
@endsection
