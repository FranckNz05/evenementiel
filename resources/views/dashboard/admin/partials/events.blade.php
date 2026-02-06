<div class="modern-card">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-calendar-alt"></i>
            Gestion des Événements
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary-modern">
                <i class="fas fa-plus"></i>
                Créer un événement
            </a>
            <div class="input-group">
                <input type="text" class="form-control form-input-modern" id="eventSearch" placeholder="Rechercher..." style="padding: 0.5rem 1rem;">
                <button class="btn btn-primary" type="button" id="searchEventBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <select class="form-select form-select-modern" id="eventSort" style="padding: 0.5rem 2rem 0.5rem 1rem;">
                <option value="">Trier par...</option>
                <option value="popular">Popularité</option>
                <option value="date">Date</option>
                <option value="revenue">Revenus</option>
            </select>
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Organisateur</th>
                        <th>Date</th>
                        <th>Tickets Vendus</th>
                        <th>Revenus</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsTableBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
        <div id="eventsPagination" class="pagination-container">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

<!-- Event Publication Requests -->
<div class="modern-card mt-4">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-clock"></i>
            Demandes de Publication
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>Organisateur</th>
                        <th>Date de l'événement</th>
                        <th>Prix des tickets</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pendingEventsBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentEventPage = 1;

function loadEvents(page = 1, search = '', sort = '') {
    $.get(`{{ route('admin.events') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#eventsTableBody').empty();
        
        response.data.forEach(event => {
            let statusBadge = '';
            if (event.status === 'published') statusBadge = '<span class="modern-badge badge-success">Publié</span>';
            else if (event.status === 'draft') statusBadge = '<span class="modern-badge badge-warning">Brouillon</span>';
            else statusBadge = '<span class="modern-badge badge-danger">Refusé</span>';

            $('#eventsTableBody').append(`
                <tr>
                    <td><div class="fw-bold text-dark">${event.title}</div></td>
                    <td>${event.user.name}</td>
                    <td>${new Date(event.start_date).toLocaleDateString()}</td>
                    <td>${event.tickets_sold}</td>
                    <td><div class="fw-bold">${new Intl.NumberFormat('fr-FR').format(event.revenue)} FCFA</div></td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group-modern" role="group">
                            <button class="btn btn-sm btn-info-modern view-event" data-id="${event.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning-modern edit-event" data-id="${event.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger-modern delete-event" data-id="${event.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });

        // Update pagination
        updateEventPagination(response);
    });
}

function updateEventPagination(response) {
    const pagination = $('#eventsPagination');
    pagination.empty();

    if (response.last_page > 1) {
        let paginationHtml = '<ul class="pagination-modern">';
        
        // Previous page
        paginationHtml += `
            <li class="page-item ${response.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${response.current_page - 1}"><i class="fas fa-chevron-left me-1"></i> Précédent</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= response.last_page; i++) {
            paginationHtml += `
                <li class="page-item ${response.current_page === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Next page
        paginationHtml += `
            <li class="page-item ${response.current_page === response.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${response.current_page + 1}">Suivant <i class="fas fa-chevron-right ms-1"></i></a>
            </li>
        `;

        paginationHtml += '</ul>';
        pagination.html(paginationHtml);
    }
}

function loadPendingEvents() {
    $.get('/admin/events/pending', function(response) {
        $('#pendingEventsBody').empty();
        
        if (response.length === 0) {
            $('#pendingEventsBody').append('<tr><td colspan="5" class="text-center text-muted py-4">Aucune demande en attente</td></tr>');
            return;
        }

        response.forEach(event => {
            $('#pendingEventsBody').append(`
                <tr>
                    <td><div class="fw-bold text-dark">${event.title}</div></td>
                    <td>${event.user.name}</td>
                    <td>${new Date(event.start_date).toLocaleDateString()}</td>
                    <td>${new Intl.NumberFormat('fr-FR').format(event.ticket_price)} FCFA</td>
                    <td>
                        <div class="btn-group-modern" role="group">
                            <button class="btn btn-success-modern btn-sm-modern approve-event" data-id="${event.id}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger-modern btn-sm-modern reject-event" data-id="${event.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    });
}

// Event Listeners
$('#eventSearch').on('input', function() {
    loadEvents(1, $(this).val(), $('#eventSort').val());
});

$('#eventSort').on('change', function() {
    loadEvents(1, $('#eventSearch').val(), $(this).val());
});

$('#eventsPagination').on('click', '.page-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    loadEvents(page, $('#eventSearch').val(), $('#eventSort').val());
});

// Handle event approval/rejection
$(document).on('click', '.approve-event, .reject-event', function() {
    const eventId = $(this).data('id');
    const status = $(this).hasClass('approve-event') ? 'published' : 'rejected';
    
    $.ajax({
        url: `/admin/events/${eventId}/status`,
        type: 'PATCH',
        data: { status: status },
        success: function(response) {
            loadPendingEvents();
            loadEvents(currentEventPage);
        }
    });
});

// Initial load
loadEvents();
loadPendingEvents();
</script>
@endpush
