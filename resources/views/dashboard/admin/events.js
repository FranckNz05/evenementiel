document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression
    document.addEventListener('click', '.delete-event', function(e) {
        e.preventDefault();
        const eventId = this.dataset.id;
        
        if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
            fetch(`${this.getAttribute('href')}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(handleResponse)
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || 'Événement supprimé avec succès');
                    this.closest('tr').remove();
                    
                    // Recharger si tableau vide
                    if (!document.querySelectorAll('tbody tr').length) {
                        window.location.reload();
                    }
                }
            })
            .catch(handleError);
        }
    });

    // Fonctions utilitaires
    function handleResponse(response) {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    }

    function handleError(error) {
        console.error('Error:', error);
        showToast('error', error.message || 'Une erreur est survenue');
    }

    function showToast(type, message) {
        const bgColor = type === 'success' ? '#4CAF50' : '#F44336';
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: bgColor,
        }).showToast();
    }
});