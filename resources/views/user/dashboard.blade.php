<x-layouts.app title="User Dashboard">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>
</x-layouts.app>
