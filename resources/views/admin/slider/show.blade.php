@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">{{ $slider->heading }}</h1>
                <p class="content-subtitle">Slider Details</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Slider
                    </a>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sliders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <!-- Left Column - Slider Details -->
            <div class="col-lg-8">
                <!-- Slider Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image me-2"></i>Slider Image
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="position-relative">
                            <img src="{{ $slider->image_url }}" alt="{{ $slider->heading }}" 
                                 class="img-fluid w-100" style="max-height: 400px; object-fit: cover;">
                            
                            <!-- Overlay with content preview -->
                            <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                                <div class="bg-dark bg-opacity-50 rounded p-4 mx-3">
                                    <h2 class="text-white mb-2">{{ $slider->heading }}</h2>
                                    @if($slider->sub_heading)
                                        <p class="text-white-50 mb-3">{{ $slider->sub_heading }}</p>
                                    @endif
                                    
                                    @if($slider->has_buttons)
                                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                                            @foreach($slider->buttons as $index => $button)
                                                <a href="{{ $button['url'] }}" class="btn {{ $index === 0 ? 'btn-primary' : 'btn-outline-light' }}" target="_blank">
                                                    {{ $button['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Slider Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Heading:</strong>
                                <p>{{ $slider->heading }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Sub Heading:</strong>
                                <p>{{ $slider->sub_heading ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <p>{!! $slider->status_badge !!}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Sort Order:</strong>
                                <p><span class="badge bg-secondary">{{ $slider->sort_order }}</span></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Created:</strong>
                                <p>{{ $slider->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Updated:</strong>
                                <p>{{ $slider->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button Information -->
                @if($slider->has_buttons)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-mouse-pointer me-2"></i>Buttons ({{ $slider->button_count }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($slider->buttons as $index => $button)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="mb-3">
                                            <i class="fas fa-{{ $index + 1 }} me-2"></i>Button {{ $index + 1 }}
                                        </h6>
                                        <div class="mb-2">
                                            <strong>Name:</strong>
                                            <span class="badge bg-info">{{ $button['name'] }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>URL:</strong>
                                            <a href="{{ $button['url'] }}" target="_blank" class="text-decoration-none">
                                                {{ $button['url'] }} <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <a href="{{ $button['url'] }}" target="_blank" 
                                               class="btn btn-sm {{ $index === 0 ? 'btn-primary' : 'btn-outline-secondary' }}">
                                                Test Button
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Actions & Metadata -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Slider
                            </a>
                            
                            <form action="{{ route('admin.sliders.updateStatus', $slider) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $slider->status === 'active' ? 'inactive' : 'active' }}">
                                <button type="submit" class="btn btn-outline-{{ $slider->status === 'active' ? 'warning' : 'success' }} w-100">
                                    <i class="fas fa-{{ $slider->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                                    {{ $slider->status === 'active' ? 'Deactivate' : 'Activate' }} Slider
                                </button>
                            </form>

                            <div class="row">
                                <div class="col-6">
                                    <form action="{{ route('admin.sliders.moveUp', $slider) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-arrow-up me-2"></i>Move Up
                                        </button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form action="{{ route('admin.sliders.moveDown', $slider) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-warning w-100">
                                            <i class="fas fa-arrow-down me-2"></i>Move Down
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <form action="{{ route('admin.sliders.duplicate', $slider) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-info w-100">
                                    <i class="fas fa-copy me-2"></i>Duplicate Slider
                                </button>
                            </form>

                            <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this slider? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Delete Slider
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Slider Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Slider Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-0">{{ $slider->button_count }}</h4>
                                    <small class="text-muted">Buttons</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info mb-0">{{ $slider->sort_order }}</h4>
                                <small class="text-muted">Order</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="small text-muted">
                            <div class="d-flex justify-content-between">
                                <span>Created:</span>
                                <span>{{ $slider->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Updated:</span>
                                <span>{{ $slider->updated_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>UUID:</span>
                                <span><code class="small">{{ Str::limit($slider->uuid, 8) }}...</code></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Details -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-code me-2"></i>Technical Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="mb-2">
                                <strong>Image Path:</strong>
                                <br>
                                <code class="small">{{ $slider->image }}</code>
                            </div>
                            
                            @if($slider->has_buttons)
                                <div class="mb-2">
                                    <strong>Button Data:</strong>
                                    <br>
                                    <pre class="small text-muted mb-0"><code>{{ json_encode($slider->buttons, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
