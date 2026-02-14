<x-layouts.admin title="Dashboard" header="Dashboard">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        {{-- Total Users --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Total Users</p>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(37, 99, 235, 0.1);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #2563EB;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            <p class="text-xs text-gray-400 mt-1">User terdaftar</p>
        </div>

        {{-- Templates --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Templates</p>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(251, 191, 36, 0.15);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #F59E0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">0</p>
            <p class="text-xs text-gray-400 mt-1">Template tersedia</p>
        </div>

    </div>

    {{-- Quick Actions & Recent Users --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white transition-all hover:shadow-md" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    Kelola Users
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: #F59E0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Upload Template
                    <span class="ml-auto text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full">Soon</span>
                </a>
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">User Terbaru</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-medium hover:underline" style="color: #2563EB;">Lihat Semua</a>
            </div>

            @php $recentUsers = \App\Models\User::where('role', 'user')->latest()->take(5)->get(); @endphp

            @if($recentUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left pb-3 text-xs font-medium text-gray-400 uppercase">Nama</th>
                                <th class="text-left pb-3 text-xs font-medium text-gray-400 uppercase">Email</th>
                                <th class="text-left pb-3 text-xs font-medium text-gray-400 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentUsers as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: #FBBF24;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-800">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="py-3 text-sm text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-400">Belum ada user terdaftar.</p>
                </div>
            @endif
        </div>
    </div>

</x-layouts.admin>
