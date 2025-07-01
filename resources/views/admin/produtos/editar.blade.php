@extends('layouts.layout', ['page' => __('Produtos'), 'pageSlug' => 'product'])

@section('menu')

    @include('admin.menu')

@endsection

@section('content')
    <div class="container mt-4">
        <h4>Editar Produto</h4>
        @livewire('produto-editar', ['produto' => $produtoId])
    </div>
@endsection
