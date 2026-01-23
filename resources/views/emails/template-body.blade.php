@include('emails.partials.header', [
    'logoUrl' => $logoUrl ?? url('assets/frontend/images/logo.png'),
    'headerSubtitle' => $headerSubtitle ?? null,
    'headerTitle' => $headerTitle ?? null,
])
{!! $body !!}
@include('emails.partials.footer', [
    'contactPhone' => $contactPhone ?? '+880 123 4567',
    'contactEmail' => $contactEmail ?? 'info@paperwings.co.nz',
    'socialLinks' => $socialLinks ?? [],
    'currentYear' => $currentYear ?? date('Y'),
    'appName' => $appName ?? config('app.name'),
])