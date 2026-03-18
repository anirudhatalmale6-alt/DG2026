@props([ // Props
    'type' => 'button',
])

<a {{ $attributes->merge(['class' => 'btn btn-pink cims-close-button']) }}>
    <i class="fa-solid fa-circle-left"></i> Close
</a> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-close-button{
            padding: 10px 40px !important;
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
