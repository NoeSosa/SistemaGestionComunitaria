<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; font-weight: bold; margin-top: 20px; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $town_name }}</h1>
        <p>{{ $town_address }}</p>
        <h2>Corte de Caja General</h2>
        <p>Del: {{ $from }} Al: {{ $to }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Ciudadano</th>
                <th>Concepto</th>
                <th>Fecha Pago</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fines as $fine)
            <tr>
                <td>{{ $fine->id }}</td>
                <td>{{ $fine->citizen->name ?? 'N/A' }}</td>
                <td>{{ $fine->notes ?? 'Multa' }}</td>
                <td>{{ $fine->paid_at->format('d/m/Y') }}</td>
                <td>${{ number_format($fine->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Recaudado: ${{ number_format($total, 2) }}
    </div>
</body>
</html>
