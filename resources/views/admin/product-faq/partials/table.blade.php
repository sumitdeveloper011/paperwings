@if($faqs->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Product</th>
                    <th class="modern-table__th">Category</th>
                    <th class="modern-table__th">FAQs Count</th>
                    <th class="modern-table__th">Created</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($faqs as $faq)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ $faq->product->name ?? 'N/A' }}</strong>
                        </td>
                        <td class="modern-table__td">
                            {{ $faq->category->name ?? 'N/A' }}
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-primary">{{ count($faq->faqs ?? []) }} FAQ(s)</span>
                        </td>
                        <td class="modern-table__td">{{ $faq->created_at->format('M d, Y') }}</td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.product-faqs.show', $faq) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.product-faqs.edit', $faq) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.product-faqs.destroy', $faq) }}"
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
        <p class="empty-state__text">Start by creating your first product FAQ</p>
        <a href="{{ route('admin.product-faqs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add FAQ
        </a>
    </div>
@endif
