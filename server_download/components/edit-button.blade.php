@props([ // Props
    'type' => 'button',
])

<a type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-warning cims-edit-button']) }}>
    <i class="fa fa-edit me-2"></i> Edit
</a> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-edit-button{
            /* padding: 10px 40px !important; */
            font-size: 24px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
