@extends('layouts.frontend.main')
@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">{{ $page->title }}</h1>
                    @if($page->sub_title)
                        <p class="page-subtitle">{{ $page->sub_title }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="page-content-wrapper">
                        @if($page->image)
                            <div class="page-image mb-4">
                                <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->title }}" class="img-fluid">
                            </div>
                        @endif
                        
                        <div class="page-content">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

