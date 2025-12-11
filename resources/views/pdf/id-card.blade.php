<style>
    @page { margin: 0; size: 90mm 55mm; } /* Tamaño Tarjeta */
    body { margin: 0; font-family: sans-serif; }
    
    .card-container {
        width: 100%; height: 100%;
        background: white;
        border: 1px solid #ddd;
        position: relative;
    }
    
    .header {
        background: #1e293b; /* Azul oscuro */
        height: 15mm;
        color: white;
        display: flex;
        align-items: center;
        padding-left: 5mm;
    }
    
    .logo { height: 10mm; float: left; margin-right: 3mm; }
    .title { font-size: 8pt; font-weight: bold; line-height: 15mm; }
    
    .content { padding: 5mm; }
    
    .photo-area {
        position: absolute;
        top: 20mm; right: 5mm;
        width: 25mm; height: 30mm;
        background: #eee;
        border: 1px solid #ccc;
    }
    
    .info { width: 55mm; font-size: 9pt; }
    .label { font-size: 6pt; color: #666; margin-top: 2mm; }
    .value { font-weight: bold; }
    
    .qr-code {
        position: absolute;
        bottom: 2mm; left: 2mm;
        width: 15mm;
    }
</style>

<div class="card-container">
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <div class="title">{{ $town_name }}</div>
    </div>
    
    <div class="content">
        <div class="info">
            <div class="label">NOMBRE</div>
            <div class="value">{{ $citizen->name }}</div>
            
            <div class="label">CURP</div>
            <div class="value" style="font-family: monospace">{{ $citizen->curp }}</div>
            
            <div class="label">COLONIA</div>
            <div class="value">{{ $citizen->neighborhood }}</div>
        </div>
        
        <div class="photo-area">
            </div>
        
        <div class="qr-code">
            <img src="data:image/png;base64,{{ base64_encode(QrCode::size(50)->generate($citizen->curp)) }}" width="100%">
        </div>
    </div>
</div>
