@props(['desktop', 'mobile'])

{{-- DESKTOP --}}
<div class="d-none d-md-flex gap-2">
    {{ $desktop }}
</div>

{{-- MOBILE --}}
<div class="d-block d-md-none mb-3 text-end">
    {{ $mobile }}
</div>
