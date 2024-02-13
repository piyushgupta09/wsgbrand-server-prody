<button class="btn text-secondary border-0" wire:click="toggleTheme" role="button">
    @if ($theme == 'light')
        <i class="bi bi-brightness-high-fill"></i>
    @else
        <i class="bi bi-moon-fill"></i>
    @endif
</button>
