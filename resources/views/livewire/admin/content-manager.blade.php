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
            <h1 class="text-2xl font-bold text-gray-900">Kelola Konten</h1>
            <p class="text-sm text-gray-500 mt-1">Lihat, edit, dan approve konten dari semua user</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="relative flex-1 max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul atau nama user..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="--tw-ring-color: rgba(37, 99, 235, 0.3);">
        </div>
        <select wire:model.live="statusFilter" class="px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent" style="--tw-ring-color: rgba(37, 99, 235, 0.3);">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Preview</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Template</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contents as $index => $content)
                        <tr class="hover:bg-gray-50 transition-colors" wire:key="content-{{ $content->id }}">
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $contents->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="w-14 h-14 rounded-lg border border-gray-200 overflow-hidden bg-gray-100 relative" style="aspect-ratio: 1/1;">
                                    @if($content->final_image)
                                        <img src="{{ asset('storage/' . $content->final_image) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{-- CSS composite preview --}}
                                        @foreach($content->template->slots as $slot)
                                            @php $img = $content->images->where('slot_number', $slot->slot_number)->first(); @endphp
                                            @if($img)
                                                <div class="absolute overflow-hidden" style="left:{{ $slot->x_percent }}%;top:{{ $slot->y_percent }}%;width:{{ $slot->width_percent }}%;height:{{ $slot->height_percent }}%;">
                                                    <img src="{{ asset('storage/' . $img->image) }}" alt="" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                        @endforeach
                                        <img src="{{ asset('storage/' . $content->template->image) }}" alt="" class="absolute inset-0 w-full h-full object-contain" style="z-index: 10;">
                                        @if($content->status === 'draft')
                                            <div class="absolute inset-0 flex items-center justify-center" style="z-index: 15;">
                                                <span class="text-[6px] font-extrabold text-white/50 uppercase tracking-wider" style="transform: rotate(-30deg);">DRAFT</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900 truncate block max-w-[200px]">{{ $content->title }}</span>
                                @if($content->caption)
                                    <span class="text-xs text-gray-400 truncate block max-w-[200px]">{{ $content->caption }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">{{ $content->user->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">{{ $content->template->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($content->status === 'published')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Published</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-400">{{ $content->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="showDetail({{ $content->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <button wire:click="openEditModal({{ $content->id }})" class="p-1.5 rounded-lg hover:bg-blue-50 transition-colors" style="color: #2563EB;" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    @if($content->status === 'draft')
                                        <button wire:click="approve({{ $content->id }})" wire:confirm="Approve dan publikasikan konten ini?" class="p-1.5 rounded-lg text-green-500 hover:text-green-700 hover:bg-green-50 transition-colors" title="Approve">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </button>
                                    @else
                                        <a href="{{ asset('storage/' . $content->final_image) }}" download="{{ Str::slug($content->title) }}.png" class="p-1.5 rounded-lg text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition-colors" title="Download">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                        <button wire:click="unpublish({{ $content->id }})" wire:confirm="Kembalikan konten ini ke draft?" class="p-1.5 rounded-lg text-yellow-500 hover:text-yellow-700 hover:bg-yellow-50 transition-colors" title="Unpublish">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                        </button>
                                    @endif
                                    <button wire:click="confirmDelete({{ $content->id }})" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm text-gray-400">Belum ada konten.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($contents->hasPages())
        <div class="mt-4">
            {{ $contents->links() }}
        </div>
    @endif

    {{-- DETAIL MODAL --}}
    @if($showDetailModal && $viewingContent)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Detail Konten</h2>
                <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Twibbon Preview --}}
                <div class="relative mx-auto bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 1/1;">
                    @if($viewingContent->final_image)
                        <img src="{{ asset('storage/' . $viewingContent->final_image) }}" alt="" class="w-full h-full object-contain">
                    @else
                        @foreach($viewingContent->template->slots as $slot)
                            @php $img = $viewingContent->images->where('slot_number', $slot->slot_number)->first(); @endphp
                            @if($img)
                                <div class="absolute overflow-hidden"
                                    style="left: {{ $slot->x_percent }}%; top: {{ $slot->y_percent }}%; width: {{ $slot->width_percent }}%; height: {{ $slot->height_percent }}%;">
                                    <img src="{{ asset('storage/' . $img->image) }}" alt=""
                                        class="w-full h-full object-cover"
                                        style="transform: translate({{ $img->offset_x }}px, {{ $img->offset_y }}px) scale({{ $img->scale }}); transform-origin: center center;">
                                </div>
                            @endif
                        @endforeach
                        <img src="{{ asset('storage/' . $viewingContent->template->image) }}" class="absolute inset-0 w-full h-full object-contain pointer-events-none" style="z-index: 10;">
                        @if($viewingContent->title)
                            <div class="absolute pointer-events-none truncate max-w-[90%] text-center" style="left: {{ $viewingContent->title_x_percent }}%; top: {{ $viewingContent->title_y_percent }}%; transform: translateX(-50%); z-index: 20; font-family: {{ $viewingContent->title_font_family }}; font-size: {{ $viewingContent->title_font_size * 0.5 }}px; font-weight: {{ $viewingContent->title_font_bold ? 'bold' : 'normal' }}; font-style: {{ $viewingContent->title_font_italic ? 'italic' : 'normal' }}; text-decoration: {{ $viewingContent->title_font_underline ? 'underline' : 'none' }}; color: {{ $viewingContent->title_font_color }};">{{ $viewingContent->title }}</div>
                        @endif
                        @if($viewingContent->caption)
                            <div class="absolute pointer-events-none max-w-[90%] text-center" style="left: {{ $viewingContent->caption_x_percent }}%; top: {{ $viewingContent->caption_y_percent }}%; transform: translateX(-50%); z-index: 20; font-family: {{ $viewingContent->caption_font_family }}; font-size: {{ $viewingContent->caption_font_size * 0.5 }}px; font-weight: {{ $viewingContent->caption_font_bold ? 'bold' : 'normal' }}; font-style: {{ $viewingContent->caption_font_italic ? 'italic' : 'normal' }}; text-decoration: {{ $viewingContent->caption_font_underline ? 'underline' : 'none' }}; color: {{ $viewingContent->caption_font_color }}; white-space: pre-line;">{{ $viewingContent->caption }}</div>
                        @endif
                        @if($viewingContent->status === 'draft')
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center overflow-hidden" style="z-index: 20;">
                                <span class="text-5xl font-extrabold text-white/40 uppercase tracking-widest select-none" style="transform: rotate(-30deg);">DRAFT</span>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">User</p>
                        <p class="text-sm font-medium text-gray-900">{{ $viewingContent->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Template</p>
                        <p class="text-sm font-medium text-gray-900">{{ $viewingContent->template->name }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Judul</p>
                    <p class="text-sm font-medium text-gray-900">{{ $viewingContent->title }}</p>
                </div>

                @if($viewingContent->caption)
                <div>
                    <p class="text-xs text-gray-500">Caption</p>
                    <p class="text-sm text-gray-700">{{ $viewingContent->caption }}</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $viewingContent->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $viewingContent->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dibuat</p>
                        <p class="text-sm text-gray-700">{{ $viewingContent->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Font Title</p>
                    <p class="text-sm text-gray-700">{{ $viewingContent->title_font_family }} {{ $viewingContent->title_font_size }}px
                        {{ $viewingContent->title_font_bold ? 'Bold' : '' }}
                        {{ $viewingContent->title_font_italic ? 'Italic' : '' }}
                        {{ $viewingContent->title_font_underline ? 'Underline' : '' }}
                        <span class="inline-block w-3 h-3 rounded-full border border-gray-300 align-middle" style="background: {{ $viewingContent->title_font_color }};"></span>
                    </p>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <div class="flex gap-2">
                    @if($viewingContent->status === 'draft')
                        <button wire:click="approve({{ $viewingContent->id }})" wire:confirm="Approve dan publikasikan konten ini?" class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                            Approve
                        </button>
                    @else
                        <a href="{{ asset('storage/' . $viewingContent->final_image) }}" download="{{ Str::slug($viewingContent->title) }}.png" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Download
                        </a>
                        <button wire:click="unpublish({{ $viewingContent->id }})" wire:confirm="Kembalikan ke draft?" class="px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors">
                            Unpublish
                        </button>
                    @endif
                </div>
                <button wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- EDIT MODAL --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 z-10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Edit Konten</h2>
                <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="update" class="space-y-4">
                {{-- Template info (read-only) --}}
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500">Template: <span class="font-medium text-gray-700">{{ $templates->find($template_id)?->name }}</span></p>
                </div>

                {{-- Twibbon Editor --}}
                @if(count($templateSlots) > 0)
                    @php $editTemplate = $templates->find($template_id); @endphp
                    @if($editTemplate)
                    <div x-data="twibbonEditor()" class="space-y-4">
                        {{-- Preview Area --}}
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">Preview Twibbon:</p>
                            <div x-ref="canvas" class="relative mx-auto bg-white border border-gray-200 rounded-lg overflow-hidden" style="max-width: 400px; aspect-ratio: 1/1;"
                                @mousemove="onDrag($event)" @mouseup="stopDrag()" @mouseleave="stopDrag()"
                                @touchmove.prevent="onTouchDrag($event)" @touchend="stopDrag()">

                                @foreach($templateSlots as $si => $slot)
                                    <div class="absolute overflow-hidden cursor-move"
                                        style="left: {{ $slot['x_percent'] }}%; top: {{ $slot['y_percent'] }}%; width: {{ $slot['width_percent'] }}%; height: {{ $slot['height_percent'] }}%; z-index: 5;"
                                        @mousedown.prevent="startDrag({{ $slot['slot_number'] }}, $event)"
                                        @touchstart.prevent="startTouchDrag({{ $slot['slot_number'] }}, $event)">

                                        @if(isset($slotFiles[$slot['slot_number']]) && $slotFiles[$slot['slot_number']])
                                            <img src="{{ $slotFiles[$slot['slot_number']]->temporaryUrl() }}"
                                                class="w-full h-full object-cover pointer-events-none"
                                                :style="`transform: translate(${transforms[{{ $slot['slot_number'] }}]?.offset_x || 0}px, ${transforms[{{ $slot['slot_number'] }}]?.offset_y || 0}px) scale(${transforms[{{ $slot['slot_number'] }}]?.scale || 1}); transform-origin: center center;`">
                                        @elseif(isset($existingImages[$slot['slot_number']]))
                                            <img src="{{ asset('storage/' . $existingImages[$slot['slot_number']]) }}"
                                                class="w-full h-full object-cover pointer-events-none"
                                                :style="`transform: translate(${transforms[{{ $slot['slot_number'] }}]?.offset_x || 0}px, ${transforms[{{ $slot['slot_number'] }}]?.offset_y || 0}px) scale(${transforms[{{ $slot['slot_number'] }}]?.scale || 1}); transform-origin: center center;`">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center border-2 border-dashed border-gray-300">
                                                <p class="text-xs text-gray-400">Foto {{ $slot['slot_number'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                <img src="{{ asset('storage/' . $editTemplate->image) }}"
                                    class="absolute inset-0 w-full h-full object-contain pointer-events-none" style="z-index: 10;">
                                {{-- Title text (draggable) --}}
                                <div class="absolute cursor-move truncate max-w-[90%] text-center select-none"
                                    :style="{left: (textDragType === 'title' ? textDragX : {{ $titleXPercent }}) + '%', top: (textDragType === 'title' ? textDragY : {{ $titleYPercent }}) + '%', transform: 'translateX(-50%)', zIndex: 25, padding: '2px 4px', fontFamily: '{{ $titleFontFamily }}', fontSize: '{{ $titleFontSize * 0.6 }}px', fontWeight: '{{ $titleFontBold ? "bold" : "normal" }}', fontStyle: '{{ $titleFontItalic ? "italic" : "normal" }}', textDecoration: '{{ $titleFontUnderline ? "underline" : "none" }}', color: '{{ $titleFontColor }}'}"
                                    @mousedown.prevent.stop="startTextDrag('title', {{ $titleXPercent }}, {{ $titleYPercent }}, $event)"
                                    @touchstart.prevent.stop="startTextTouchDrag('title', {{ $titleXPercent }}, {{ $titleYPercent }}, $event)">{{ $title ?: 'Title' }}</div>
                                {{-- Caption text (draggable) --}}
                                <div class="absolute cursor-move max-w-[90%] text-center select-none" style="white-space: pre-line;"
                                    :style="{left: (textDragType === 'caption' ? textDragX : {{ $captionXPercent }}) + '%', top: (textDragType === 'caption' ? textDragY : {{ $captionYPercent }}) + '%', transform: 'translateX(-50%)', zIndex: 25, padding: '2px 4px', fontFamily: '{{ $captionFontFamily }}', fontSize: '{{ $captionFontSize * 0.6 }}px', fontWeight: '{{ $captionFontBold ? "bold" : "normal" }}', fontStyle: '{{ $captionFontItalic ? "italic" : "normal" }}', textDecoration: '{{ $captionFontUnderline ? "underline" : "none" }}', color: '{{ $captionFontColor }}'}"
                                    @mousedown.prevent.stop="startTextDrag('caption', {{ $captionXPercent }}, {{ $captionYPercent }}, $event)"
                                    @touchstart.prevent.stop="startTextTouchDrag('caption', {{ $captionXPercent }}, {{ $captionYPercent }}, $event)">{{ $caption ?: 'Caption' }}</div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Drag teks title/caption untuk mengubah posisi.</p>
                        </div>

                        {{-- Slot Controls --}}
                        <div class="space-y-3">
                            @foreach($templateSlots as $si => $slot)
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700">Foto Slot {{ $slot['slot_number'] }}</span>
                                        <button type="button" @click="resetTransform({{ $slot['slot_number'] }})" class="text-xs text-gray-500 hover:text-gray-700">Reset posisi</button>
                                    </div>

                                    <div class="relative mb-2">
                                        <label class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span>
                                                @if(isset($slotFiles[$slot['slot_number']]) && $slotFiles[$slot['slot_number']])
                                                    {{ $slotFiles[$slot['slot_number']]->getClientOriginalName() }}
                                                @elseif(isset($existingImages[$slot['slot_number']]))
                                                    Ganti foto...
                                                @else
                                                    Pilih foto...
                                                @endif
                                            </span>
                                            <input type="file" wire:model="slotFiles.{{ $slot['slot_number'] }}" accept="image/jpeg,image/png" class="hidden">
                                        </label>
                                        <div wire:loading wire:target="slotFiles.{{ $slot['slot_number'] }}" class="text-xs text-blue-600 mt-1">Uploading...</div>
                                        @error("slotFiles.{$slot['slot_number']}") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    @if((isset($slotFiles[$slot['slot_number']]) && $slotFiles[$slot['slot_number']]) || isset($existingImages[$slot['slot_number']]))
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500">Zoom:</span>
                                            <input type="range" min="0.5" max="3" step="0.1"
                                                :value="transforms[{{ $slot['slot_number'] }}]?.scale || 1"
                                                @input="updateScale({{ $slot['slot_number'] }}, $event.target.value)"
                                                class="flex-1 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                            <span class="text-xs text-gray-500 w-8 text-right" x-text="(transforms[{{ $slot['slot_number'] }}]?.scale || 1).toFixed(1) + 'x'"></span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                    <input type="text" wire:model.live="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caption <span class="text-gray-400">(opsional, maks 300 karakter)</span></label>
                    <textarea wire:model.live="caption" rows="3" maxlength="300" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"></textarea>
                    @error('caption') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Font Settings --}}
                <div class="space-y-3">
                    {{-- Title Font --}}
                    <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <span class="text-xs font-semibold text-orange-700 mb-2 block">Pengaturan Font Title</span>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Font</label>
                                <select wire:model.live="titleFontFamily" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="Arial">Arial</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Trebuchet MS">Trebuchet MS</option>
                                    <option value="Impact">Impact</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Ukuran (px)</label>
                                <input type="number" wire:model.live="titleFontSize" min="10" max="72" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$toggle('titleFontBold')"
                                class="px-3 py-1.5 text-xs font-bold rounded border transition-colors {{ $titleFontBold ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">B</button>
                            <button type="button" wire:click="$toggle('titleFontItalic')"
                                class="px-3 py-1.5 text-xs italic rounded border transition-colors {{ $titleFontItalic ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">I</button>
                            <button type="button" wire:click="$toggle('titleFontUnderline')"
                                class="px-3 py-1.5 text-xs underline rounded border transition-colors {{ $titleFontUnderline ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">U</button>
                            <div class="ml-auto flex items-center gap-1">
                                <label class="text-xs text-gray-500">Warna:</label>
                                <input type="color" wire:model.live="titleFontColor" class="w-7 h-7 rounded border border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    {{-- Caption Font --}}
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <span class="text-xs font-semibold text-green-700 mb-2 block">Pengaturan Font Caption</span>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Font</label>
                                <select wire:model.live="captionFontFamily" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-green-500">
                                    <option value="Arial">Arial</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Trebuchet MS">Trebuchet MS</option>
                                    <option value="Impact">Impact</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Ukuran (px)</label>
                                <input type="number" wire:model.live="captionFontSize" min="10" max="72" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-green-500">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$toggle('captionFontBold')"
                                class="px-3 py-1.5 text-xs font-bold rounded border transition-colors {{ $captionFontBold ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">B</button>
                            <button type="button" wire:click="$toggle('captionFontItalic')"
                                class="px-3 py-1.5 text-xs italic rounded border transition-colors {{ $captionFontItalic ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">I</button>
                            <button type="button" wire:click="$toggle('captionFontUnderline')"
                                class="px-3 py-1.5 text-xs underline rounded border transition-colors {{ $captionFontUnderline ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">U</button>
                            <div class="ml-auto flex items-center gap-1">
                                <label class="text-xs text-gray-500">Warna:</label>
                                <input type="color" wire:model.live="captionFontColor" class="w-7 h-7 rounded border border-gray-300 cursor-pointer">
                            </div>
                        </div>
                    </div>
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
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Konten?</h3>
            <p class="text-sm text-gray-500 mb-6">Konten dan semua foto akan dihapus permanen.</p>
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
</div>

@script
<script>
    Alpine.data('twibbonEditor', () => ({
        transforms: @json($slotTransforms ?? []),
        isDragging: false,
        dragSlot: null,
        startX: 0,
        startY: 0,
        startOffsetX: 0,
        startOffsetY: 0,

        // Text drag state
        textDragType: null,
        textDragX: 0,
        textDragY: 0,
        textStartMouseX: 0,
        textStartMouseY: 0,
        textOrigX: 0,
        textOrigY: 0,

        startDrag(slot, event) {
            this.isDragging = true;
            this.dragSlot = slot;
            this.startX = event.clientX;
            this.startY = event.clientY;
            this.startOffsetX = this.transforms[slot]?.offset_x || 0;
            this.startOffsetY = this.transforms[slot]?.offset_y || 0;
        },

        startTouchDrag(slot, event) {
            if (event.touches.length !== 1) return;
            this.isDragging = true;
            this.dragSlot = slot;
            this.startX = event.touches[0].clientX;
            this.startY = event.touches[0].clientY;
            this.startOffsetX = this.transforms[slot]?.offset_x || 0;
            this.startOffsetY = this.transforms[slot]?.offset_y || 0;
        },

        startTextDrag(type, origX, origY, event) {
            this.textDragType = type;
            this.textOrigX = origX;
            this.textOrigY = origY;
            this.textDragX = origX;
            this.textDragY = origY;
            this.textStartMouseX = event.clientX;
            this.textStartMouseY = event.clientY;
        },

        startTextTouchDrag(type, origX, origY, event) {
            if (event.touches.length !== 1) return;
            this.textDragType = type;
            this.textOrigX = origX;
            this.textOrigY = origY;
            this.textDragX = origX;
            this.textDragY = origY;
            this.textStartMouseX = event.touches[0].clientX;
            this.textStartMouseY = event.touches[0].clientY;
        },

        onDrag(event) {
            if (this.textDragType) {
                this._handleTextMove(event.clientX, event.clientY);
                return;
            }
            if (!this.isDragging || this.dragSlot === null) return;
            const dx = event.clientX - this.startX;
            const dy = event.clientY - this.startY;
            if (!this.transforms[this.dragSlot]) {
                this.transforms[this.dragSlot] = { offset_x: 0, offset_y: 0, scale: 1 };
            }
            this.transforms[this.dragSlot].offset_x = this.startOffsetX + dx;
            this.transforms[this.dragSlot].offset_y = this.startOffsetY + dy;
        },

        onTouchDrag(event) {
            if (this.textDragType) {
                if (event.touches.length === 1) {
                    event.preventDefault();
                    this._handleTextMove(event.touches[0].clientX, event.touches[0].clientY);
                }
                return;
            }
            if (!this.isDragging || this.dragSlot === null || event.touches.length !== 1) return;
            const dx = event.touches[0].clientX - this.startX;
            const dy = event.touches[0].clientY - this.startY;
            if (!this.transforms[this.dragSlot]) {
                this.transforms[this.dragSlot] = { offset_x: 0, offset_y: 0, scale: 1 };
            }
            this.transforms[this.dragSlot].offset_x = this.startOffsetX + dx;
            this.transforms[this.dragSlot].offset_y = this.startOffsetY + dy;
        },

        _handleTextMove(clientX, clientY) {
            const canvas = this.$refs.canvas;
            if (!canvas) return;
            const rect = canvas.getBoundingClientRect();
            const dxPx = clientX - this.textStartMouseX;
            const dyPx = clientY - this.textStartMouseY;
            const dxPercent = (dxPx / rect.width) * 100;
            const dyPercent = (dyPx / rect.height) * 100;
            this.textDragX = Math.max(0, Math.min(100, this.textOrigX + dxPercent));
            this.textDragY = Math.max(0, Math.min(100, this.textOrigY + dyPercent));
        },

        stopDrag() {
            if (this.textDragType) {
                const xVal = Math.round(this.textDragX * 100) / 100;
                const yVal = Math.round(this.textDragY * 100) / 100;
                if (this.textDragType === 'title') {
                    $wire.set('titleXPercent', xVal);
                    $wire.set('titleYPercent', yVal);
                } else {
                    $wire.set('captionXPercent', xVal);
                    $wire.set('captionYPercent', yVal);
                }
                this.textDragType = null;
                return;
            }
            if (this.isDragging) {
                this.syncToLivewire();
            }
            this.isDragging = false;
            this.dragSlot = null;
        },

        updateScale(slot, value) {
            if (!this.transforms[slot]) {
                this.transforms[slot] = { offset_x: 0, offset_y: 0, scale: 1 };
            }
            this.transforms[slot].scale = parseFloat(value);
            this.syncToLivewire();
        },

        resetTransform(slot) {
            this.transforms[slot] = { offset_x: 0, offset_y: 0, scale: 1 };
            this.syncToLivewire();
        },

        syncToLivewire() {
            $wire.set('slotTransforms', JSON.parse(JSON.stringify(this.transforms)));
        }
    }));
</script>
@endscript
