<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    
    <div class="max-w-7xl mx-auto mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-orange-400 to-red-600">
                Manage Users
            </h1>
            <p class="text-gray-400 text-sm mt-1">Kelola Akun & Hak Akses Pengguna</p>
        </div>
        
        <button wire:click="create" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold shadow-lg shadow-orange-500/20 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add User
        </button>
    </div>

    <div class="max-w-7xl mx-auto mb-4">
        @if (session()->has('message'))
            <div class="px-4 py-3 bg-green-500/10 border border-green-500/50 text-green-400 rounded-xl text-sm font-bold flex items-center gap-3">
                <i class="fa-solid fa-check-circle"></i> {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="px-4 py-3 bg-red-500/10 border border-red-500/50 text-red-400 rounded-xl text-sm font-bold flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="max-w-7xl mx-auto">
        
        <div class="mb-4 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau username..." 
                class="w-full md:w-1/3 bg-[#1a1d2d] border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:border-orange-500 focus:outline-none pl-10">
            <i class="fa-solid fa-search absolute left-3.5 top-3.5 text-gray-500"></i>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-[#11131f] text-gray-200 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">User Info</th>
                            <th class="px-6 py-4">Role / Level</th>
                            <th class="px-6 py-4">Cabang</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}&background=random" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="text-white font-bold">{{ $user->nama }}</p>
                                        <p class="text-xs text-gray-500">@ {{ $user->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    {{ $user->level }}
                                </span>
                                <div class="text-[10px] mt-1">{{ $user->position ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                {{ optional($user->cabang)->name_cabang ?? $user->group }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status == 'Active')
                                    <span class="text-green-400 flex items-center gap-1.5 font-bold text-xs">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                                    </span>
                                @else
                                    <span class="text-red-400 flex items-center gap-1.5 font-bold text-xs">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Non-Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit('{{ $user->username }}')" class="p-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-white transition-colors">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button wire:confirm="Yakin ingin menghapus user ini?" wire:click="delete('{{ $user->username }}')" class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-700">
                {{ $users->links() }} 
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-[#1a1d2d] w-full max-w-2xl rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
            
            <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center bg-[#11131f]">
                <h3 class="text-lg font-bold text-white">
                    {{ $isEditMode ? 'Edit User' : 'Add New User' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="store" class="p-6 max-h-[80vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="username" {{ $isEditMode ? 'readonly' : '' }} 
                               class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none {{ $isEditMode ? 'cursor-not-allowed opacity-60' : '' }}">
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nama" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                        @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Email</label>
                        <input type="email" wire:model="email" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Phone</label>
                        <input type="text" wire:model="phone" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Cabang (Group) <span class="text-red-500">*</span></label>
                        <select wire:model="group" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                            <option value="">Pilih Cabang...</option>
                            @foreach($listCabang as $cabang)
                                <option value="{{ $cabang->code_cabang }}">{{ $cabang->name_cabang }}</option>
                            @endforeach
                        </select>
                        @error('group') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Level User <span class="text-red-500">*</span></label>
                        <select wire:model="level" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                            <option value="">Pilih Level...</option>
                            @foreach($listLevel as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                        @error('level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Position / Jabatan</label>
                        <input type="text" wire:model="position" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Status Akun <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                            <option value="">Pilih Status...</option>
                            <option value="Active">Active</option>
                            <option value="Non Active">Non Active</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-700">
                        <h4 class="text-orange-400 font-bold text-sm mb-4">Pengaturan Password</h4>
                        @if($isEditMode)
                            <p class="text-xs text-gray-500 mb-2">*Kosongkan jika tidak ingin mengubah password</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">New Password</label>
                        <input type="password" wire:model="password" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Retype Password</label>
                        <input type="password" wire:model="password_confirmation" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 focus:border-orange-500 focus:outline-none">
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-xl bg-gray-700 text-white font-bold hover:bg-gray-600 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 text-white font-bold hover:from-orange-500 hover:to-red-500 shadow-lg shadow-orange-500/30 transition-all">
                        {{ $isEditMode ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>