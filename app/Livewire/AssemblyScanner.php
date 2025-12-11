<?php

namespace App\Livewire;

use App\Models\Assembly;
use App\Models\Attendance;
use App\Models\Citizen;
use Livewire\Component;
use Livewire\Attributes\Layout;

class AssemblyScanner extends Component
{
    public Assembly $assembly;
    public $lastScan = null;     // Guardará info del último escaneo para mostrar en pantalla
    public $scanStatus = '';     // 'success' o 'error'
    public $errorMessage = '';

    // Este método se ejecuta al cargar la página (recibe el ID de la url)
    // Este método se ejecuta al cargar la página (recibe el ID de la url)
    public function mount(Assembly $assembly)
    {
        // 1. Verificar si la asamblea está activa
        $this->assembly = $assembly;
        if ($this->assembly->status !== 'in_progress') {
            abort(403, 'Esta asamblea no está activa.');
        }

        // 2. NUEVO: Verificar Permisos de Usuario
        $user = auth()->user();
        
        // Si NO es super admin Y NO tiene rol de escaneador, fuera.
        if (! $user->hasRole(['super_admin', 'escaneador'])) {
            abort(403, 'No tienes permiso para operar el escáner.');
        }
    }

    // Esta función será llamada desde Javascript cuando la cámara lea un QR
    public function handleScan($curp)
    {
        // 1. Limpiar errores previos
        $this->reset(['errorMessage', 'scanStatus', 'lastScan']);
        
        // 2. Buscar ciudadano
        $citizen = Citizen::where('curp', $curp)->first();

        if (!$citizen) {
            $this->scanStatus = 'error';
            $this->errorMessage = "El CURP {$curp} no está registrado en el padrón.";
            return;
        }

        if (!$citizen->is_active) {
            $this->scanStatus = 'error';
            $this->errorMessage = "El ciudadano {$citizen->name} está dado de baja.";
            return;
        }

        // 3. Registrar o Actualizar Asistencia
        // Buscamos si ya tiene registro en ESTA asamblea
        $attendance = Attendance::where('assembly_id', $this->assembly->id)
            ->where('citizen_id', $citizen->id)
            ->first();

        if (!$attendance) {
            // CASO A: PRIMERA VEZ (Entrada)
            Attendance::create([
                'assembly_id' => $this->assembly->id,
                'citizen_id' => $citizen->id,
                'check_in_at' => now(),
            ]);
            
            $this->scanStatus = 'success';
            $this->lastScan = [
                'name' => $citizen->name,
                'curp' => $citizen->curp,
                'type' => 'ENTRADA REGISTRADA',
                'time' => now()->format('H:i:s'),
                'is_update' => false, // Para pintar de verde
            ];
        } else {
            // CASO B: SEGUNDA VEZ (Confirmar Permanencia)
            // Solo actualizamos si pasó al menos 1 minuto desde el último registro (para evitar doble escaneo accidental)
            if ($attendance->updated_at->diffInMinutes(now()) < 1) {
                $this->scanStatus = 'warning';
                $this->errorMessage = "Ya registrado hace un momento.";
                return;
            }

            $attendance->update([
                'quorum_check_at' => now(),
            ]);

            $this->scanStatus = 'success';
            $this->lastScan = [
                'name' => $citizen->name,
                'curp' => $citizen->curp,
                'type' => 'PERMANENCIA CONFIRMADA',
                'time' => now()->format('H:i:s'),
                'is_update' => true, // Para pintar de azul
            ];
        }
        
        // Reproducir sonido (enviamos evento al navegador)
        $this->dispatch('play-sound', status: $this->scanStatus);
    }

    // Método para limpiar la pantalla desde Javascript
    public function resetScan()
    {
        $this->reset(['lastScan', 'scanStatus', 'errorMessage']);
    }

    #[Layout('components.layouts.app')] 
    public function render()
    {
        return view('livewire.assembly-scanner');
    }
}
