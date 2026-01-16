@props([
    'src' => null,
    'alt' => 'Image',
    'class' => '',
    'fallback' => 'assets/images/placeholder.jpg',
    'loading' => null
])

<img src="{{ $src ? asset('storage/' . $src) : asset($fallback) }}" 
     alt="{{ $alt }}"
     class="{{ $class }}"
     @if($loading) loading="{{ $loading }}" @endif
     onerror="this.src='{{ asset($fallback) }}'">
