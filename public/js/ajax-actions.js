/**
 * Système AJAX pour les actions utilisateur (suivre, liker, commenter)
 * Évite le rechargement de page pour une meilleure UX
 */

class AjaxActions {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.initFollowButtons();
        this.initLikeButtons();
        this.initCommentForms();
    }

    // ============================================
    // GESTION DES BOUTONS DE SUIVI
    // ============================================
    initFollowButtons() {
        const followSection = document.getElementById('follow-section');
        if (!followSection) return;

        // Vérifier si l'utilisateur est connecté
        if (!this.csrfToken) {
            console.log('Utilisateur non connecté - boutons de suivi désactivés');
            return;
        }

        this.attachFollowEvents();
    }

    attachFollowEvents() {
        const followBtn = document.getElementById('follow-btn');
        const unfollowBtn = document.getElementById('unfollow-btn');

        if (followBtn) {
            followBtn.addEventListener('click', (e) => this.handleFollow(e));
        }

        if (unfollowBtn) {
            unfollowBtn.addEventListener('click', (e) => this.handleUnfollow(e));
        }
    }

    async handleFollow(event) {
        const button = event.target;
        const organizerSlug = button.dataset.organizerSlug;
        const organizerName = button.dataset.organizerName;
        
        // Vérifier si l'utilisateur est connecté
        if (!this.csrfToken) {
            this.showNotification('Vous devez être connecté pour suivre un organisateur', 'error');
            return;
        }
        
        this.setButtonLoading(button, 'Suivi...');
        
        try {
            console.log('Tentative de suivi:', organizerSlug);
            console.log('Token CSRF:', this.csrfToken);
            
            const response = await fetch(`/organizers/${organizerSlug}/follow`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            console.log('Réponse reçue:', response.status, response.statusText);
            
            if (!response.ok) {
                if (response.status === 401) {
                    this.showNotification('Vous devez être connecté pour suivre un organisateur', 'error');
                    this.restoreButton(button, '<i class="fas fa-user-plus me-2"></i><span>Suivre</span>');
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Données reçues:', data);
            
            if (data.success) {
                this.updateFollowButton(organizerSlug, organizerName, 'unfollow');
                this.updateFollowersCount(1);
                this.showNotification('Vous suivez maintenant ' + organizerName, 'success');
            } else {
                this.showNotification(data.message || 'Erreur lors du suivi', 'error');
                this.restoreButton(button, '<i class="fas fa-user-plus me-2"></i><span>Suivre</span>');
            }
        } catch (error) {
            console.error('Erreur détaillée:', error);
            this.showNotification('Erreur de connexion: ' + error.message, 'error');
            this.restoreButton(button, '<i class="fas fa-user-plus me-2"></i><span>Suivre</span>');
        }
    }

    async handleUnfollow(event) {
        const button = event.target;
        const organizerSlug = button.dataset.organizerSlug;
        const organizerName = button.dataset.organizerName;
        
        if (!confirm(`Êtes-vous sûr de vouloir vous désabonner de ${organizerName} ?`)) {
            return;
        }
        
        // Vérifier si l'utilisateur est connecté
        if (!this.csrfToken) {
            this.showNotification('Vous devez être connecté pour vous désabonner', 'error');
            return;
        }
        
        this.setButtonLoading(button, 'Désabonnement...');
        
        try {
            console.log('Tentative de désabonnement:', organizerSlug);
            console.log('Token CSRF:', this.csrfToken);
            
            const response = await fetch(`/organizers/${organizerSlug}/unfollow`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            console.log('Réponse reçue:', response.status, response.statusText);
            
            if (!response.ok) {
                if (response.status === 401) {
                    this.showNotification('Vous devez être connecté pour vous désabonner', 'error');
                    this.restoreButton(button, '<i class="fas fa-check me-2"></i><span>Abonné</span>');
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Données reçues:', data);
            
            if (data.success) {
                this.updateFollowButton(organizerSlug, organizerName, 'follow');
                this.updateFollowersCount(-1);
                this.showNotification('Vous ne suivez plus ' + organizerName, 'success');
            } else {
                this.showNotification(data.message || 'Erreur lors du désabonnement', 'error');
                this.restoreButton(button, '<i class="fas fa-check me-2"></i><span>Abonné</span>');
            }
        } catch (error) {
            console.error('Erreur détaillée:', error);
            this.showNotification('Erreur de connexion: ' + error.message, 'error');
            this.restoreButton(button, '<i class="fas fa-check me-2"></i><span>Abonné</span>');
        }
    }

    updateFollowButton(organizerSlug, organizerName, action) {
        const followSection = document.getElementById('follow-section');
        if (!followSection) return;

        if (action === 'unfollow') {
            followSection.innerHTML = `
                <button type="button" 
                        class="btn btn-outline-primary btn-lg rounded-pill px-5 follow-btn"
                        id="unfollow-btn"
                        data-organizer-slug="${organizerSlug}" 
                        data-organizer-name="${organizerName}"
                        aria-label="Ne plus suivre ${organizerName}">
                    <i class="fas fa-check me-2" aria-hidden="true"></i>
                    <span>Abonné</span>
                </button>
            `;
        } else {
            followSection.innerHTML = `
                <button type="button" 
                        class="btn btn-primary btn-lg rounded-pill px-5 follow-btn"
                        id="follow-btn"
                        data-organizer-slug="${organizerSlug}" 
                        data-organizer-name="${organizerName}"
                        aria-label="Suivre ${organizerName}">
                    <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                    <span>Suivre</span>
                </button>
            `;
        }
        
        // Réattacher les événements
        this.attachFollowEvents();
    }

    // ============================================
    // GESTION DES BOUTONS DE LIKE
    // ============================================
    initLikeButtons() {
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', (e) => this.handleLike(e));
        });
    }

    async handleLike(event) {
        const button = event.target.closest('.like-btn');
        const itemId = button.dataset.itemId;
        const itemType = button.dataset.itemType; // 'event', 'blog', etc.
        const isLiked = button.classList.contains('liked');
        
        this.setButtonLoading(button, '...');
        
        try {
            const url = isLiked ? `/${itemType}s/${itemId}/unlike` : `/${itemType}s/${itemId}/like`;
            const method = isLiked ? 'DELETE' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateLikeButton(button, data.liked, data.likes_count);
                this.showNotification(
                    data.liked ? 'Ajouté aux favoris' : 'Retiré des favoris', 
                    'success'
                );
            } else {
                this.showNotification(data.message || 'Erreur lors de l\'action', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur de connexion', 'error');
        }
    }

    updateLikeButton(button, isLiked, likesCount) {
        const icon = button.querySelector('i');
        const countSpan = button.querySelector('.likes-count');
        
        if (isLiked) {
            button.classList.add('liked');
            icon.className = 'fas fa-heart';
            button.style.color = '#e74c3c';
        } else {
            button.classList.remove('liked');
            icon.className = 'far fa-heart';
            button.style.color = '';
        }
        
        if (countSpan) {
            countSpan.textContent = likesCount;
        }
        
        this.restoreButton(button);
    }

    // ============================================
    // GESTION DES FORMULAIRES DE COMMENTAIRE
    // ============================================
    initCommentForms() {
        document.querySelectorAll('.comment-form').forEach(form => {
            form.addEventListener('submit', (e) => this.handleComment(e));
        });
    }

    async handleComment(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);
        
        this.setButtonLoading(submitBtn, 'Publication...');
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.addCommentToList(data.comment);
                form.reset();
                this.showNotification('Commentaire publié', 'success');
            } else {
                this.showNotification(data.message || 'Erreur lors de la publication', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur de connexion', 'error');
        } finally {
            this.restoreButton(submitBtn, 'Publier');
        }
    }

    addCommentToList(comment) {
        const commentsList = document.querySelector('.comments-list');
        if (!commentsList) return;

        const commentElement = document.createElement('div');
        commentElement.className = 'comment-item';
        commentElement.innerHTML = `
            <div class="comment-header">
                <strong>${comment.user_name}</strong>
                <small class="text-muted">${comment.created_at}</small>
            </div>
            <div class="comment-content">${comment.content}</div>
        `;
        
        commentsList.insertBefore(commentElement, commentsList.firstChild);
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================
    setButtonLoading(button, text) {
        button.disabled = true;
        button.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
    }

    restoreButton(button, originalContent = null) {
        button.disabled = false;
        if (originalContent) {
            button.innerHTML = originalContent;
        }
    }

    updateFollowersCount(change) {
        const followersElement = document.querySelector('.stat-item .stat-number');
        if (followersElement) {
            const currentCount = parseInt(followersElement.textContent);
            followersElement.textContent = currentCount + change;
        }
    }

    showNotification(message, type) {
        // Supprimer les notifications existantes
        document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
        `;
        
        // Ajouter les styles si nécessaire
        if (!document.querySelector('#toast-styles')) {
            const styles = document.createElement('style');
            styles.id = 'toast-styles';
            styles.textContent = `
                .toast-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 500;
                    z-index: 9999;
                    animation: slideInRight 0.3s ease-out;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }
                .toast-success { background-color: #28a745; }
                .toast-error { background-color: #dc3545; }
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(toast);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialiser le système AJAX quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    new AjaxActions();
});
