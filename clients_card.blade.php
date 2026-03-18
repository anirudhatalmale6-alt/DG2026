@php
    $createdDate = $c->created_at ? date('M jS, Y', strtotime($c->created_at)) : 'N/A';
@endphp
<div class="client-card {{ ($inactive ?? false) || !$c->is_active ? 'inactive' : '' }}"
     data-name="{{ strtolower($c->registered_company_name ?? '') }}"
     data-code="{{ strtolower($c->client_code ?? '') }}"
     data-reg="{{ strtolower($c->company_reg_number ?? '') }}"
     data-tax="{{ strtolower($c->income_tax_number ?? '') }}"
     data-vat="{{ strtolower($c->vat_number ?? '') }}">
    <div class="row align-items-center">
        <!-- Company Name -->
        <div class="col-xl-3 col-md-4 col-sm-12 mb-2 mb-xl-0">
            <div class="client-code">
                {{ $c->client_code ?: '#C-' . str_pad($c->id, 6, '0', STR_PAD_LEFT) }}
                @if(isset($showStatus) && $showStatus)
                    @if($c->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                @endif
            </div>
            <div class="client-name">{{ $c->registered_company_name ?: 'No name' }}</div>
            @if($c->trading_name && $c->trading_name != $c->registered_company_name)
                <div class="client-trading">t/a {{ $c->trading_name }}</div>
            @endif
            <div class="client-date"><i class="fa fa-calendar"></i>{{ $createdDate }}</div>
        </div>

        <!-- Registration Number -->
        <div class="col-xl-2 col-md-2 col-sm-6 mb-2 mb-xl-0">
            <div class="info-block">
                <div class="info-icon reg"><i class="fa fa-registered"></i></div>
                <div>
                    <div class="info-label">Reg Number</div>
                    <div class="info-value">{{ $c->company_reg_number ?: '-' }}</div>
                    @if($c->company_type_name)
                        <div class="info-sub">{{ $c->company_type_name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tax Number -->
        <div class="col-xl-2 col-md-2 col-sm-6 mb-2 mb-xl-0">
            <div class="info-block">
                <div class="info-icon tax"><i class="fa fa-file-invoice"></i></div>
                <div>
                    <div class="info-label">Tax Number</div>
                    <div class="info-value">{{ $c->income_tax_number ?: '-' }}</div>
                </div>
            </div>
        </div>

        <!-- VAT Number -->
        <div class="col-xl-2 col-md-2 col-sm-6 mb-2 mb-xl-0">
            <div class="info-block">
                <div class="info-icon vat"><i class="fa fa-percent"></i></div>
                <div>
                    <div class="info-label">VAT Number</div>
                    <div class="info-value">{{ $c->vat_number ?: '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="col-xl-2 col-md-2 col-sm-6 mb-2 mb-xl-0">
            <div class="info-block">
                <div class="info-icon contact"><i class="fa fa-phone"></i></div>
                <div>
                    <div class="info-label">Contact</div>
                    <div class="info-value">{{ $c->contact_person ?: '-' }}</div>
                    @if($c->contact_phone)
                        <div class="info-sub">{{ $c->contact_phone }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-xl-1 col-md-1 col-sm-6 text-end">
            <div class="dropdown action-dropdown">
                <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('cimsclients.show', $c->id) }}"><i class="fa fa-eye me-2 text-info"></i>View</a></li>
                    <li><a class="dropdown-item" href="{{ route('cimsclients.edit', $c->id) }}"><i class="fa fa-edit me-2 text-primary"></i>Edit</a></li>
                    <li><a class="dropdown-item" href="{{ route('cimsclients.toggle.get', $c->id) }}"><i class="fa {{ $c->is_active ? 'fa-ban text-warning' : 'fa-check text-success' }} me-2"></i>{{ $c->is_active ? 'Deactivate' : 'Activate' }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDelete({{ $c->id }})"><i class="fa fa-trash me-2"></i>Delete</a></li>
                </ul>
            </div>
        </div>
    </div>
    <form id="delete-form-{{ $c->id }}" action="{{ route('cimsclients.destroy', $c->id) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
</div>
