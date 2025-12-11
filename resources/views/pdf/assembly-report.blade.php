<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Asamblea</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 12px; color: #555; }
        
        /* Cajas de Estadísticas */
        .stats-box { width: 100%; margin-bottom: 20px; border: 1px solid #ddd; background: #f9f9f9; padding: 10px; }
        .stat-item { display: inline-block; width: 30%; text-align: center; }
        .stat-value { font-size: 14px; font-weight: bold; }
        .stat-label { font-size: 9px; color: #666; text-transform: uppercase; }
        
        /* Tablas */
        h3 { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #eee; font-weight: bold; }
        
        /* Estados */
        .paid { color: green; font-weight: bold; }
        .debt { color: red; font-weight: bold; }
        .justified { color: #d97706; font-weight: bold; } /* Color Ámbar/Naranja */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE ASISTENCIA Y QUÓRUM</div>
        <div class="subtitle">{{ $assembly->title }} - {{ $assembly->date->format('d/m/Y') }}</div>
    </div>

    <div class="stats-box">
        <div class="stat-item">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Padrón Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $stats['present'] }}</div>
            <div class="stat-label">Asistentes ({{ $stats['percentage'] }}%)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $stats['absent'] }}</div>
            <div class="stat-label">Ausentes</div>
        </div>
        <div style="text-align: center; margin-top: 8px; font-weight: bold; color: {{ $stats['is_valid'] ? 'green' : 'red' }}">
            {{ $stats['is_valid'] ? 'QUÓRUM LEGAL ALCANZADO' : 'NO SE ALCANZÓ QUÓRUM' }}
        </div>
    </div>

    <h3>1. Ciudadanos Presentes ({{ count($attendees) }})</h3>
    <table>
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="50%">Nombre</th>
                <th width="20%">CURP</th>
                <th width="20%">Hora Entrada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendees as $index => $citizen)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $citizen->name }}</td>
                    <td>{{ $citizen->curp }}</td>
                    <td>{{ \Carbon\Carbon::parse($citizen->pivot->check_in_at)->format('H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <h3>2. Ciudadanos Ausentes ({{ count($absentees) }})</h3>
    <table>
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="50%">Nombre</th>
                <th width="20%">CURP</th>
                <th width="20%">Estado Multa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absentees as $index => $citizen)
                @php
                    $fine = $citizen->fines->first(); 
                    // Buscamos si tiene permiso
                    $justification = $citizen->justifications->first();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $citizen->name }}</td>
                    <td>{{ $citizen->curp }}</td>
                    <td align="center">
                        @if($fine)
                            <span class="paid">PAGADO</span><br>
                            <span style="font-size: 9px;">{{ \Carbon\Carbon::parse($fine->pivot->paid_at)->format('d/m/Y') }}</span>
                        
                        {{-- NUEVA CONDICIÓN: SI TIENE PERMISO --}}
                        @elseif($justification)
                            <span class="justified">JUSTIFICADO</span><br>
                            <span style="font-size: 9px; color: #666;">(Con Permiso)</span>
                        
                        @else
                            <span class="debt">ADEUDO</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" align="center">Todos asistieron. No hay multas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #999;">
        Documento generado el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
