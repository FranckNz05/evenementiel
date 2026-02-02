#!/bin/bash

# Script de déploiement pour MokiliEvent Microservices
set -e

echo "🚀 Déploiement de MokiliEvent Microservices"

# Variables
PROJECT_NAME="evenementiel"
BACKUP_DIR="/backups/$(date +%Y%m%d_%H%M%S)"

# Fonction pour afficher les messages
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Fonction pour vérifier les prérequis
check_prerequisites() {
    log "🔍 Vérification des prérequis..."
    
    # Vérifier Docker
    if ! command -v docker &> /dev/null; then
        log "❌ Docker n'est pas installé"
        exit 1
    fi
    
    # Vérifier Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        log "❌ Docker Compose n'est pas installé"
        exit 1
    fi
    
    # Vérifier les permissions
    if ! docker ps &> /dev/null; then
        log "❌ Permissions Docker insuffisantes"
        exit 1
    fi
    
    log "✅ Prérequis vérifiés"
}

# Fonction pour créer une sauvegarde
create_backup() {
    log "💾 Création d'une sauvegarde..."
    
    mkdir -p $BACKUP_DIR
    
    # Sauvegarder les volumes Docker
    docker run --rm -v evenementiel_postgres_user_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/postgres_user_data.tar.gz -C /data .
    docker run --rm -v evenementiel_postgres_event_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/postgres_event_data.tar.gz -C /data .
    docker run --rm -v evenementiel_postgres_ticket_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/postgres_ticket_data.tar.gz -C /data .
    docker run --rm -v evenementiel_postgres_payment_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/postgres_payment_data.tar.gz -C /data .
    docker run --rm -v evenementiel_postgres_notification_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/postgres_notification_data.tar.gz -C /data .
    docker run --rm -v evenementiel_postgres_analytics_data:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/postgres_analytics_data.tar.gz -C /data .
    
    log "✅ Sauvegarde créée dans $BACKUP_DIR"
}

# Fonction pour arrêter les services existants
stop_services() {
    log "🛑 Arrêt des services existants..."
    
    if docker-compose ps | grep -q "Up"; then
        docker-compose down
        log "✅ Services arrêtés"
    else
        log "ℹ️ Aucun service en cours d'exécution"
    fi
}

# Fonction pour construire les images
build_images() {
    log "🔨 Construction des images Docker..."
    
    # Construire les images pour chaque service
    docker-compose build --no-cache
    
    log "✅ Images construites"
}

# Fonction pour démarrer les services
start_services() {
    log "🎯 Démarrage des services..."
    
    # Démarrer les services en arrière-plan
    docker-compose up -d
    
    # Attendre que les services soient prêts
    log "⏳ Attente du démarrage des services..."
    sleep 30
    
    # Vérifier le statut des services
    check_services_health
}

# Fonction pour vérifier la santé des services
check_services_health() {
    log "🏥 Vérification de la santé des services..."
    
    services=("user-service:8000" "event-service:8001" "ticket-service:8002" "payment-service:8003" "notification-service:8004" "analytics-service:8005")
    
    for service in "${services[@]}"; do
        service_name=$(echo $service | cut -d: -f1)
        port=$(echo $service | cut -d: -f2)
        
        if curl -f http://localhost:$port/health &> /dev/null; then
            log "✅ $service_name est en ligne"
        else
            log "❌ $service_name n'est pas accessible"
            exit 1
        fi
    done
    
    log "✅ Tous les services sont en ligne"
}

# Fonction pour exécuter les migrations
run_migrations() {
    log "📊 Exécution des migrations..."
    
    # Attendre que les bases de données soient prêtes
    sleep 10
    
    # Exécuter les migrations pour chaque service
    docker-compose exec user-service php artisan migrate --force
    docker-compose exec event-service php artisan migrate --force
    docker-compose exec ticket-service php artisan migrate --force
    docker-compose exec payment-service php artisan migrate --force
    docker-compose exec notification-service php artisan migrate --force
    docker-compose exec analytics-service php artisan migrate --force
    
    log "✅ Migrations exécutées"
}

# Fonction pour exécuter les seeders
run_seeders() {
    log "🌱 Exécution des seeders..."
    
    # Exécuter les seeders pour chaque service
    docker-compose exec user-service php artisan db:seed --force
    docker-compose exec event-service php artisan db:seed --force
    docker-compose exec ticket-service php artisan db:seed --force
    docker-compose exec payment-service php artisan db:seed --force
    docker-compose exec notification-service php artisan db:seed --force
    docker-compose exec analytics-service php artisan db:seed --force
    
    log "✅ Seeders exécutés"
}

# Fonction pour optimiser l'application
optimize_application() {
    log "⚡ Optimisation de l'application..."
    
    # Optimiser chaque service
    docker-compose exec user-service php artisan config:cache
    docker-compose exec user-service php artisan route:cache
    docker-compose exec user-service php artisan view:cache
    
    docker-compose exec event-service php artisan config:cache
    docker-compose exec event-service php artisan route:cache
    docker-compose exec event-service php artisan view:cache
    
    docker-compose exec ticket-service php artisan config:cache
    docker-compose exec ticket-service php artisan route:cache
    docker-compose exec ticket-service php artisan view:cache
    
    docker-compose exec payment-service php artisan config:cache
    docker-compose exec payment-service php artisan route:cache
    docker-compose exec payment-service php artisan view:cache
    
    docker-compose exec notification-service php artisan config:cache
    docker-compose exec notification-service php artisan route:cache
    docker-compose exec notification-service php artisan view:cache
    
    docker-compose exec analytics-service php artisan config:cache
    docker-compose exec analytics-service php artisan route:cache
    docker-compose exec analytics-service php artisan view:cache
    
    log "✅ Application optimisée"
}

# Fonction pour afficher les informations de déploiement
show_deployment_info() {
    log "📋 Informations de déploiement:"
    echo "  - API Gateway: http://localhost"
    echo "  - User Service: http://192.168.1.186:8000"
    echo "  - Event Service: http://localhost:8001"
    echo "  - Ticket Service: http://localhost:8002"
    echo "  - Payment Service: http://localhost:8003"
    echo "  - Notification Service: http://localhost:8004"
    echo "  - Analytics Service: http://localhost:8005"
    echo "  - Prometheus: http://localhost:9090"
    echo "  - Grafana: http://localhost:3000"
    echo "  - RabbitMQ Management: http://localhost:15672"
    echo "  - Elasticsearch: http://localhost:9200"
    echo ""
    echo "  - Sauvegarde: $BACKUP_DIR"
    echo "  - Logs: docker-compose logs -f"
    echo "  - Statut: docker-compose ps"
}

# Fonction principale
main() {
    log "🎯 Début du déploiement"
    
    check_prerequisites
    create_backup
    stop_services
    build_images
    start_services
    run_migrations
    run_seeders
    optimize_application
    
    log "🎉 Déploiement terminé avec succès!"
    show_deployment_info
}

# Gestion des arguments
case "${1:-deploy}" in
    "deploy")
        main
        ;;
    "stop")
        stop_services
        ;;
    "start")
        start_services
        ;;
    "restart")
        stop_services
        start_services
        ;;
    "logs")
        docker-compose logs -f
        ;;
    "status")
        docker-compose ps
        ;;
    "backup")
        create_backup
        ;;
    "health")
        check_services_health
        ;;
    *)
        echo "Usage: $0 {deploy|stop|start|restart|logs|status|backup|health}"
        exit 1
        ;;
esac
