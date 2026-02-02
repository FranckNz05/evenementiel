<div class="modal fade" id="exportLogsModal" tabindex="-1" role="dialog" aria-labelledby="exportLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportLogsModalLabel">Exporter les logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.logs.export') }}" method="GET">
                <div class="modal-body">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="level" value="{{ request('level') }}">
                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                    <input type="hidden" name="channel" value="{{ request('channel') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    
                    <div class="form-group">
                        <label for="export_format">Format d'exportation</label>
                        <select class="form-control" id="export_format" name="format" required>
                            <option value="csv">CSV (Excel)</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="export_limit">Nombre maximum d'entrées</label>
                        <input type="number" class="form-control" id="export_limit" name="limit" min="1" max="10000" value="1000">
                        <small class="form-text text-muted">Limité à 10 000 entrées maximum pour des raisons de performances.</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="export_all_pages" name="all_pages" value="1">
                            <label class="form-check-label" for="export_all_pages">
                                Exporter tous les résultats (ignorer la pagination)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download mr-1"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Gestion du bouton d'export
        $('.export-logs-btn').on('click', function(e) {
            e.preventDefault();
            $('#exportLogsModal').modal('show');
        });
        
        // Gestion de la case à cocher "Toutes les pages"
        $('#export_all_pages').on('change', function() {
            if ($(this).is(':checked')) {
                $('#export_limit').val(10000).prop('disabled', true);
            } else {
                $('#export_limit').val(1000).prop('disabled', false);
            }
        });
    });
</script>
@endpush
