@props([ // Props
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-danger cims-cancel-button']) }}>
    <i class="fa fa-times me-2"></i> Cancel
</button> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-cancel-button{
            padding: 10px 40px !important;
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
