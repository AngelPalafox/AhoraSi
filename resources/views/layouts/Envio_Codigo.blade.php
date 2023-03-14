<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('Bienvenido') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="verificacion" :value="__('Ingresa Codigo de Verificacion')" />
            <x-text-input id="verificacion" class="block mt-1 w-full" type="number" name="inputLogin" :value="old('inputLogin')"/>
        </div>

       
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-3">
                {{ __('Verificar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>