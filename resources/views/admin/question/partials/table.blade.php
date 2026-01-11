@if($questions->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Product</th>
                    <th class="modern-table__th">Question</th>
                    <th class="modern-table__th">Asked By</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Answers</th>
                    <th class="modern-table__th">Date</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($questions as $question)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ $question->product->name ?? 'N/A' }}</strong>
                        </td>
                        <td class="modern-table__td">
                            <div class="text-truncate" style="max-width: 300px;" title="{{ $question->question }}">
                                {{ \Illuminate\Support\Str::limit($question->question, 60) }}
                            </div>
                        </td>
                        <td class="modern-table__td">{{ $question->reviewer_name ?? $question->name }}</td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.questions.updateStatus', $question) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-question-id="{{ $question->uuid }}">
                                    @php $status = (int) $question->status; @endphp
                                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Approved</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-info">{{ $question->answers->count() }}</span>
                        </td>
                        <td class="modern-table__td">
                            {{ $question->created_at->format('M d, Y') }}
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.questions.show', $question) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.questions.destroy', $question) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this question?')">
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
            <i class="fas fa-comments"></i>
        </div>
        <h3 class="empty-state__title">No Questions Found</h3>
    </div>
@endif
