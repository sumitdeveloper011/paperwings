@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => $page->title,
        'subtitle' => $page->sub_title ?? null,
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $page->title, 'url' => null]
        ]
    ])

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

