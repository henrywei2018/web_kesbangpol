<x-filament-panels::page>
    <form wire:submit="saveSettings">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>