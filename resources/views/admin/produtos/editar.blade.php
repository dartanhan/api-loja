@extends('layouts.layout', ['page' => __('Produtos'), 'pageSlug' => 'product'])

@section('menu')

    @include('admin.menu')

@endsection

@section('content')
    <div class="container-fluid mt-4">
        @livewire('produto-editar', ['produto' => $produtoId])
    </div>
@endsection
