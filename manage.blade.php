@extends('layouts.default')

@section('title', 'Persons')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ============================================
   PERSONS LISTING — PREMIUM RESTYLED
   SmartWeigh Brand: #009688 / #4DB6AC / #00796B
   ============================================ */

/* ---------- Hero Banner ---------- */
.persons-hero {
    background: linear-gradient(135deg, #004D40 0%, #009688 50%, #4DB6AC 100%);
    border-radius: 16px;
    padding: 32px 40px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 150, 136, 0.3);
}

.persons-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.persons-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    border-radius: 50%;
}

.persons-hero h2 {
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.persons-hero p {
    color: rgba(255,255,255,0.85);
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    margin: 6px 0 0;
    position: relative;
    z-index: 1;
}

/* ---------- Stats Chips ---------- */
.stats-row {
    display: flex;
    gap: 16px;
    margin-top: 18px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}

.stat-chip {
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 12px;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.stat-chip:hover {
    background: rgba(255,255,255,0.28);
    transform: translateY(-2px);
}

.stat-chip i {
    color: #fff;
    font-size: 18px;
}

.stat-chip .stat-value {
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 700;
    line-height: 1;
}

.stat-chip .stat-label {
    color: rgba(255,255,255,0.8);
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ---------- Search & Action Bar ---------- */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 450px;
}

.search-box input {
    width: 100%;
    padding: 14px 20px 14px 48px;
    border: 2px solid #e0e0e0;
    border-radius: 14px;
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    font-weight: 400;
    color: #333;
    background: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.search-box input:focus {
    outline: none;
    border-color: #009688;
    box-shadow: 0 0 0 4px rgba(0, 150, 136, 0.12), 0 4px 16px rgba(0,0,0,0.06);
}

.search-box input::placeholder {
    color: #aaa;
}

.search-box .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #009688;
    font-size: 16px;
}

.btn-add-person {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #009688 0%, #00796B 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0, 150, 136, 0.35);
    transition: all 0.3s ease;
}

.btn-add-person:hover {
    background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 150, 136, 0.45);
}

.btn-add-person i {
    font-size: 14px;
    background: rgba(255,255,255,0.2);
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ---------- Person Cards Grid ---------- */
.person-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8e8e8;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative;
    cursor: pointer;
    height: 100%;
}

.person-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0, 150, 136, 0.2);
    border-color: #4DB6AC;
}

.person-card .card-accent {
    height: 4px;
    background: linear-gradient(to right, #009688, #4DB6AC);
    transition: height 0.3s ease;
}

.person-card:hover .card-accent {
    height: 6px;
}

.person-card .card-inner {
    padding: 20px;
    text-align: center;
}

.person-card .avatar-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 14px;
}

.person-card .avatar-wrap img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #4DB6AC;
    box-shadow: 0 4px 12px rgba(0, 150, 136, 0.2);
    transition: all 0.3s ease;
}

.person-card:hover .avatar-wrap img {
    border-color: #009688;
    box-shadow: 0 6px 20px rgba(0, 150, 136, 0.35);
    transform: scale(1.05);
}

.person-card .status-dot {
    position: absolute;
    bottom: 4px;
    right: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

.person-card .status-dot.alive { background: #28a745; }
.person-card .status-dot.deceased { background: #dc3545; }

.person-card .person-name {
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 600;
    color: #004D40;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.person-card .person-name a {
    color: inherit;
    text-decoration: none;
}

.person-card .person-name a:hover {
    color: #009688;
}

.person-card .person-id {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    color: #777;
    margin: 0 0 4px;
    font-weight: 500;
    letter-spacing: 0.3px;
}

.person-card .person-meta {
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    color: #aaa;
    margin: 0 0 14px;
}

/* Action Buttons Row */
.person-card .action-btns {
    display: flex;
    justify-content: center;
    gap: 8px;
    padding-top: 14px;
    border-top: 1px solid #f0f0f0;
}

.person-card .action-btns .act-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.act-btn.btn-call { background: linear-gradient(135deg, #009688, #00796B); }
.act-btn.btn-call:hover { background: linear-gradient(135deg, #00796B, #004D40); transform: scale(1.1); }

.act-btn.btn-email { background: linear-gradient(135deg, #FF5722, #E64A19); }
.act-btn.btn-email:hover { background: linear-gradient(135deg, #E64A19, #BF360C); transform: scale(1.1); }

.act-btn.btn-view { background: linear-gradient(135deg, #4DB6AC, #009688); }
.act-btn.btn-view:hover { background: linear-gradient(135deg, #009688, #00796B); transform: scale(1.1); }

.act-btn.btn-delete { background: linear-gradient(135deg, #ef5350, #d32f2f); }
.act-btn.btn-delete:hover { background: linear-gradient(135deg, #d32f2f, #b71c1c); transform: scale(1.1); }

/* Dropdown override */
.person-card .card-menu {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 2;
}

.person-card .card-menu .menu-dots {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.person-card:hover .card-menu .menu-dots {
    opacity: 1;
}

.person-card .card-menu .dropdown-menu {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    padding: 6px;
    min-width: 140px;
}

.person-card .card-menu .dropdown-item {
    border-radius: 6px;
    padding: 8px 14px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.person-card .card-menu .dropdown-item:hover {
    background: #E0F2F1;
    color: #004D40;
}

.person-card .card-menu .dropdown-item.text-danger:hover {
    background: #FFEBEE;
    color: #C62828;
}

/* ---------- Empty State ---------- */
.empty-state-box {
    text-align: center;
    padding: 80px 30px;
    background: #fff;
    border-radius: 16px;
    border: 2px dashed #4DB6AC;
}

.empty-state-box .empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #E0F2F1, #B2DFDB);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.empty-state-box .empty-icon i {
    font-size: 34px;
    color: #009688;
}

.empty-state-box h5 {
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #004D40;
    margin: 0 0 8px;
}

.empty-state-box p {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #999;
    margin: 0 0 20px;
}

/* ---------- Pagination ---------- */
.pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding: 16px 24px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e8e8e8;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.pagination-bar .page-info {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #777;
    font-weight: 500;
}

.pagination-bar .page-info strong {
    color: #004D40;
}

.pagination-bar .page-btns {
    display: flex;
    gap: 8px;
}

.pagination-bar .page-btn {
    padding: 10px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: #fff;
    color: #555;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.pagination-bar .page-btn:hover:not(:disabled) {
    border-color: #009688;
    color: #009688;
    background: #E0F2F1;
}

.pagination-bar .page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* ---------- Animations ---------- */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.person-card-col {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.person-card-col:nth-child(1) { animation-delay: 0.05s; }
.person-card-col:nth-child(2) { animation-delay: 0.1s; }
.person-card-col:nth-child(3) { animation-delay: 0.15s; }
.person-card-col:nth-child(4) { animation-delay: 0.2s; }
.person-card-col:nth-child(5) { animation-delay: 0.25s; }
.person-card-col:nth-child(6) { animation-delay: 0.3s; }
.person-card-col:nth-child(7) { animation-delay: 0.35s; }
.person-card-col:nth-child(8) { animation-delay: 0.4s; }
.person-card-col:nth-child(9) { animation-delay: 0.45s; }
.person-card-col:nth-child(10) { animation-delay: 0.5s; }
.person-card-col:nth-child(11) { animation-delay: 0.55s; }
.person-card-col:nth-child(12) { animation-delay: 0.6s; }

/* Loading spinner */
.loading-state {
    text-align: center;
    padding: 80px 20px;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #E0F2F1;
    border-top-color: #009688;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-state p {
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    color: #999;
    font-weight: 500;
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
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#009688" href="javascript:void(0)">Persons</a></li>
            </ol>
        </div>
    </div>

    <!-- Hero Banner -->
    <div class="persons-hero">
        <h2><i class="fa fa-users" style="margin-right:10px;"></i> People Directory</h2>
        <p>Manage all persons, contacts and stakeholders in one place</p>
        <div class="stats-row">
            <div class="stat-chip">
                <i class="fa fa-users"></i>
                <div>
                    <div class="stat-value" id="stat_total">—</div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
            <div class="stat-chip">
                <i class="fa fa-check-circle"></i>
                <div>
                    <div class="stat-value" id="stat_active">—</div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Add -->
    <div class="action-bar">
        <div class="search-box">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="search_input" placeholder="Search by name, ID number, email or phone...">
        </div>
        <a href="{{ route('cimspersons.create') }}" class="btn-add-person">
            <i class="fa fa-plus"></i> New Person
        </a>
    </div>

    <!-- Persons Grid -->
    <div class="row g-4" id="persons_grid">
        <div class="col-12">
            <div class="loading-state">
                <div class="loading-spinner"></div>
                <p>Loading persons...</p>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar" id="pagination_container" style="display:none;">
        <div class="page-info">
            Showing <strong><span id="showing_from">0</span></strong> – <strong><span id="showing_to">0</span></strong> of <strong><span id="total_count">0</span></strong> persons
        </div>
        <div class="page-btns">
            <button class="page-btn" id="prev_btn" disabled>
                <i class="fa fa-chevron-left"></i> Previous
            </button>
            <button class="page-btn" id="next_btn" disabled>
                Next <i class="fa fa-chevron-right"></i>
            </button>
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
    var statTotal = document.getElementById('stat_total');
    var statActive = document.getElementById('stat_active');

    var currentOffset = 0;
    var limit = 12;
    var total = 0;
    var searchTimeout = null;
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

                // Update stats
                statTotal.textContent = total;
                var activeCount = 0;
                if (data.rows) {
                    data.rows.forEach(function(p) {
                        if ((p.person_status || 'Alive') !== 'Deceased') activeCount++;
                    });
                }
                statActive.textContent = query ? activeCount : total;
            })
            .catch(function(err) {
                personsGrid.innerHTML = '<div class="col-12"><div class="empty-state-box"><div class="empty-icon"><i class="fa fa-exclamation-triangle"></i></div><h5>Error Loading Persons</h5><p>Something went wrong. Please try again.</p></div></div>';
            });
    }

    function renderPersons(persons) {
        if (!persons || persons.length === 0) {
            personsGrid.innerHTML = '<div class="col-12"><div class="empty-state-box"><div class="empty-icon"><i class="fa fa-user-slash"></i></div><h5>No Persons Found</h5><p>Get started by adding your first person</p><a href="{{ route("cimspersons.create") }}" class="btn-add-person"><i class="fa fa-plus"></i> Add First Person</a></div></div>';
            paginationContainer.style.display = 'none';
            return;
        }

        var html = '';
        persons.forEach(function(p, idx) {
            var fullName = (p.title ? p.title + ' ' : '') + (p.firstname || '') + ' ' + (p.surname || '');
            var idNumber = p.identity_number || 'N/A';
            var mobile = p.mobile_phone || '';
            var email = p.email || '';
            var status = p.person_status || 'Alive';
            var statusClass = status === 'Deceased' ? 'deceased' : 'alive';
            var avatar = p.profile_picture ? '/storage/' + p.profile_picture : defaultAvatar;
            var editUrl = '{{ route("cimspersons.edit", ":id") }}'.replace(':id', p.id);

            html += '<div class="col-xl-2 col-xxl-3 col-lg-3 col-md-4 col-sm-6 person-card-col">';
            html += '  <div class="person-card">';
            html += '    <div class="card-accent"></div>';

            // Dropdown menu
            html += '    <div class="card-menu">';
            html += '      <div class="dropdown">';
            html += '        <button class="menu-dots" data-bs-toggle="dropdown">';
            html += '          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="5" r="2" fill="#999"/><circle cx="12" cy="12" r="2" fill="#999"/><circle cx="12" cy="19" r="2" fill="#999"/></svg>';
            html += '        </button>';
            html += '        <div class="dropdown-menu dropdown-menu-end">';
            html += '          <a class="dropdown-item" href="' + editUrl + '"><i class="fa fa-pen me-2" style="color:#009688;"></i> Edit</a>';
            html += '          <a class="dropdown-item text-danger" href="#" onclick="deletePerson(' + p.id + ', \'' + escapeHtml(fullName).replace(/'/g, "\\'") + '\'); return false;"><i class="fa fa-trash me-2"></i> Delete</a>';
            html += '        </div>';
            html += '      </div>';
            html += '    </div>';

            html += '    <div class="card-inner">';
            html += '      <div class="avatar-wrap">';
            html += '        <img src="' + avatar + '" alt="" onerror="this.src=\'' + defaultAvatar + '\'">';
            html += '        <span class="status-dot ' + statusClass + '" title="' + status + '"></span>';
            html += '      </div>';
            html += '      <h6 class="person-name"><a href="' + editUrl + '">' + escapeHtml(fullName) + '</a></h6>';
            html += '      <p class="person-id">' + escapeHtml(idNumber) + '</p>';
            html += '      <p class="person-meta">' + (p.gender || '') + (p.ethnic_group ? ' &bull; ' + p.ethnic_group : '') + '</p>';

            // Action buttons
            html += '      <div class="action-btns">';
            if (mobile) {
                html += '        <a href="tel:' + mobile + '" class="act-btn btn-call" title="' + escapeHtml(mobile) + '"><i class="fas fa-phone-alt"></i></a>';
            }
            if (email) {
                html += '        <a href="mailto:' + email + '" class="act-btn btn-email" title="' + escapeHtml(email) + '"><i class="fas fa-envelope"></i></a>';
            }
            html += '        <a href="' + editUrl + '" class="act-btn btn-view" title="View Details"><i class="fas fa-user"></i></a>';
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

    // Search
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadPersons(searchInput.value, 0);
        }, 300);
    });

    // Pagination
    prevBtn.addEventListener('click', function() {
        if (currentOffset > 0) loadPersons(searchInput.value, currentOffset - limit);
    });

    nextBtn.addEventListener('click', function() {
        if (currentOffset + limit < total) loadPersons(searchInput.value, currentOffset + limit);
    });

    // Delete
    window.deletePerson = function(id, name) {
        Swal.fire({
            title: 'Delete Person?',
            html: 'Are you sure you want to remove <strong>"' + name + '"</strong>?<br><small class="text-muted">This action cannot be undone.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#009688',
            confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete',
            cancelButtonText: 'Cancel',
            customClass: { popup: 'rounded-4' }
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
