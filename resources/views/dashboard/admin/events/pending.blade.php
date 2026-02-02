@extends('layouts.admin')

@section('title', 'Événements en attente')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clock me-1"></i>
                Événements en attente d'approbation
            </h6>
            <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pendingEventsTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Titre</th>
                            <th>Organisateur</th>
                            <th>Catégorie</th>
                            <th>Date de début</th>
                            <th>Lieu</th>
                            <th>Demandé le</th>
                            <th>Actions</th>
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
                    return `<div class="event-title">${data || 'Sans titre'}</div>`;
                }
            },
            { 
                data: 'user.organizer_profile.company_name',
                name: 'user.organizerProfile.company_name',
                defaultContent: 'N/A'
            },
            { 
                data: 'category.name',
                name: 'category.name',
                defaultContent: 'N/A'
            },
            { 
                data: 'start_date',
                name: 'start_date',
                render: function(data) {
                    return data ? new Date(data).toLocaleString() : 'N/A';
                }
            },
            { 
                data: 'ville',
                name: 'ville',
                render: function(data, type, row) {
                    return data ? `${data}, ${row.pays || ''}` : 'N/A';
                }
            },
            { 
                data: 'publication_request.created_at',
                name: 'publication_request.created_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleString() : 'N/A';
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="/admin/events/${data}" class="btn btn-info" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-success approve-btn" data-id="${data}" title="Approuver">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger reject-btn" data-id="${data}" title="Rejeter">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        }
    });

    // Gestion de l'approbation
    $('#pendingEventsTable').on('click', '.approve-btn', function() {
        const eventId = $(this).data('id');
        approveEvent(eventId, table);
    });

    // Gestion du rejet
    let currentEventId;
    $('#pendingEventsTable').on('click', '.reject-btn', function() {
        currentEventId = $(this).data('id');
        $('#rejectModal').modal('show');
    });

    $('#rejectForm').submit(function(e) {
        e.preventDefault();
        const reason = $('#rejection_reason').val();
        rejectEvent(currentEventId, reason, table);
    });
});

function approveEvent(eventId, table) {
    if (!confirm('Êtes-vous sûr de vouloir approuver cet événement ?')) return;

    $.ajax({
        url: `/admin/events/${eventId}/approve`,
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Une erreur est survenue');
        }
    });
}

function rejectEvent(eventId, reason, table) {
    if (!reason || reason.trim() === '') {
        toastr.warning('Veuillez fournir un motif de rejet');
        return;
    }

    $.ajax({
        url: `/admin/events/${eventId}/reject`,
        method: 'POST',
        data: { 
            _token: '{{ csrf_token() }}',
            rejection_reason: reason
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                table.ajax.reload(null, false);
                $('#rejectModal').modal('hide');
                $('#rejection_reason').val('');
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Une erreur est survenue');
        }
    });
}
</script>
@endpush