<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-blog me-1"></i>
                Gestion des Articles
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour au tableau de bord
                </a>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Créer un article
                </a>
                <div class="input-group">
                    <input type="text" class="form-control" id="blogSearch" placeholder="Rechercher...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBlogBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select class="form-select" id="blogSort">
                    <option value="">Trier par...</option>
                    <option value="popular">Popularité</option>
                    <option value="date">Date</option>
                    <option value="comments">Commentaires</option>
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
        <div id="blogsPagination" class="d-flex justify-content-center mt-3">
            <!-- Pagination loaded dynamically -->
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentBlogPage = 1;

function loadBlogs(page = 1, search = '', sort = '') {
    $.get(`{{ url('admin/blogs') }}?page=${page}&search=${search}&sort=${sort}`, function(response) {
        $('#blogsTableBody').empty();

        response.data.forEach(blog => {
            $('#blogsTableBody').append(`
                <tr>
                    <td>${blog.title}</td>
                    <td>${blog.user.name}</td>
                    <td>${new Date(blog.created_at).toLocaleDateString()}</td>
                    <td>${blog.views}</td>
                    <td>${blog.likes_count}</td>
                    <td>${blog.comments_count}</td>
                    <td>
                        <span class="badge bg-${blog.status === 'published' ? 'success' : 'warning'}">
                            ${blog.status}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary btn-sm view-blog" data-id="${blog.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm edit-blog" data-id="${blog.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-blog" data-id="${blog.id}">
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
