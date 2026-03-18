@props([ // Props
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-primary cims-update-button']) }}>
    <i class="fa fa-save me-2"></i> Update
</button> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-update-button{
            padding: 10px 40px !important;
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
