<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MokiliEvent') }} - Maintenance</title>
    
    <!-- Favicon -->
    @php
        $favicon = DB::table('settings')->where('key', 'favicon')->value('value');
    @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    @else
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0 20px;
            text-align: center;
        }
        .maintenance-container {
            max-width: 600px;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .logo {
            max-width: 200px;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #666;
        }
        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
        
        /* Bouton discret de maintenance */
        .maintenance-access-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: rgba(78, 115, 223, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: white;
        }
        
        .maintenance-access-btn:hover {
            background-color: rgba(78, 115, 223, 1);
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        
        .maintenance-access-btn i {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Bouton discret pour l'accès admin -->
    <a href="{{ route('admin.maintenance.login') }}" class="maintenance-access-btn" title="Accès administrateur">
        <i class="fas fa-cog"></i>
    </a>
    
    <div class="maintenance-container">
        @php
            $logo = DB::table('settings')->where('key', 'logo')->value('value');
            $siteName = DB::table('settings')->where('key', 'site_name')->value('value') ?? config('app.name');
        @endphp
        
        @if($logo)
            <img src="{{ asset($logo) }}" alt="{{ $siteName }}" class="logo">
        @else
            <img src="{{ asset('images/logo.png') }}" alt="{{ $siteName }}" class="logo">
        @endif
        
        <h1>Site en maintenance</h1>
        <p>Nous effectuons actuellement des travaux de maintenance sur notre site. Nous serons de retour très bientôt. Merci de votre patiences.</p>
        
        @if(isset($exception) && $exception->getStatusCode() === 503 && $exception->getMessage())
            <p>{{ $exception->getMessage() }}</p>
        @endif
    </div>
</body>
</html>