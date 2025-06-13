<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #198754;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau message de contact</h1>
        </div>
        
        <div class="content">
            <p><strong>De :</strong> {{ $data['name'] }} ({{ $data['email'] }})</p>
            
            <p><strong>Message :</strong></p>
            <p>{{ $data['message'] }}</p>
        </div>
        
        <div class="footer">
            <p>Ce message a été envoyé via le formulaire de contact de NaturaCorp</p>
        </div>
    </div>
</body>
</html> 