<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Casiers de Boissons</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 12px; border: none; }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        .btn { border-radius: 8px; padding: 0.5rem 1.2rem; }
        .form-control { border-radius: 8px; padding: 0.6rem 1rem; }
        .table th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: #0d6efd !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-box-open me-2"></i>Mokili Stock</a>
    </div>
</nav>

<div class="container py-2">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-dark mb-0">Gestion des Casiers de Boissons</h1>
            <p class="text-muted">Gérez vos produits et générez des fiches d'inventaire.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulaire d'ajout -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0"><i class="fas fa-plus-circle me-2"></i>Nouveau Produit</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock.casiers.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du Produit</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ex: Primus 72cl" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="bottles_per_crate" class="form-label">Bouteilles par casier</label>
                            <input type="number" name="bottles_per_crate" id="bottles_per_crate" class="form-control @error('bottles_per_crate') is-invalid @enderror" placeholder="Ex: 24" value="{{ old('bottles_per_crate') }}" required>
                            @error('bottles_per_crate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer le produit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des produits -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Produits Enregistrés</h5>
                    @if($products->count() > 0)
                        <a href="{{ route('stock.casiers.print') }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="fas fa-print me-1"></i> Tout Imprimer
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nom du Produit</th>
                                    <th>Bouteilles</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $product->name }}</td>
                                        <td>
                                            <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">
                                                <i class="fas fa-wine-bottle me-1"></i> {{ $product->bottles_per_crate }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm">
                                                <form action="{{ route('stock.casiers.destroy', $product) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce produit ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-info-circle mb-2 d-block fa-2x"></i>
                                            Aucun produit enregistré pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
