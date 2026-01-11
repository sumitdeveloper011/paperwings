{{-- Tags Select Component (Select2 Multi-select) - Reusable
     Usage: @include('components.select-tags', [
         'id' => 'tag_ids',
         'name' => 'tag_ids[]',
         'label' => 'Tags',
         'required' => false,
         'selected' => old('tag_ids', []),
         'tags' => $tags,
         'placeholder' => 'Select tags...',
         'class' => 'form-select select2-tags'
     ])
--}}
@php
    $selectId = $id ?? 'tag_ids';
    $selectName = $name ?? 'tag_ids[]';
    $labelText = $label ?? 'Tags';
    $isRequired = $required ?? false;
    $selectedValues = $selected ?? old('tag_ids', []);
    $tagsList = $tags ?? [];
    $placeholder = $placeholder ?? 'Select tags...';
    $selectClass = $class ?? 'form-select select2-tags';
    $helpText = $helpText ?? 'Select multiple tags for this product';
@endphp

<div class="mb-3">
    <label for="{{ $selectId }}" class="form-label">
        {{ $labelText }}
        @if($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select class="{{ $selectClass }} @error('tag_ids') is-invalid @enderror"
            id="{{ $selectId }}"
            name="{{ $selectName }}"
            multiple>
        @foreach($tagsList as $tag)
            <option value="{{ $tag->id }}" {{ in_array($tag->id, (array)$selectedValues) ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
        @endforeach
    </select>
    @if($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
    @error('tag_ids')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#{{ $selectId }}').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            width: '100%',
            tags: false, // Set to true if you want to allow creating new tags
            tokenSeparators: [',']
        });
    } else {
        setTimeout(function() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $('#{{ $selectId }}').select2({
                    theme: 'bootstrap-5',
                    placeholder: '{{ $placeholder }}',
                    allowClear: true,
                    width: '100%',
                    tags: false,
                    tokenSeparators: [',']
                });
            }
        }, 500);
    }
});
</script>
@endpush
