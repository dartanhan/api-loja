@extends('layouts.layout', ['page' => __('Produtos'), 'pageSlug' => 'product'])

@section('menu')

    @include('admin.menu')

@endsection

@section('content')
    <livewire:produtos-variacoes xmlns:livewire="http://www.w3.org/1999/xhtml"/>
@endsection
