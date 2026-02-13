<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-ticket-alt me-1"></i>
                Gestion des Tickets
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour au tableau de bord
                </a>
                <div class="input-group">
                    <input type="text" class="form-control" id="ticketSearch" placeholder="Rechercher par code...">
                    <button class="btn btn-outline-secondary" type="button" id="searchTicketBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select class="form-select" id="ticketSort">
                    <option value="">Trier par...</option>
                    <option value="date">Date d'achat</option>
                    <option value="price">Prix</option>
                    <option value="event">Événement</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Événement</th>
                        <th>Acheteur</th>
                        <th>Prix</th>
                        <th>Date d'achat</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ticketsTableBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
        <div id="ticketsPagination" class="d-flex justify-content-center mt-3">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTicketPage = 1;

function loadTickets(page = 1, search = '', sort = '') {
    $.get(`{{ url('admin/tickets') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#ticketsTableBody').empty();

        response.data.forEach(ticket => {
            $('#ticketsTableBody').append(`
                <tr>
                    <td>${ticket.ticket_code || 'N/A'}</td>
                    <td>${ticket.event.title}</td>
                    <td>${ticket.user.name}</td>
                    <td>${new Intl.NumberFormat('fr-FR').format(ticket.price)} FCFA</td>
                    <td>${new Date(ticket.created_at).toLocaleDateString()}</td>
                    <td>
                        <span class="badge bg-${ticket.statut === 'disponible' ? 'success' : 'warning'}">
                            ${ticket.statut || 'N/A'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary btn-sm view-ticket" data-id="${ticket.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm edit-ticket" data-id="${ticket.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-ticket" data-id="${ticket.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });

        // Update pagination
        updateTicketPagination(response);
    });
}

function updateTicketPagination(response) {
    const pagination = $('#ticketsPagination');
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

// Event Listeners
$('#ticketSearch').on('input', function() {
    loadTickets(1, $(this).val(), $('#ticketSort').val());
});

$('#ticketSort').on('change', function() {
    loadTickets(1, $('#ticketSearch').val(), $(this).val());
});

$('#ticketsPagination').on('click', '.page-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    loadTickets(page, $('#ticketSearch').val(), $('#ticketSort').val());
});

// Initial load
loadTickets();
</script>
@endpush
