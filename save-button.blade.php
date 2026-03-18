@props([ // Props
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-primary cims-save-button']) }}>
    <i class="fa fa-save me-2"></i> Save
</button> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-save-button{
            padding: 10px 40px !important;
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
