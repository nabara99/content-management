<x-layouts.user title="Dashboard" header="Dashboard">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Total Konten --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Total Konten</p>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(37, 99, 235, 0.1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #2563EB;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalContents }}</p>
            <p class="text-xs text-gray-400 mt-1">Konten dibuat</p>
        </div>

        {{-- Draft --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Draft</p>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(251, 191, 36, 0.15);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #F59E0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $draftContents }}</p>
            <p class="text-xs text-gray-400 mt-1">Belum dipublikasi</p>
        </div>

        {{-- Published --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Published</p>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(16, 185, 129, 0.1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #10B981;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $publishedContents }}</p>
            <p class="text-xs text-gray-400 mt-1">Sudah dipublikasi</p>
        </div>

        {{-- Template Tersedia --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Template</p>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(139, 92, 246, 0.1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #8B5CF6;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $availableTemplates }}</p>
            <p class="text-xs text-gray-400 mt-1">Template tersedia</p>
        </div>
    </div>

    {{-- Quick Actions & Recent Contents --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('user.contents.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white transition-all hover:shadow-md" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Buat Konten Baru
                </a>
                <a href="{{ route('user.contents.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    Lihat Semua Konten
                </a>
            </div>
        </div>

        {{-- Recent Contents --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Konten Terbaru</h3>
                <a href="{{ route('user.contents.index') }}" class="text-xs font-medium hover:underline" style="color: #2563EB;">Lihat Semua</a>
            </div>

            @if($recentContents->count() > 0)
                <div class="space-y-3">
                    @foreach($recentContents as $content)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        {{-- Thumbnail --}}
                        <div class="w-12 h-12 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 flex-shrink-0">
                            <img src="{{ asset('storage/' . $content->template->image) }}" alt="" class="w-full h-full object-contain">
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $content->title }}</p>
                            <p class="text-xs text-gray-400">{{ $content->template->name }} &middot; {{ $content->created_at->diffForHumans() }}</p>
                        </div>
                        {{-- Status --}}
                        <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0 {{ $content->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $content->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <p class="text-sm text-gray-400">Belum ada konten. Mulai buat konten pertama Anda!</p>
                    <a href="{{ route('user.contents.index') }}" class="inline-block mt-3 px-4 py-2 text-sm font-medium text-white rounded-lg hover:shadow-md transition-all" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                        Buat Konten
                    </a>
                </div>
            @endif
        </div>
    </div>

</x-layouts.user>
