<div class="min-h-screen bg-gray-50 p-8" wire:poll.5s>
    
    <div class="flex justify-between items-center mb-8 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $assembly->title }}</h1>
            <p class="text-gray-500 text-sm">Monitor de Asistencia en Tiempo Real</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-sm font-medium text-gray-600">Actualizando cada 5s</span>
            <a href="/admin/assemblies" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Volver al Panel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Asistentes Presentes</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-2">{{ $attendeesCount }}</h2>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-400">
                de {{ $totalCitizens }} ciudadanos activos
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 {{ $quorumPercentage >= 50 ? 'border-green-500' : 'border-yellow-500' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Porcentaje de Quórum</p>
                    <h2 class="text-4xl font-bold {{ $quorumPercentage >= 50 ? 'text-green-600' : 'text-yellow-600' }} mt-2">
                        {{ $quorumPercentage }}%
                    </h2>
                </div>
                <div class="p-3 {{ $quorumPercentage >= 50 ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }} rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-4">
                <div class="bg-{{ $quorumPercentage >= 50 ? 'green' : 'yellow' }}-500 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $quorumPercentage }}%"></div>
            </div>
            <div class="mt-2 text-xs text-right text-gray-500">
                Meta: 50% + 1
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Estado Asamblea</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-2 uppercase">{{ $assembly->status }}</h2>
                </div>
                <div class="p-3 bg-purple-50 rounded-full text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-400">
                Inició: {{ $assembly->date->format('H:i d/m/Y') }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Últimos Registros (En Vivo)</h3>
            <span class="text-xs bg-blue-100 text-blue-800 py-1 px-2 rounded-full">Mostrando últimos 10</span>
        </div>
        
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-sm uppercase tracking-wider">
                    <th class="px-6 py-3 font-medium">Hora</th>
                    <th class="px-6 py-3 font-medium">Nombre del Ciudadano</th>
                    <th class="px-6 py-3 font-medium">CURP</th>
                    <th class="px-6 py-3 font-medium">Estatus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recentArrivals as $citizen)
                    <tr class="hover:bg-gray-50 transition animate-in fade-in duration-500">
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                            {{ \Carbon\Carbon::parse($citizen->pivot->check_in_at)->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $citizen->name }}</div>
                            <div class="text-xs text-gray-500">{{ $citizen->neighborhood ?? 'Sin colonia' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                            {{ $citizen->curp }}
                        </td>
                        <td class="px-6 py-4">
                            @if($citizen->pivot->quorum_check_at)
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">Permanencia</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">Entrada</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            No hay registros aún. ¡Comienza a escanear!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
