<x-filament::card>
    <div><h1 class="text-2xl font-bold tracking-tight">Bienvenido:</h1></div>
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <h2 class="text-2xl font-bold tracking-tight">
                  {{ $user->name }}
            </h2>
        </div>

        <form action="{{ route('filament.admin.auth.logout') }}" method="POST" class="flex-shrink-0">
            @csrf
           <button type="submit" class="filament-button btn btn-sm btn-light border-1 border-gray-800 rounded-md">
    Cerrar sesión
</button>
        </form>
    </div>
</x-filament::card>