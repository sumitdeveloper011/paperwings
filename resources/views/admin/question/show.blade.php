@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-comment"></i>
                    Question Details
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Question Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-row">
                        <strong>Product:</strong>
                        <span>{{ $question->product->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Question:</strong>
                        <div class="mt-2">{{ $question->question }}</div>
                    </div>
                    <div class="info-row">
                        <strong>Asked By:</strong>
                        <span>{{ $question->reviewer_name ?? $question->name }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Email:</strong>
                        <span>{{ $question->email }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Status:</strong>
                        <span class="badge {{ $question->status == 1 ? 'bg-success' : 'bg-warning' }}">
                            {{ $question->status == 1 ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Created:</strong>
                        <span>{{ $question->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            @if($question->answers->count() > 0)
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Answers ({{ $question->answers->count() }})</h3>
                </div>
                <div class="modern-card__body">
                    @foreach($question->answers as $answer)
                        <div class="answer-item mb-3 p-3" style="background: #f8f9fa; border-radius: 5px;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $answer->reviewer_name ?? $answer->name }}</strong>
                                    <p class="mb-0 mt-1">{{ $answer->answer }}</p>
                                    <small class="text-muted">
                                        {{ $answer->created_at->format('M d, Y') }}
                                        @if($answer->helpful_count > 0)
                                            | {{ $answer->helpful_count }} helpful
                                        @endif
                                    </small>
                                </div>
                                <span class="badge {{ $answer->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $answer->status == 1 ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

