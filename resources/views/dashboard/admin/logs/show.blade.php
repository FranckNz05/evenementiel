@extends('layouts.app')

@section('title', 'Détails du Log - Administration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Détails du Log
                    </h3>
                    <a href="{{ route('admin.logs.index') }}" class="modern-btn btn-secondary-modern">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body-modern">
                    @php
                        $logData = $log['_source'];
                        $level = $logData['level'] ?? 'UNKNOWN';
                        $levelClass = match($level) {
                            'ERROR' => 'danger',
                            'WARNING' => 'warning',
                            'INFO' => 'info',
                            'DEBUG' => 'secondary',
                            default => 'light'
                        };
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informations générales</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><code>{{ $log['_id'] }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Timestamp:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($logData['@timestamp'])->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Niveau:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $levelClass }}">{{ $level }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Service:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $logData['service'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Environnement:</strong></td>
                                    <td>{{ $logData['environment'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Canal:</strong></td>
                                    <td>{{ $logData['channel'] ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Tags et métadonnées</h5>
                            @if(isset($logData['tags']) && count($logData['tags']) > 0)
                                <div class="mb-3">
                                    <strong>Tags:</strong><br>
                                    @foreach($logData['tags'] as $tag)
                                        <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if(isset($logData['extra']) && count($logData['extra']) > 0)
                                <div class="mb-3">
                                    <strong>Informations supplémentaires:</strong>
                                    <pre class="bg-light p-2 rounded"><code>{{ json_encode($logData['extra'], JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Message</h5>
                            <div class="alert alert-{{ $levelClass }}">
                                <pre style="white-space: pre-wrap; margin: 0;">{{ $logData['message'] ?? 'N/A' }}</pre>
                            </div>
                        </div>
                    </div>

                    @if(isset($logData['context']) && count($logData['context']) > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Contexte</h5>
                                <div class="bg-light p-3 rounded">
                                    <pre><code>{{ json_encode($logData['context'], JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
