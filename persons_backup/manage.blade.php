@extends('layouts.default')

@section('title', 'Persons')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* Contact card styling enhancements */
.contact-bx {
    border: 1px solid #17A2B8;
    transition: all 0.3s ease;
}

.contact-bx:hover {
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.25);
    transform: translateY(-3px);
}

.contact-bx .image-bx img {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.contact-bx .user-meta-info h5 a {
    color: #0d3d56 !important;
    font-weight: 600;
}

.contact-bx .user-meta-info p {
    color: #666;
    font-size: 13px;
}

.contact-bx .user-meta-info ul li a {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border-color: #17A2B8;
    color: #fff !important;
}

.contact-bx .user-meta-info ul li a i {
    color: #fff !important;
}

.contact-bx .user-meta-info ul li a:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: #fff !important;
}

.contact-bx .user-meta-info ul li a:hover i {
    color: #fff !important;
}

/* Search box styling */
.contacts-search {
    max-width: 400px;
}

.contacts-search .form-control {
    border: 2px solid #17A2B8;
    border-radius: 8px 0 0 8px;
}

.contacts-search .input-group-text {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: 2px solid #17A2B8;
    border-left: none;
    border-radius: 0 8px 8px 0;
}

.contacts-search .input-group-text a {
    color: #fff;
}

/* New Person button */
.btn-new-person {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: none;
    color: #fff;
    padding: 12px 25px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

.btn-new-person:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: #fff;
    transform: translateY(-2px);
}

/* Person ID badge */
.person-id-badge {
    font-size: 11px;
    color: #999;
    margin-top: 5px;
}

/* Status indicator */
.status-active {
    background: #28a745 !important;
}

.status-deceased {
    background: #dc3545 !important;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i {
    font-size: 60px;
    margin-bottom: 20px;
    color: #ddd;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">SmartDash</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#17A2B8" href="javascript:void(0)">Persons</a></li>
            </ol>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="input-group contacts-search mb-4">
            <input type="text" class="form-control" id="search_input" placeholder="Search by name, ID, email, phone...">
            <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
        </div>
        <div class="mb-4">
            <a href="{{ route('cimspersons.create') }}" class="btn btn-new-person">
                <i class="fa fa-plus"></i> New Person
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="row" id="persons_grid">
                <!-- Persons will be loaded here via AJAX -->
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p>Loading persons...</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" id="pagination_container" style="display:none;">
                <div class="text-muted">
                    Showing <span id="showing_from">0</span> - <span id="showing_to">0</span> of <span id="total_count">0</span>
                </div>
                <div>
                    <button class="btn btn-outline-secondary" id="prev_btn" disabled>
                        <i class="fa fa-chevron-left"></i> Previous
                    </button>
                    <button class="btn btn-outline-secondary ms-2" id="next_btn" disabled>
                        Next <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search_input');
    var personsGrid = document.getElementById('persons_grid');
    var paginationContainer = document.getElementById('pagination_container');
    var prevBtn = document.getElementById('prev_btn');
    var nextBtn = document.getElementById('next_btn');
    var showingFrom = document.getElementById('showing_from');
    var showingTo = document.getElementById('showing_to');
    var totalCount = document.getElementById('total_count');

    var currentOffset = 0;
    var limit = 12;
    var total = 0;
    var searchTimeout = null;

    // Default avatar
    var defaultAvatar = '/public/smartdash/images/user.jpg';

    function loadPersons(query, offset) {
        query = query || '';
        offset = offset || 0;

        var url = '{{ route("cimspersons.search") }}?q=' + encodeURIComponent(query) + '&limit=' + limit + '&offset=' + offset;

        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                total = data.total;
                currentOffset = offset;
                renderPersons(data.rows);
                updatePagination();
            })
            .catch(function(err) {
                personsGrid.innerHTML = '<div class="col-12"><div class="empty-state"><i class="fa fa-exclamation-triangle"></i><p>Error loading persons</p></div></div>';
            });
    }

    function renderPersons(persons) {
        if (!persons || persons.length === 0) {
            personsGrid.innerHTML = '<div class="col-12"><div class="empty-state"><i class="fa fa-user-slash"></i><p>No persons found</p><a href="{{ route("cimspersons.create") }}" class="btn btn-new-person mt-3"><i class="fa fa-plus"></i> Add First Person</a></div></div>';
            paginationContainer.style.display = 'none';
            return;
        }

        var html = '';
        persons.forEach(function(p) {
            var fullName = (p.title ? p.title + ' ' : '') + (p.firstname || '') + ' ' + (p.surname || '');
            var idNumber = p.identity_number || 'N/A';
            var mobile = p.mobile_phone || '';
            var email = p.email || '';
            var status = p.person_status || 'Active';
            var statusClass = status === 'Deceased' ? 'status-deceased' : 'status-active';
            var avatar = p.profile_picture ? '/storage/' + p.profile_picture : defaultAvatar;

            html += '<div class="col-xl-2 col-xxl-3 col-md-4 col-sm-6 items">';
            html += '  <div class="card contact-bx item-content">';
            html += '    <div class="card-header border-0">';
            html += '      <div class="action-dropdown">';
            html += '        <div class="dropdown">';
            html += '          <div class="btn-link" data-bs-toggle="dropdown">';
            html += '            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
            html += '              <circle cx="12.4999" cy="3.5" r="2.5" fill="#A5A5A5"/>';
            html += '              <circle cx="12.4999" cy="11.5" r="2.5" fill="#A5A5A5"/>';
            html += '              <circle cx="12.4999" cy="19.5" r="2.5" fill="#A5A5A5"/>';
            html += '            </svg>';
            html += '          </div>';
            html += '          <div class="dropdown-menu dropdown-menu-end">';
            html += '            <a class="dropdown-item" href="{{ route("cimspersons.edit", ":id") }}'.replace(':id', p.id) + '">Edit</a>';
            html += '            <a class="dropdown-item text-danger" href="#" onclick="deletePerson(' + p.id + ', \'' + escapeHtml(fullName).replace(/'/g, "\\'") + '\'); return false;">Delete</a>';
            html += '          </div>';
            html += '        </div>';
            html += '      </div>';
            html += '    </div>';
            html += '    <div class="card-body user-profile">';
            html += '      <div class="image-bx">';
            html += '        <img src="' + avatar + '" alt="" class="rounded-circle" onerror="this.src=\'' + defaultAvatar + '\'">';
            html += '        <span class="' + statusClass + '"></span>';
            html += '      </div>';
            html += '      <div class="media-body user-meta-info">';
            html += '        <h5 class="mb-0"><a href="{{ route("cimspersons.edit", ":id") }}'.replace(':id', p.id) + '" class="text-black user-name">' + escapeHtml(fullName) + '</a></h5>';
            html += '        <p class="mb-1">' + escapeHtml(idNumber) + '</p>';
            html += '        <p class="person-id-badge mb-2">' + (p.gender || '') + (p.ethnic_group ? ' • ' + p.ethnic_group : '') + '</p>';
            html += '        <ul>';
            if (mobile) {
                html += '          <li><a href="tel:' + mobile + '" title="' + escapeHtml(mobile) + '"><i class="fas fa-phone-alt"></i></a></li>';
            }
            if (email) {
                html += '          <li><a href="mailto:' + email + '" title="' + escapeHtml(email) + '"><i class="fas fa-envelope"></i></a></li>';
            }
            html += '          <li><a href="{{ route("cimspersons.edit", ":id") }}'.replace(':id', p.id) + '" title="View Details"><i class="fas fa-user"></i></a></li>';
            html += '        </ul>';
            html += '      </div>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        });

        personsGrid.innerHTML = html;
        paginationContainer.style.display = 'flex';
    }

    function updatePagination() {
        var from = total > 0 ? currentOffset + 1 : 0;
        var to = Math.min(currentOffset + limit, total);

        showingFrom.textContent = from;
        showingTo.textContent = to;
        totalCount.textContent = total;

        prevBtn.disabled = currentOffset === 0;
        nextBtn.disabled = currentOffset + limit >= total;
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Search input handler
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadPersons(searchInput.value, 0);
        }, 300);
    });

    // Pagination handlers
    prevBtn.addEventListener('click', function() {
        if (currentOffset > 0) {
            loadPersons(searchInput.value, currentOffset - limit);
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentOffset + limit < total) {
            loadPersons(searchInput.value, currentOffset + limit);
        }
    });

    // Delete function
    window.deletePerson = function(id, name) {
        Swal.fire({
            title: 'Delete Person?',
            text: 'Are you sure you want to remove "' + name + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete'
        }).then(function(result) {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("cimspersons.destroy", ":id") }}'.replace(':id', id);

                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrf);

                var method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);

                document.body.appendChild(form);
                form.submit();
            }
        });
    };

    // Initial load
    loadPersons('', 0);
});
</script>
@endpush
