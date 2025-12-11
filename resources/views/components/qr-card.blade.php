<div class="text-center p-4">
    <h2 class="text-xl font-bold mb-4">{{ $record->name }}</h2>
    
    @php
        use chillerlan\QRCode\QRCode;
        // Generamos la imagen en base64 directamente
        $qrImage = (new QRCode)->render($record->curp);
    @endphp

    <div class="flex justify-center mb-4">
        <img src="{{ $qrImage }}" alt="QR Code" style="width: 200px; height: 200px;" />
    </div>
    
    <p class="font-mono text-lg bg-gray-100 p-2 rounded">{{ $record->curp }}</p>
    
    <div class="mt-4 text-sm text-gray-500">
        Sistema Municipal de Gestión
    </div>
    
    <button onclick="window.print()" class="mt-6 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 no-print">
        Imprimir Ficha
    </button>
</div>

<style>
    @media print {
        .no-print, header, aside, .fi-topbar { display: none !important; }
        body { background: white; }
        .fi-modal-window { box-shadow: none; }
    }
</style>
