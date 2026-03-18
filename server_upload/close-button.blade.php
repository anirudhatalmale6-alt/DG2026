@props([ // Props
    'type' => 'button',
])

<a {{ $attributes->merge(['class' => 'button_master_close']) }}>
    <i class="fa-solid fa-circle-left"></i> Close
</a> {{-- Html Element --}}

{{-- CSS now managed centrally in cims_master_system_settings.css --}}
