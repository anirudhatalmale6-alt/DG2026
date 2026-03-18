@props([ // Props
    'type' => '',
])

<a type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-pink cims-pint-button']) }}>
    {{ $slot }}
</a> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-pint-button{
            padding: 10px 40px !important;
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
