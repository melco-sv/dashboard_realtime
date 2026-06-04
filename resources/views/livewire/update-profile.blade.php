<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Master User</h2>
            <p class="text-sm text-gray-400">Update Profile User</p>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-xl flex items-center gap-3 animate-pulse">
        <i class="fa-solid fa-circle-check"></i>
        <span class="font-medium">{{ session('message') }}</span>
    </div>
    @endif

    <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-xl p-6 shadow-2xl">
        <form wire:submit.prevent="update" class="space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Username</label>
                    <input type="text" wire:model="username" readonly disabled
                        class="w-full bg-[#11131f] text-gray-500 border border-gray-700 rounded-lg px-4 py-2.5 cursor-not-allowed focus:outline-none font-mono">
                    <p class="text-[10px] text-gray-600 italic">*Username tidak bisa diubah</p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-orange-500 uppercase tracking-wider">Name</label>
                    <input type="text" wire:model="nama" required
                        class="w-full bg-[#0b0c15] text-white border border-gray-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-4 py-2.5 transition-all"
                        placeholder="Masukkan nama lengkap">
                    @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-orange-500 uppercase tracking-wider">Email</label>
                    <input type="email" wire:model="email"
                        class="w-full bg-[#0b0c15] text-white border border-gray-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-4 py-2.5 transition-all"
                        placeholder="contoh@email.com">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-orange-500 uppercase tracking-wider">Phone</label>
                    <input type="text" wire:model="phone"
                        class="w-full bg-[#0b0c15] text-white border border-gray-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-4 py-2.5 transition-all"
                        placeholder="0812...">
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Level</label>
                    <div class="w-full bg-[#11131f] text-gray-500 border border-gray-700 rounded-lg px-4 py-2.5 flex items-center gap-2 cursor-not-allowed">
                        <i class="fa-solid fa-user-shield text-xs"></i>
                        {{ $level }}
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-300 uppercase tracking-wider">Position <span class="text-gray-600 lowercase font-normal">(opsional)</span></label>
                    <input type="text" wire:model="position"
                        class="w-full bg-[#0b0c15] text-white border border-gray-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg px-4 py-2.5 transition-all"
                        placeholder="Jabatan...">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kode Cabang</label>
                    <div class="w-full bg-[#11131f] text-gray-500 border border-gray-700 rounded-lg px-4 py-2.5 flex items-center gap-2 cursor-not-allowed">
                        <i class="fa-solid fa-building text-xs"></i>
                        {{ $code_cabang ?: '-' }}
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</label>
                    <div class="w-full bg-[#11131f] text-gray-500 border border-gray-700 rounded-lg px-4 py-2.5 flex items-center gap-2 cursor-not-allowed">
                        <i class="fa-solid fa-toggle-on text-green-600"></i>
                        {{ $status }}
                    </div>
                </div>

            </div>

            <div class="mt-8 pt-6 border-t border-gray-700/50">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-orange-500"></i> Change Password
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#0b0c15] p-4 rounded-xl border border-gray-800">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400">New Password</label>
                        <input type="password" wire:model="new_password"
                            class="w-full bg-[#1a1d2d] text-white border border-gray-700 focus:border-orange-500 rounded-lg px-4 py-2.5 transition-all"
                            placeholder="Isi jika ingin mengganti password">
                        @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400">Retype New Password</label>
                        <input type="password" wire:model="new_password_confirmation"
                            class="w-full bg-[#1a1d2d] text-white border border-gray-700 focus:border-orange-500 rounded-lg px-4 py-2.5 transition-all"
                            placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="flex items-center gap-2 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/20 transition-all transform hover:scale-105">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>