@if($reviews->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Product</th>
                    <th class="modern-table__th">Reviewer</th>
                    <th class="modern-table__th">Rating</th>
                    <th class="modern-table__th">Review</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Date</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($reviews as $review)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ $review->product->name ?? 'N/A' }}</strong>
                        </td>
                        <td class="modern-table__td">
                            <div>
                                <strong>{{ $review->reviewer_name ?? $review->name }}</strong>
                                @if($review->verified_purchase)
                                    <span class="badge bg-success ms-1" title="Verified Purchase">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @endif
                            </div>
                            @if($review->email)
                                <small class="text-muted">{{ $review->email }}</small>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <div class="rating-display">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ms-1">({{ $review->rating }}/5)</span>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="text-truncate" style="max-width: 300px;" title="{{ $review->review }}">
                                {{ \Illuminate\Support\Str::limit($review->review, 60) }}
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <form method="POST"
                                  action="{{ route('admin.reviews.updateStatus', $review->uuid) }}"
                                  class="status-form ajax-status-form"
                                  data-review-uuid="{{ $review->uuid }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select">
                                    @php
                                        $status = (int) $review->status;
                                    @endphp
                                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Approved</option>
                                    <option value="2" {{ $status == 2 ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">
                            {{ $review->created_at->format('M d, Y') }}
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.reviews.show', $review->uuid) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.reviews.destroy', $review->uuid) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <div class="empty-state__icon">
            <i class="fas fa-star"></i>
        </div>
        <h3 class="empty-state__title">No Reviews Found</h3>
    </div>
@endif
