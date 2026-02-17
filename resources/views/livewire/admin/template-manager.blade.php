<div>
    {{-- Flash Message --}}
    @if(session('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center justify-between"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center justify-between"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-red-500 hover:text-red-700">&times;</button>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Templates</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola template konten yang tersedia</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="openCreateModal"
                class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all hover:shadow-md"
                style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Tambah Template
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <div class="relative max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari template..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="--tw-ring-color: rgba(37, 99, 235, 0.3);">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Preview</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Slot Foto</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($templates as $index => $template)
                        <tr class="hover:bg-gray-50 transition-colors" wire:key="template-{{ $template->id }}">
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $templates->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="w-16 h-16 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center"
                                    style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%228%22 height=%228%22><rect width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/><rect x=%224%22 y=%224%22 width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/></svg>'); background-size: 8px 8px;">
                                    <img src="{{ asset('storage/' . $template->image) }}" alt="{{ $template->name }}" class="w-full h-full object-contain">
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $template->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $template->slots->count() }} foto
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleStatus({{ $template->id }})"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none"
                                    style="background: {{ $template->status === 'active' ? '#2563EB' : '#D1D5DB' }};">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 shadow-sm {{ $template->status === 'active' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="block text-xs mt-1 {{ $template->status === 'active' ? 'text-blue-600' : 'text-gray-400' }}">
                                    {{ $template->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="openEditModal({{ $template->id }})"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                                        style="color: #2563EB; background: rgba(37, 99, 235, 0.1);"
                                        onmouseover="this.style.background='rgba(37, 99, 235, 0.2)'"
                                        onmouseout="this.style.background='rgba(37, 99, 235, 0.1)'">
                                        Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $template->id }})"
                                        class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-400">Belum ada template.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($templates->hasPages())
        <div class="mt-4">
            {{ $templates->links() }}
        </div>
    @endif

    {{-- CREATE MODAL --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="document.body.style.overflow='hidden'" x-on:remove="document.body.style.overflow=''">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Tambah Template Baru</h2>
                <button wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="create" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                    <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Contoh: HUT RI ke-81">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Gambar (PNG)</label>
                    <div class="relative" x-data="{ dragging: false }">
                        <div class="border-2 border-dashed rounded-lg p-4 text-center transition-colors"
                            :class="dragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">

                            @if($imageFile)
                                <div class="mb-2">
                                    <div class="w-32 h-32 mx-auto rounded-lg border border-gray-200 overflow-hidden"
                                        style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%228%22 height=%228%22><rect width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/><rect x=%224%22 y=%224%22 width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/></svg>'); background-size: 8px 8px;">
                                        <img src="{{ $imageFile->temporaryUrl() }}" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">{{ $imageFile->getClientOriginalName() }}</p>
                                </div>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-500">Drag & drop atau klik untuk upload</p>
                                <p class="text-xs text-gray-400 mt-1">Format PNG, maksimal 5MB</p>
                            @endif

                            <input type="file" wire:model="imageFile" accept=".png" x-ref="fileInput"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                    <div wire:loading wire:target="imageFile" class="text-xs text-blue-600 mt-1">Mengupload...</div>
                    @error('imageFile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Interactive Template Editor --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Posisi Slot & Teks</label>
                        @if(count($slotConfigs) < 3)
                            <button type="button" wire:click="addSlot" class="text-xs font-medium px-3 py-1 rounded-lg transition-colors" style="color: #2563EB; background: rgba(37, 99, 235, 0.1);">
                                + Tambah Slot
                            </button>
                        @endif
                    </div>
                    @error('slotConfigs') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror

                    @if($imageFile)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200"
                        x-data="templateEditor()"
                        x-on:mousemove.window="onMouseMove($event)"
                        x-on:mouseup.window="onMouseUp()">

                        <p class="text-xs text-gray-500 mb-2">Drag elemen untuk mengatur posisi. Drag sudut kanan bawah slot untuk resize.</p>

                        <div class="relative mx-auto bg-white border border-gray-200 rounded-lg select-none" style="max-width: 500px;" x-ref="canvas">
                            <img src="{{ $imageFile->temporaryUrl() }}" class="w-full h-auto pointer-events-none" draggable="false"
                                style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%228%22 height=%228%22><rect width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/><rect x=%224%22 y=%224%22 width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/></svg>'); background-size: 8px 8px;">

                            {{-- Slot rectangles --}}
                            @foreach($slotConfigs as $i => $slot)
                            <div class="absolute border-2 border-dashed border-blue-500 bg-blue-500/10 cursor-move flex items-center justify-center group"
                                :style="getSlotStyle({{ $i }})"
                                @mousedown.prevent="startDrag('slot', {{ $i }}, $event)"
                                @touchstart.prevent="startDrag('slot', {{ $i }}, $event)">
                                <span class="text-xs font-bold text-blue-600 bg-white/80 px-1.5 py-0.5 rounded pointer-events-none">{{ $i + 1 }}</span>
                                <button type="button" wire:click="removeSlot({{ $i }})"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs leading-none flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-30"
                                    @mousedown.stop @touchstart.stop>&times;</button>
                                <div class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize z-20"
                                    @mousedown.prevent.stop="startDrag('resize', {{ $i }}, $event)"
                                    @touchstart.prevent.stop="startDrag('resize', {{ $i }}, $event)">
                                    <svg class="w-3 h-3 text-blue-600 absolute bottom-0.5 right-0.5" viewBox="0 0 6 6" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 1v4H1"/><path d="M5 3v2H3"/></svg>
                                </div>
                            </div>
                            @endforeach

                            {{-- Title label --}}
                            <div class="absolute cursor-move text-xs font-bold text-orange-600 bg-orange-100/90 px-1.5 py-0.5 rounded whitespace-nowrap z-20 select-none"
                                :style="getTitleStyle()"
                                @mousedown.prevent="startDrag('title', null, $event)"
                                @touchstart.prevent="startDrag('title', null, $event)">
                                Title
                            </div>

                            {{-- Caption label --}}
                            <div class="absolute cursor-move text-xs font-bold text-green-600 bg-green-100/90 px-1.5 py-0.5 rounded whitespace-nowrap z-20 select-none"
                                :style="getCaptionStyle()"
                                @mousedown.prevent="startDrag('caption', null, $event)"
                                @touchstart.prevent="startDrag('caption', null, $event)">
                                Caption
                            </div>
                        </div>

                        {{-- Legend --}}
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 border-2 border-dashed border-blue-500 inline-block"></span> Slot Foto</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-orange-100 border border-orange-300 inline-block rounded"></span> Title</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-100 border border-green-300 inline-block rounded"></span> Caption</span>
                        </div>
                    </div>
                    @else
                        <p class="text-xs text-gray-400 text-center py-3">Upload gambar template terlebih dahulu untuk mengatur posisi.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all hover:shadow-md" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                        <span wire:loading.remove wire:target="create">Simpan</span>
                        <span wire:loading wire:target="create">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- EDIT MODAL --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Edit Template</h2>
                <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="update" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                    <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Gambar (PNG)</label>

                    {{-- Current image --}}
                    @if($existingImage && !$imageFile)
                        <div class="mb-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                            <div class="w-32 h-32 mx-auto rounded-lg border border-gray-200 overflow-hidden"
                                style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%228%22 height=%228%22><rect width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/><rect x=%224%22 y=%224%22 width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/></svg>'); background-size: 8px 8px;">
                                <img src="{{ asset('storage/' . $existingImage) }}" class="w-full h-full object-contain">
                            </div>
                        </div>
                    @endif

                    <div class="relative" x-data="{ dragging: false }">
                        <div class="border-2 border-dashed rounded-lg p-4 text-center transition-colors"
                            :class="dragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                            x-on:dragover.prevent="dragging = true"
                            x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="dragging = false; $refs.editFileInput.files = $event.dataTransfer.files; $refs.editFileInput.dispatchEvent(new Event('change'))">

                            @if($imageFile)
                                <div class="mb-2">
                                    <div class="w-32 h-32 mx-auto rounded-lg border border-gray-200 overflow-hidden"
                                        style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%228%22 height=%228%22><rect width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/><rect x=%224%22 y=%224%22 width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/></svg>'); background-size: 8px 8px;">
                                        <img src="{{ $imageFile->temporaryUrl() }}" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">{{ $imageFile->getClientOriginalName() }}</p>
                                </div>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500">Upload gambar baru untuk mengganti</p>
                                <p class="text-xs text-gray-400 mt-1">Format PNG, maksimal 5MB</p>
                            @endif

                            <input type="file" wire:model="imageFile" accept=".png" x-ref="editFileInput"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                    <div wire:loading wire:target="imageFile" class="text-xs text-blue-600 mt-1">Mengupload...</div>
                    @error('imageFile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Interactive Template Editor --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Posisi Slot & Teks</label>
                        @if(count($slotConfigs) < 3)
                            <button type="button" wire:click="addSlot" class="text-xs font-medium px-3 py-1 rounded-lg transition-colors" style="color: #2563EB; background: rgba(37, 99, 235, 0.1);">
                                + Tambah Slot
                            </button>
                        @endif
                    </div>
                    @error('slotConfigs') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror

                    @php $previewImage = $imageFile ? $imageFile->temporaryUrl() : ($existingImage ? asset('storage/' . $existingImage) : null); @endphp
                    @if($previewImage)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200"
                        x-data="templateEditor()"
                        x-on:mousemove.window="onMouseMove($event)"
                        x-on:mouseup.window="onMouseUp()">

                        <p class="text-xs text-gray-500 mb-2">Drag elemen untuk mengatur posisi. Drag sudut kanan bawah slot untuk resize.</p>

                        <div class="relative mx-auto bg-white border border-gray-200 rounded-lg select-none" style="max-width: 500px;" x-ref="canvas">
                            <img src="{{ $previewImage }}" class="w-full h-auto pointer-events-none" draggable="false"
                                style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%228%22 height=%228%22><rect width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/><rect x=%224%22 y=%224%22 width=%224%22 height=%224%22 fill=%22%23f0f0f0%22/></svg>'); background-size: 8px 8px;">

                            {{-- Slot rectangles --}}
                            @foreach($slotConfigs as $i => $slot)
                            <div class="absolute border-2 border-dashed border-blue-500 bg-blue-500/10 cursor-move flex items-center justify-center group"
                                :style="getSlotStyle({{ $i }})"
                                @mousedown.prevent="startDrag('slot', {{ $i }}, $event)"
                                @touchstart.prevent="startDrag('slot', {{ $i }}, $event)">
                                <span class="text-xs font-bold text-blue-600 bg-white/80 px-1.5 py-0.5 rounded pointer-events-none">{{ $i + 1 }}</span>
                                <button type="button" wire:click="removeSlot({{ $i }})"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs leading-none flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-30"
                                    @mousedown.stop @touchstart.stop>&times;</button>
                                <div class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize z-20"
                                    @mousedown.prevent.stop="startDrag('resize', {{ $i }}, $event)"
                                    @touchstart.prevent.stop="startDrag('resize', {{ $i }}, $event)">
                                    <svg class="w-3 h-3 text-blue-600 absolute bottom-0.5 right-0.5" viewBox="0 0 6 6" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 1v4H1"/><path d="M5 3v2H3"/></svg>
                                </div>
                            </div>
                            @endforeach

                            {{-- Title label --}}
                            <div class="absolute cursor-move text-xs font-bold text-orange-600 bg-orange-100/90 px-1.5 py-0.5 rounded whitespace-nowrap z-20 select-none"
                                :style="getTitleStyle()"
                                @mousedown.prevent="startDrag('title', null, $event)"
                                @touchstart.prevent="startDrag('title', null, $event)">
                                Title
                            </div>

                            {{-- Caption label --}}
                            <div class="absolute cursor-move text-xs font-bold text-green-600 bg-green-100/90 px-1.5 py-0.5 rounded whitespace-nowrap z-20 select-none"
                                :style="getCaptionStyle()"
                                @mousedown.prevent="startDrag('caption', null, $event)"
                                @touchstart.prevent="startDrag('caption', null, $event)">
                                Caption
                            </div>
                        </div>

                        {{-- Legend --}}
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 border-2 border-dashed border-blue-500 inline-block"></span> Slot Foto</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-orange-100 border border-orange-300 inline-block rounded"></span> Title</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-100 border border-green-300 inline-block rounded"></span> Caption</span>
                        </div>
                    </div>
                    @else
                        <p class="text-xs text-gray-400 text-center py-3">Upload gambar template terlebih dahulu untuk mengatur posisi.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all hover:shadow-md" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                        <span wire:loading.remove wire:target="update">Perbarui</span>
                        <span wire:loading wire:target="update">Memperbarui...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- DELETE CONFIRM MODAL --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 z-10 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Template?</h3>
            <p class="text-sm text-gray-500 mb-6">Template dan file gambar akan dihapus permanen.</p>
            <div class="flex justify-center gap-3">
                <button wire:click="$set('showDeleteConfirm', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button wire:click="delete" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
    @endif

    @script
    <script>
        Alpine.data('templateEditor', () => ({
            dragType: null,
            dragIndex: null,
            dragX: 0,
            dragY: 0,
            dragW: 0,
            dragH: 0,
            startMouseX: 0,
            startMouseY: 0,
            origX: 0,
            origY: 0,
            origW: 0,
            origH: 0,
            canvasW: 0,
            canvasH: 0,

            init() {
                this._touchMoveHandler = (e) => {
                    if (!this.dragType) return;
                    e.preventDefault();
                    this._handleMove(e.touches[0].clientX, e.touches[0].clientY);
                };
                this._touchEndHandler = () => this.onMouseUp();
                document.addEventListener('touchmove', this._touchMoveHandler, { passive: false });
                document.addEventListener('touchend', this._touchEndHandler);
            },

            destroy() {
                document.removeEventListener('touchmove', this._touchMoveHandler);
                document.removeEventListener('touchend', this._touchEndHandler);
            },

            getSlotStyle(i) {
                if (this.dragIndex === i && (this.dragType === 'slot' || this.dragType === 'resize')) {
                    return `left:${this.dragX}%;top:${this.dragY}%;width:${this.dragW}%;height:${this.dragH}%`;
                }
                let s = this.$wire.slotConfigs[i];
                if (!s) return 'display:none';
                return `left:${s.x_percent}%;top:${s.y_percent}%;width:${s.width_percent}%;height:${s.height_percent}%`;
            },

            getTitleStyle() {
                if (this.dragType === 'title') {
                    return `left:${this.dragX}%;top:${this.dragY}%;transform:translateX(-50%)`;
                }
                return `left:${this.$wire.title_x_percent}%;top:${this.$wire.title_y_percent}%;transform:translateX(-50%)`;
            },

            getCaptionStyle() {
                if (this.dragType === 'caption') {
                    return `left:${this.dragX}%;top:${this.dragY}%;transform:translateX(-50%)`;
                }
                return `left:${this.$wire.caption_x_percent}%;top:${this.$wire.caption_y_percent}%;transform:translateX(-50%)`;
            },

            startDrag(type, index, e) {
                let canvas = this.$refs.canvas;
                let rect = canvas.getBoundingClientRect();
                let clientX = e.touches ? e.touches[0].clientX : e.clientX;
                let clientY = e.touches ? e.touches[0].clientY : e.clientY;

                this.dragType = type;
                this.dragIndex = index;
                this.startMouseX = clientX;
                this.startMouseY = clientY;
                this.canvasW = rect.width;
                this.canvasH = rect.height;

                if (type === 'slot' || type === 'resize') {
                    let s = this.$wire.slotConfigs[index];
                    this.origX = parseFloat(s.x_percent);
                    this.origY = parseFloat(s.y_percent);
                    this.origW = parseFloat(s.width_percent);
                    this.origH = parseFloat(s.height_percent);
                    this.dragX = this.origX;
                    this.dragY = this.origY;
                    this.dragW = this.origW;
                    this.dragH = this.origH;
                } else if (type === 'title') {
                    this.origX = parseFloat(this.$wire.title_x_percent);
                    this.origY = parseFloat(this.$wire.title_y_percent);
                    this.dragX = this.origX;
                    this.dragY = this.origY;
                } else if (type === 'caption') {
                    this.origX = parseFloat(this.$wire.caption_x_percent);
                    this.origY = parseFloat(this.$wire.caption_y_percent);
                    this.dragX = this.origX;
                    this.dragY = this.origY;
                }
            },

            onMouseMove(e) {
                if (!this.dragType) return;
                this._handleMove(e.clientX, e.clientY);
            },

            _handleMove(clientX, clientY) {
                let dx = ((clientX - this.startMouseX) / this.canvasW) * 100;
                let dy = ((clientY - this.startMouseY) / this.canvasH) * 100;

                if (this.dragType === 'slot') {
                    this.dragX = Math.max(0, Math.min(100 - this.origW, this.origX + dx));
                    this.dragY = Math.max(0, Math.min(100 - this.origH, this.origY + dy));
                    this.dragW = this.origW;
                    this.dragH = this.origH;
                } else if (this.dragType === 'resize') {
                    this.dragX = this.origX;
                    this.dragY = this.origY;
                    this.dragW = Math.max(5, Math.min(100 - this.origX, this.origW + dx));
                    this.dragH = Math.max(5, Math.min(100 - this.origY, this.origH + dy));
                } else if (this.dragType === 'title' || this.dragType === 'caption') {
                    this.dragX = Math.max(0, Math.min(100, this.origX + dx));
                    this.dragY = Math.max(0, Math.min(100, this.origY + dy));
                }
            },

            onMouseUp() {
                if (!this.dragType) return;
                let r = (v) => Math.round(v * 10) / 10;

                if (this.dragType === 'slot') {
                    this.$wire.set('slotConfigs.' + this.dragIndex + '.x_percent', r(this.dragX));
                    this.$wire.set('slotConfigs.' + this.dragIndex + '.y_percent', r(this.dragY));
                } else if (this.dragType === 'resize') {
                    this.$wire.set('slotConfigs.' + this.dragIndex + '.width_percent', r(this.dragW));
                    this.$wire.set('slotConfigs.' + this.dragIndex + '.height_percent', r(this.dragH));
                } else if (this.dragType === 'title') {
                    this.$wire.set('title_x_percent', r(this.dragX));
                    this.$wire.set('title_y_percent', r(this.dragY));
                } else if (this.dragType === 'caption') {
                    this.$wire.set('caption_x_percent', r(this.dragX));
                    this.$wire.set('caption_y_percent', r(this.dragY));
                }

                this.dragType = null;
                this.dragIndex = null;
            },
        }));
    </script>
    @endscript
</div>
