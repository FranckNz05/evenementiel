<div class="modern-card">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-ticket-alt"></i>
            Gestion des Tickets
        </div>
        <div class="d-flex gap-2">
            <div class="input-group">
                <input type="text" class="form-control form-input-modern" id="ticketSearch" placeholder="Rechercher par code..." style="padding: 0.5rem 1rem;">
                <button class="btn btn-primary" type="button" id="searchTicketBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <select class="form-select form-select-modern" id="ticketSort" style="padding: 0.5rem 2rem 0.5rem 1rem;">
                <option value="">Trier par...</option>
                <option value="date">Date d'achat</option>
                <option value="price">Prix</option>
                <option value="event">Événement</option>
            </select>
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
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
        <div id="ticketsPagination" class="pagination-container">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTicketPage = 1;

function loadTickets(page = 1, search = '', sort = '') {
    $.get(`{{ route('admin.tickets') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#ticketsTableBody').empty();
        
        response.data.forEach(ticket => {
            const statusBadge = ticket.order.status === 'completed' 
                ? '<span class="modern-badge badge-success">Payé</span>' 
                : '<span class="modern-badge badge-warning">' + ticket.order.status + '</span>';
                
            $('#ticketsTableBody').append(`
                <tr>
                    <td><div class="fw-bold text-dark font-monospace">${ticket.unique_code}</div></td>
                    <td>${ticket.event.title}</td>
                    <td>${ticket.user.name}</td>
                    <td><div class="fw-bold">${new Intl.NumberFormat('fr-FR').format(ticket.price)} FCFA</div></td>
                    <td>${new Date(ticket.created_at).toLocaleDateString()}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group-modern" role="group">
                            <button class="btn btn-sm btn-info-modern view-ticket" data-id="${ticket.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning-modern edit-ticket" data-id="${ticket.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger-modern delete-ticket" data-id="${ticket.id}">
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
