@if($faqs->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Question</th>
                    <th class="modern-table__th">Answer</th>
                    <th class="modern-table__th">Category</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Sort Order</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($faqs as $faq)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ \Illuminate\Support\Str::limit($faq->question, 60) }}</strong>
                        </td>
                        <td class="modern-table__td">
                            <div class="text-truncate" style="max-width: 300px;" title="{{ strip_tags($faq->answer) }}">
                                {{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 50) }}
                            </div>
                        </td>
                        <td class="modern-table__td">
                            @if($faq->category)
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $faq->category)) }}</span>
                            @else
                                <span class="text-muted">General</span>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.faqs.updateStatus', $faq) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-faq-id="{{ $faq->uuid }}">
                                    @php
                                        $status = (int) $faq->status;
                                    @endphp
                                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">{{ $faq->sort_order ?? 0 }}</td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.faqs.show', $faq) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.faqs.edit', $faq) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.faqs.destroy', $faq) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this FAQ?')">
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
            <i class="fas fa-question-circle"></i>
        </div>
        <h3 class="empty-state__title">No FAQs Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No FAQs found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All FAQs
            </a>
        @else
            <p class="empty-state__text">Start by creating your first FAQ</p>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add FAQ
            </a>
        @endif
    </div>
@endif
