@extends('layouts.layout')

@section('menu')

    @include('admin.menu')

@endsection

@section('content')

    <div class="container-fluid bg-dark">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" style="height: 632px"
                         src="{{URL::asset('/img/slide/image_slide_one.png')}}"
                         alt="First slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" style="height: 632px"
                         src="{{URL::asset('/img/slide/image_slide_two.png')}}"
                         alt="Second slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" style="height: 632px"
                         src="{{URL::asset('/img/slide/image_slide_tree.png')}}"
                         alt="Third slide">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" style="height: 632px"
                         src="{{URL::asset('/img/slide/image_slide_four.png')}}"
                         alt="Four slide">
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
@endsection
