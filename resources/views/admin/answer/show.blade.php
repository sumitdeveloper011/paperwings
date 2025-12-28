@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-reply"></i>
                    Answer Details
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.answers.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Answer Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-row">
                        <strong>Question:</strong>
                        <div class="mt-2">{{ $answer->question->question ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <strong>Product:</strong>
                        <span>{{ $answer->question->product->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Answer:</strong>
                        <div class="mt-2">{{ $answer->answer }}</div>
                    </div>
                    <div class="info-row">
                        <strong>Answered By:</strong>
                        <span>{{ $answer->reviewer_name ?? $answer->name }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Status:</strong>
                        <span class="badge {{ $answer->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                            {{ $answer->status == 1 ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Helpful Count:</strong>
                        <span class="badge bg-info">{{ $answer->helpful_count }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Created:</strong>
                        <span>{{ $answer->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

