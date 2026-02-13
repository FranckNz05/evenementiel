<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-1"></i>
                Gestion des Événements
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour au tableau de bord
                </a>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Créer un événement
                </a>
                <div class="input-group">
                    <input type="text" class="form-control" id="eventSearch" placeholder="Rechercher...">
                    <button class="btn btn-outline-secondary" type="button" id="searchEventBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select class="form-select" id="eventSort">
                    <option value="">Trier par...</option>
                    <option value="popular">Popularité</option>
                    <option value="date">Date</option>
                    <option value="revenue">Revenus</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
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
        <div id="eventsPagination" class="d-flex justify-content-center mt-3">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

<!-- Event Publication Requests -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-clock me-1"></i>
            Demandes de Publication
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
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
    $.get(`{{ url('admin/events') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#eventsTableBody').empty();

        response.data.forEach(event => {
            $('#eventsTableBody').append(`
                <tr>
                    <td>${event.title}</td>
                    <td>${event.organizer ? event.organizer.company_name : 'N/A'}</td>
                    <td>${new Date(event.start_date).toLocaleDateString()}</td>
                    <td>${event.tickets_sold}</td>
                    <td>${new Intl.NumberFormat('fr-FR').format(event.revenue)} FCFA</td>
                    <td>
                        <span class="badge bg-${event.status === 'published' ? 'success' : 'warning'}">
                            ${event.status}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary btn-sm view-event" data-id="${event.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm edit-event" data-id="${event.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-event" data-id="${event.id}">
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
        let paginationHtml = '<ul class="pagination">';

        // Previous page
        paginationHtml += `
            <li class="page-item ${response.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${response.current_page - 1}">Précédent</a>
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
                <a class="page-link" href="#" data-page="${response.current_page + 1}">Suivant</a>
            </li>
        `;

        paginationHtml += '</ul>';
        pagination.html(paginationHtml);
    }
}

function loadPendingEvents() {
    $.get('/admin/events/pending', function(response) {
        $('#pendingEventsBody').empty();

        response.forEach(event => {
            $('#pendingEventsBody').append(`
                <tr>
                    <td>${event.title}</td>
                    <td>${event.user.name}</td>
                    <td>${new Date(event.start_date).toLocaleDateString()}</td>
                    <td>${new Intl.NumberFormat('fr-FR').format(event.ticket_price)} FCFA</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-success btn-sm approve-event" data-id="${event.id}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm reject-event" data-id="${event.id}">
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
