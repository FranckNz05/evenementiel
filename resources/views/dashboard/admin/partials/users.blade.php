<div class="modern-card">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-users"></i>
            Gestion des Utilisateurs
        </div>
        <div class="d-flex gap-2">
            <div class="input-group">
                <input type="text" class="form-control form-input-modern" id="userSearch" placeholder="Rechercher..." style="padding: 0.5rem 1rem;">
                <button class="btn btn-primary" type="button" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <select class="form-select form-select-modern" id="userSort" style="padding: 0.5rem 2rem 0.5rem 1rem;">
                <option value="">Trier par...</option>
                <option value="date">Date d'inscription</option>
                <option value="purchases">Achats</option>
                <option value="role">Rôle</option>
            </select>
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th>Achats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
        <div id="usersPagination" class="pagination-container">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

<!-- Organizer Requests Section -->
<div class="modern-card mt-4">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-user-plus"></i>
            Demandes Organisateur
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Date de demande</th>
                        <th>Motivation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizerRequests ?? collect() as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->user->email }}</td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ Str::limit($request->motivation, 50) }}</td>
                            <td>
                                <div class="btn-group-modern" role="group">
                                    <button type="button" class="btn btn-success-modern btn-sm-modern approve-request" data-id="{{ $request->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger-modern btn-sm-modern reject-request" data-id="{{ $request->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucune demande d'organisateur en attente</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;

function loadUsers(page = 1, search = '', sort = '') {
    $.get(`{{ route('admin.users') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#usersTableBody').empty();
        
        response.data.forEach(user => {
            $('#usersTableBody').append(`
                <tr>
                    <td><div class="fw-bold text-dark">${user.name}</div></td>
                    <td>${user.email}</td>
                    <td><span class="modern-badge badge-info">${user.roles.map(role => role.name).join(', ')}</span></td>
                    <td>${new Date(user.created_at).toLocaleDateString()}</td>
                    <td><div class="fw-bold">${user.total_purchases}</div></td>
                    <td>
                        <div class="btn-group-modern" role="group">
                            <button class="btn btn-sm btn-info-modern view-user" data-id="${user.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning-modern edit-user" data-id="${user.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger-modern delete-user" data-id="${user.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });

        // Update pagination
        updatePagination(response);
    });
}

function updatePagination(response) {
    const pagination = $('#usersPagination');
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
$('#userSearch').on('input', function() {
    loadUsers(1, $(this).val(), $('#userSort').val());
});

$('#userSort').on('change', function() {
    loadUsers(1, $('#userSearch').val(), $(this).val());
});

$('#usersPagination').on('click', '.page-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    loadUsers(page, $('#userSearch').val(), $('#userSort').val());
});

// Handle organizer requests
$('.approve-request, .reject-request').click(function() {
    const requestId = $(this).data('id');
    const status = $(this).hasClass('approve-request') ? 'approved' : 'rejected';
    
    $.ajax({
        url: `/admin/organizer-requests/${requestId}`,
        type: 'PATCH',
        data: { status: status },
        success: function(response) {
            // Reload the page or update the table
            location.reload();
        }
    });
});

// Initial load
loadUsers();
</script>
@endpush
