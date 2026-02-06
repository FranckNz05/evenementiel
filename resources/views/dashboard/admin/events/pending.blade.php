@extends('layouts.dashboard')

@section('title', 'Événements en Attente')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Événements en Attente</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Événements</a></li>
                    <li class="breadcrumb-item active">Modération</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.events.index') }}" class="btn btn-light shadow-sm border">
                <i class="fas fa-arrow-left me-1"></i> Tous les événements
            </a>
        </div>
    </div>

    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header-modern">
            <h5 class="card-title my-1 text-white">
                <i class="fas fa-clock me-2"></i>Demandes de publication en attente
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-4">
                <table class="table modern-table display w-100" id="pendingEventsTable">
                    <thead>
                        <tr>
                            <th>Titre de l'événement</th>
                            <th>Organisateur</th>
                            <th>Catégorie</th>
                            <th>Date début</th>
                            <th>Lieu</th>
                            <th>Soumis le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.events.partials.reject-modal')
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#pendingEventsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.events.pending-api') }}",
            dataType: 'json'
        },
        columns: [
            { 
                data: 'title',
                name: 'title',
                render: function(data, type, row) {
                    return `<div class="fw-bold text-dark">${data || 'Sans titre'}</div>`;
                }
            },
            { 
                data: 'user.organizer_profile.company_name',
                name: 'user.organizerProfile.company_name',
                render: function(data) {
                    return `<span class="small fw-medium">${data || 'N/A'}</span>`;
                }
            },
            { 
                data: 'category.name',
                name: 'category.name',
                render: function(data) {
                    return `<span class="badge bg-light text-primary border border-primary-subtle">${data || 'N/A'}</span>`;
                }
            },
            { 
                data: 'start_date',
                name: 'start_date',
                render: function(data) {
                    if (!data) return 'N/A';
                    const date = new Date(data);
                    return `<div class="small fw-bold text-dark">${date.toLocaleDateString('fr-FR')}</div><div class="small text-muted">${date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}</div>`;
                }
            },
            { 
                data: 'ville',
                name: 'ville',
                render: function(data, type, row) {
                    return `<div class="small"><i class="fas fa-map-marker-alt text-danger me-1"></i>${data || 'N/A'}</div>`;
                }
            },
            { 
                data: 'publication_request.created_at',
                name: 'publication_request.created_at',
                render: function(data) {
                    if (!data) return 'N/A';
                    const date = new Date(data);
                    return `<span class="small text-muted">${date.toLocaleDateString('fr-FR')}</span>`;
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group shadow-sm">
                            <a href="/admin/events/${data}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-success approve-btn" data-id="${data}" title="Approuver la publication">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger reject-btn" data-id="${data}" title="Rejeter la demande">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        drawCallback: function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-sm');
        }
    });

    // Approbation
    $('#pendingEventsTable').on('click', '.approve-btn', function() {
        const eventId = $(this).data('id');
        if (confirm('Souhaitez-vous approuver la publication de cet événement ?')) {
            $.ajax({
                url: `/admin/events/${eventId}/approve`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        if (window.showToast) window.showToast(res.message);
                        table.ajax.reload(null, false);
                    } else {
                        if (window.showToast) window.showToast(res.message, 'error');
                    }
                }
            });
        }
    });

    // Rejet
    let currentEventId;
    $('#pendingEventsTable').on('click', '.reject-btn', function() {
        currentEventId = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    });

    $('#rejectForm').submit(function(e) {
        e.preventDefault();
        const reason = $('#rejection_reason').val();
        if (!reason || reason.trim() === '') {
            alert('Veuillez indiquer un motif de rejet.');
            return;
        }

        $.ajax({
            url: `/admin/events/${currentEventId}/reject`,
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                rejection_reason: reason
            },
            success: function(res) {
                if (res.success) {
                    if (window.showToast) window.showToast(res.message);
                    table.ajax.reload(null, false);
                    bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                    $('#rejection_reason').val('');
                } else {
                    if (window.showToast) window.showToast(res.message, 'error');
                }
            }
        });
    });
});
</script>
<style>
    #pendingEventsTable_wrapper .dataTables_length,
    #pendingEventsTable_wrapper .dataTables_filter {
        margin-bottom: 1.5rem;
    }
    #pendingEventsTable_wrapper .dataTables_info,
    #pendingEventsTable_wrapper .dataTables_paginate {
        margin-top: 1.5rem;
    }
</style>
@endpush