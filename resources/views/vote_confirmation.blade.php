<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de vote - Trophées NSI 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .message {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="message {{ $success ? 'success' : 'error' }}">
        @if($success)
            <h1>Vote enregistré !</h1>
            <p>Merci pour votre vote pour le Prix du Public des Trophées NSI 2026 !</p>
        @else
            <h1>Erreur</h1>
            <p>Erreur lors de la confirmation de votre adresse e-mail, veuillez réessayer.</p>
        @endif
    </div>
</body>
</html>
