@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope-open"></i>
                    Contact Message Details
                </h1>
                <p class="page-header__subtitle">View and manage contact message</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Messages</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Message Details -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Message Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-user"></i> Name
                            </label>
                            <div class="info-value">{{ $contact->name }}</div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <div class="info-value">
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            </div>
                        </div>
                        @if($contact->phone)
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-phone"></i> Phone
                            </label>
                            <div class="info-value">
                                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                            </div>
                        </div>
                        @endif
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-tag"></i> Subject
                            </label>
                            <div class="info-value"><strong>{{ $contact->subject }}</strong></div>
                        </div>
                        <div class="info-item info-item--full">
                            <label class="info-label">
                                <i class="fas fa-comment-alt"></i> Message
                            </label>
                            <div class="info-value info-value--message">{{ nl2br(e($contact->message)) }}</div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-calendar"></i> Submitted
                            </label>
                            <div class="info-value">
                                {{ $contact->created_at->format('F d, Y') }}<br>
                                <small class="text-muted">{{ $contact->created_at->format('h:i A') }}</small>
                            </div>
                        </div>
                        @if($contact->admin_viewed_at)
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-eye"></i> Viewed
                            </label>
                            <div class="info-value">
                                {{ $contact->admin_viewed_at->format('F d, Y') }}<br>
                                <small class="text-muted">{{ $contact->admin_viewed_at->format('h:i A') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Management -->
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-cog"></i>
                        Manage Status
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.contacts.update', $contact) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-flag"></i> Status
                            </label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $contact->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $contact->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="solved" {{ $contact->status == 'solved' ? 'selected' : '' }}>Solved</option>
                                <option value="closed" {{ $contact->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="admin_notes" class="form-label">
                                <i class="fas fa-sticky-note"></i> Admin Notes
                            </label>
                            <textarea name="admin_notes" 
                                      id="admin_notes" 
                                      class="form-control" 
                                      rows="5" 
                                      placeholder="Add internal notes about this message...">{{ old('admin_notes', $contact->admin_notes) }}</textarea>
                            <small class="form-text text-muted">These notes are only visible to admins.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            @if($contact->admin_notes)
            <div class="modern-card mt-3">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-sticky-note"></i>
                        Current Notes
                    </h3>
                </div>
                <div class="modern-card__body">
                    <p>{{ nl2br(e($contact->admin_notes)) }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

