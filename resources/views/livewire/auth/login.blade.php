<div class="min-h-screen flex items-center justify-center bg-gray-100 font-['Space_Grotesk']">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-6">
        
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800">Sistem Monitoring</h1>
            <p class="text-gray-500 mt-2">Silakan login untuk melanjutkan</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-4">
            
            @if($error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $error }}
                </div>
            @endif

            <div>
                <label class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" wire:model="username" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Masukkan username">
                @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" wire:model="password" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Masukkan password">
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-transform active:scale-95 shadow-md">
                LOGIN
            </button>
        </form>

        <div class="text-center text-xs text-gray-400 mt-4">
            &copy; 2025 PT SUCOFINDO
        </div>
    </div>
</div>