<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .logo {
            height: 80px;
        }
        .company-info {
            text-align: right;
        }
        .invoice-title {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .client-info {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="{{ public_path('images/vitrine/logo.png') }}" alt="NaturaCorp" class="logo">
        </div>
        <div class="company-info">
            <p>NaturaCorp</p>
            <p>123 Rue de la Santé</p>
            <p>75000 Paris</p>
            <p>Tél: 01 23 45 67 89</p>
            <p>Email: contact@naturacorp.com</p>
        </div>
    </div>

    <div class="invoice-title">FACTURE</div>

    <div class="client-info">
        <p><strong>Pharmacie:</strong> {{ $pharmacy->name }}</p>
        <p><strong>Adresse:</strong> {{ $pharmacy->address }}</p>
        <p><strong>Ville:</strong> {{ $pharmacy->city }}</p>
        <p><strong>Code postal:</strong> {{ $pharmacy->postal_code }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>N° Facture:</strong> {{ $order->id }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                    <td>{{ number_format($item->total, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total TTC: {{ number_format($order->total, 2, ',', ' ') }} €
    </div>

    <div class="footer">
        <p>NaturaCorp - RCS Paris B 123 456 789 - TVA Intracommunautaire FR 12 345 678 901</p>
        <p>IBAN: FR76 1234 5678 9012 3456 7890 123 - BIC: ABCD1234XXX</p>
    </div>
</body>
</html> 