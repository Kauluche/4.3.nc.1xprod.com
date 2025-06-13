<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation de commande NaturaCorp #{{ $order->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #4a5568; text-align: center;">Confirmation de votre commande</h1>
        
        <p>Bonjour,</p>
        
        <p>Nous vous confirmons la réception de votre commande n°{{ $order->id }}.</p>
        
        <h2 style="color: #4a5568; margin-top: 20px;">Détails de la commande</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background-color: #f7fafc;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #e2e8f0;">Produit</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;">Quantité</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;">Prix unitaire</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->product->name }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;">{{ $item->quantity }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;">{{ number_format($item->unit_price, 2) }} €</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;">{{ number_format($item->quantity * $item->unit_price, 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;"><strong>Total</strong></td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #e2e8f0;"><strong>{{ number_format($order->total, 2) }} €</strong></td>
                </tr>
            </tfoot>
        </table>

        <h2 style="color: #4a5568; margin-top: 20px;">Informations de livraison</h2>
        <p>
            {{ $order->pharmacy->name }}<br>
            {{ $order->pharmacy->address }}<br>
            {{ $order->pharmacy->postal_code }} {{ $order->pharmacy->city }}
        </p>
        
        <p>Nous vous tiendrons informé de l'avancement de votre commande.</p>
        
        <p>Cordialement,<br>L'équipe NaturaCorp</p>
        
        <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
            <p>© {{ date('Y') }} NaturaCorp. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html> 