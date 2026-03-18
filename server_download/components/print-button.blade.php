@props([ // Props
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-dark cims-print-button']) }}>
    <i class="fa fa-print me-2"></i> Print
</button> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-print-button{
            padding: 10px 40px !important;
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
