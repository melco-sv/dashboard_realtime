<div class="min-h-screen flex items-center justify-center bg-[#f0f2f5] font-['Space_Grotesk'] relative overflow-hidden">

    <div class="absolute top-0 left-0 w-full h-1/2 bg-[#0f172a]"></div>
    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-blue-900/20 to-transparent pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md p-6">

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">

            <div class="pt-8 pb-6 px-8 text-center bg-white">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('assets/logo-sucofindo.png') }}"
                        alt="Logo SUCOFINDO"
                        class="h-16 w-auto object-contain drop-shadow-sm hover:scale-105 transition-transform duration-300">
                </div>

                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Sistem Monitoring</h2>
                <p class="text-gray-500 text-sm mt-2">Selamat datang, silakan masuk ke akun Anda</p>
            </div>

            <div class="p-8 pt-2">
                <form wire:submit.prevent="login" class="space-y-5">

                    @error('username')
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm flex items-start gap-3 animate-fade-in-down">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="font-bold text-sm">Gagal Login</p>
                            <p class="text-xs">{{ $message }}</p>
                        </div>
                    </div>
                    @enderror

                    <div class="group">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wide mb-2 group-focus-within:text-blue-600 transition-colors">
                            Username
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input type="text" wire:model="username"
                                class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 @error('username') border-red-500 bg-red-50 @enderror"
                                placeholder="Masukkan ID Pengguna">
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wide mb-2 group-focus-within:text-blue-600 transition-colors">
                            Password
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input type="password" wire:model="password"
                                class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-300 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 @error('password') border-red-500 bg-red-50 @enderror"
                                placeholder="Masukkan Kata Sandi">
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-[#0056b3] hover:bg-[#004494] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">

                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>

                            <span wire:loading.remove>MASUK</span>
                        </button>
                    </div>

                </form>
            </div>

            <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-center">
                <p class="text-xs text-gray-400 font-medium">
                    &copy; {{ date('Y') }} PT SUCOFINDO (Persero). All rights reserved.
                </p>
            </div>

        </div>
    </div>
</div>