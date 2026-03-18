@props([ // Props
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-danger cims-delete-button']) }}>
    <i class="fa fa-trash me-2"></i> Delete
</button> {{-- Html Element --}}

@push('styles') {{-- Styles --}}
    <style>
        .cims-delete-button{
            padding: 15px 40px !important;
            font-size: 30px !important;
            margin: 10px 10px !important;
            display: inline !important;
        }
    </style>
@endpush
