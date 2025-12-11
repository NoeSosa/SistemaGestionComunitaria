<x-filament-panels::page>
    <x-filament-panels::form wire:submit="generate">
        {{ $this->form }}
        
        <div class="mt-4">
            {{ $this->generateAction }}
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
