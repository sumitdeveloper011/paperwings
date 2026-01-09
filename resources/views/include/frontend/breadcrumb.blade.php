    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title ?? 'Page Title' }}</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">{{ $title ?? 'Page Title' }}</h1>
                    @if(isset($subtitle))
                    <p class="page-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
