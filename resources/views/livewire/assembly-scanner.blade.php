<div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center p-4 relative overflow-hidden">
    
    <a href="/admin/assemblies" class="absolute top-4 right-4 z-50 bg-white/20 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-bold border border-white/30 hover:bg-white/30 transition">
        SALIR ✕
    </a>

    <div class="absolute top-4 left-4 z-40 text-white">
        <h1 class="font-bold text-lg leading-tight">{{ $assembly->title }}</h1>
        
        @if($assembly->status === 'in_progress')
            <div class="flex items-center gap-2 mt-1">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs text-green-400 font-mono font-bold">EN VIVO</span>
            </div>
        @elseif($assembly->status === 'completed')
            <div class="flex items-center gap-2 mt-1">
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                <span class="text-xs text-red-400 font-mono font-bold">FINALIZADA</span>
            </div>
        @else
            <div class="flex items-center gap-2 mt-1">
                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                <span class="text-xs text-yellow-400 font-mono font-bold">PENDIENTE</span>
            </div>
        @endif
    </div>

    <div class="relative w-full max-w-md aspect-[3/4] bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-700">
        
        {{-- 
            ZONA DE CÁMARA (Estática)
            Usamos wire:ignore para que Livewire NUNCA toque ni refresque este div.
            Esto evita que la cámara se reinicie o el botón aparezca de nuevo.
        --}}
        <div wire:ignore class="absolute inset-0 z-0">
            <div id="reader" class="w-full h-full object-cover"></div>
            
            <div id="start-screen" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-gray-900">
                <div class="p-5 bg-white/10 rounded-full mb-6 ring-1 ring-white/20">
                    <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <button id="btn-start-scanner" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-8 rounded-full shadow-lg shadow-blue-500/30 transform transition active:scale-95 text-lg w-64">
                    ACTIVAR CÁMARA
                </button>
                <p class="text-gray-400 text-xs mt-4">Requiere permisos de cámara</p>
            </div>

            <div class="absolute inset-0 border-2 border-white/10 pointer-events-none flex items-center justify-center">
                <div class="w-64 h-64 border-2 border-blue-400/30 rounded-xl relative">
                    <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-blue-500 -mt-0.5 -ml-0.5 rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-blue-500 -mt-0.5 -mr-0.5 rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-blue-500 -mb-0.5 -ml-0.5 rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-blue-500 -mb-0.5 -mr-0.5 rounded-br-lg"></div>
                </div>
            </div>
        </div>

        {{-- 
            ZONA DE ALERTAS (Dinámica)
            Esta parte SÍ es controlada por Livewire. Se sobrepone a la cámara.
        --}}
        @if($lastScan || $errorMessage)
            <div class="absolute inset-0 z-50 flex flex-col items-center justify-center bg-gray-900/95 backdrop-blur-md p-6 text-center animate-in fade-in zoom-in duration-200">
                
                @if($scanStatus === 'success')
                    <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mb-6 shadow-xl shadow-green-500/40 animate-bounce">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-green-400 font-bold text-xl uppercase tracking-widest mb-2">{{ $lastScan['type'] }}</h2>
                    <h3 class="text-white font-black text-3xl mb-2 leading-tight">{{ $lastScan['name'] }}</h3>
                    <div class="inline-block bg-gray-800 rounded px-3 py-1 mt-2">
                        <p class="text-gray-400 font-mono text-sm tracking-wider">{{ $lastScan['curp'] }}</p>
                    </div>
                    
                @elseif($scanStatus === 'warning')
                    <div class="w-20 h-20 bg-yellow-500 rounded-full flex items-center justify-center mb-6 shadow-xl shadow-yellow-500/40">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-yellow-400 font-bold text-xl uppercase mb-2">Atención</h2>
                    <p class="text-white text-lg px-4">{{ $errorMessage }}</p>

                @else
                    <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mb-6 shadow-xl shadow-red-500/40">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h2 class="text-red-400 font-bold text-xl uppercase mb-2">Error</h2>
                    <p class="text-white text-lg px-4">{{ $errorMessage }}</p>
                @endif

                <div class="absolute bottom-0 left-0 w-full h-2 bg-gray-800">
                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 origin-left" 
                         style="animation: progress 2.5s linear forwards;"></div>
                </div>

                {{-- Botón manual para cerrar la alerta antes si se desea --}}
                <button wire:click="resetScan" class="absolute top-4 right-4 text-gray-500 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
    </div>

    <div class="mt-6 w-full max-w-md z-40">
        <form wire:submit.prevent="handleScan($refs.manualInput.value)" class="flex gap-2 relative">
            <input type="text" x-ref="manualInput" class="block w-full pl-4 bg-gray-800 border-gray-700 text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 uppercase tracking-widest placeholder-gray-500" placeholder="ENTRADA MANUAL (CURP)">
            <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-6 rounded-lg font-bold transition">OK</button>
        </form>
    </div>

    <style>
        @keyframes progress { 0% { width: 100%; } 100% { width: 0%; } }
    </style>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const html5QrCode = new Html5Qrcode("reader");
            const startBtn = document.getElementById('btn-start-scanner');
            const startScreen = document.getElementById('start-screen');
            
            // VARIABLES DE CONTROL
            let isProcessing = false; 
            let lastDecodedText = null;    // Para evitar duplicados inmediatos
            let lastScanTime = 0;

            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 0.75
            };
            
            // --- AL DETECTAR QR ---
            const onScanSuccess = (decodedText, decodedResult) => {
                const now = Date.now();

                // 1. VALIDACIONES DE SEGURIDAD
                if (isProcessing) return; // Si ya estamos procesando uno, ignorar.

                // Filtro anti-rebote: Si es el mismo código y pasó menos de 5 segundos, ignorar.
                // Esto soluciona que si dejas la cámara puesta, te salga "Ya registrado" una y otra vez.
                if (decodedText === lastDecodedText && (now - lastScanTime) < 5000) {
                    return;
                }

                // 2. BLOQUEAR Y PAUSAR
                isProcessing = true;
                lastDecodedText = decodedText;
                lastScanTime = now;
                
                try { html5QrCode.pause(); } catch(e) {}

                // 3. ENVIAR A LIVEWIRE
                @this.handleScan(decodedText).then(() => {
                    // 4. ESPERAR 2.5 SEGUNDOS (Tiempo que dura la barra de progreso)
                    setTimeout(() => {
                        // Limpiar alerta visual
                        @this.resetScan();
                        
                        // Reanudar cámara
                        try { html5QrCode.resume(); } catch(e) {
                            console.log("Cámara reiniciada");
                        }
                        
                        // Desbloquear para siguiente lectura
                        isProcessing = false;
                        
                    }, 2500);
                });
            };

            // --- INICIAR CÁMARA ---
            startBtn.addEventListener('click', () => {
                startBtn.innerHTML = 'INICIANDO...';
                startBtn.disabled = true;
                
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    config, 
                    onScanSuccess
                ).then(() => {
                    // Ocultar pantalla de inicio manualmente (ya que está dentro de wire:ignore)
                    startScreen.style.display = 'none';
                }).catch(err => {
                    console.error(err);
                    startBtn.innerHTML = 'REINTENTAR';
                    startBtn.disabled = false;
                    alert("Error: " + err);
                });
            });

            Livewire.on('play-sound', (data) => {
                let audio = new Audio(
                    data.status === 'success' 
                    ? 'https://notificationsounds.com/storage/sounds/file-sounds-1150-pristine.mp3' 
                    : 'https://notificationsounds.com/storage/sounds/file-sounds-1148-juntos.mp3'
                );
                audio.play().catch(e => {});
            });
        });
    </script>
</div>