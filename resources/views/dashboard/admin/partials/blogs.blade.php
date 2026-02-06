<div class="modern-card">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-blog"></i>
            Gestion des Articles
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary-modern">
                <i class="fas fa-plus"></i>
                Créer un article
            </a>
            <div class="input-group">
                <input type="text" class="form-control form-input-modern" id="blogSearch" placeholder="Rechercher..." style="padding: 0.5rem 1rem;">
                <button class="btn btn-primary" type="button" id="searchBlogBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <select class="form-select form-select-modern" id="blogSort" style="padding: 0.5rem 2rem 0.5rem 1rem;">
                <option value="">Trier par...</option>
                <option value="popular">Popularité</option>
                <option value="date">Date</option>
                <option value="comments">Commentaires</option>
            </select>
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th>Vues</th>
                        <th>Likes</th>
                        <th>Commentaires</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="blogsTableBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
        <div id="blogsPagination" class="pagination-container">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentBlogPage = 1;

function loadBlogs(page = 1, search = '', sort = '') {
    $.get(`{{ route('admin.blogs') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#blogsTableBody').empty();
        
        response.data.forEach(blog => {
            const statusBadge = blog.status === 'published' 
                ? '<span class="modern-badge badge-success">Publié</span>' 
                : '<span class="modern-badge badge-warning">Brouillon</span>';

            $('#blogsTableBody').append(`
                <tr>
                    <td><div class="fw-bold text-dark">${blog.title}</div></td>
                    <td>${blog.user.name}</td>
                    <td>${new Date(blog.created_at).toLocaleDateString()}</td>
                    <td>${blog.views}</td>
                    <td>${blog.likes_count}</td>
                    <td>${blog.comments_count}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group-modern" role="group">
                            <button class="btn btn-sm btn-info-modern view-blog" data-id="${blog.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning-modern edit-blog" data-id="${blog.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger-modern delete-blog" data-id="${blog.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });

        // Update pagination
        updateBlogPagination(response);
    });
}

function updateBlogPagination(response) {
    const pagination = $('#blogsPagination');
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
$('#blogSearch').on('input', function() {
    loadBlogs(1, $(this).val(), $('#blogSort').val());
});

$('#blogSort').on('change', function() {
    loadBlogs(1, $('#blogSearch').val(), $(this).val());
});

$('#blogsPagination').on('click', '.page-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    loadBlogs(page, $('#blogSearch').val(), $('#blogSort').val());
});

// Initial load
loadBlogs();
</script>
@endpush
