<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression Stock Complet</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: #fff;
            font-size: 12px;
        }
        .print-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .product-section {
            border: 1px solid #000;
            padding: 8px 12px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #000;
            text-transform: uppercase;
        }
        .header .meta {
            color: #333;
            font-size: 11px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 5px;
        }
        .bottle-box {
            width: 22px;
            height: 22px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 3px;
            position: relative;
        }
        .bottle-box span {
            font-size: 8px;
            color: #666;
        }
        .footer-info {
            text-align: right;
            font-size: 9px;
            color: #777;
            margin-top: 4px;
        }
        
        @media print {
            body { padding: 0; }
            .print-container { max-width: 100%; }
            .no-print { display: none; }
            .product-section { margin-bottom: 8px; }
        }

        .no-print-toolbar {
            background: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-print {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-print:hover { background-color: #218838; }
    </style>
</head>
<body>

    <div class="no-print no-print-toolbar">
        <div>
            <strong>Impression Compacte ({{ $products->count() }} produits)</strong>
            <small class="d-block text-muted">Ajusté pour ~7-8 produits par page A4</small>
        </div>
        <button onclick="window.print();" class="btn-print">
            Imprimer la liste
        </button>
    </div>

    <div class="print-container">
        @foreach($products as $product)
            <div class="product-section">
                <div class="header">
                    <h1>{{ $product->name }}</h1>
                    <div class="meta">
                        Capacité: <strong>{{ $product->bottles_per_crate }}</strong> • 
                        Date: {{ date('d/m') }}
                    </div>
                </div>

                <div class="grid">
                    @for ($i = 1; $i <= $product->bottles_per_crate; $i++)
                        <div class="bottle-box">
                            <span>{{ $i }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
