<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Content Management</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex">

        {{-- Left Side - Branding --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center"
            style="background: linear-gradient(160deg, #2563EB 0%, #1D4ED8 40%, #1E40AF 100%);">

            {{-- Decorative shapes - kuning terang --}}
            <div class="absolute -top-10 -left-10 w-64 h-64 rounded-full" style="background: #FBBF24; opacity: 0.25;">
            </div>
            <div class="absolute bottom-20 -right-20 w-80 h-80 rounded-full" style="background: #FBBF24; opacity: 0.2;">
            </div>

            {{-- Blue lighter circles --}}
            <div class="absolute top-1/4 right-10 w-48 h-48 rounded-full" style="background: #60A5FA; opacity: 0.2;">
            </div>
            <div class="absolute bottom-10 left-20 w-32 h-32 rounded-full" style="background: #93C5FD; opacity: 0.15;">
            </div>

            {{-- Yellow accent bar top --}}
            <div class="absolute top-0 left-0 w-full h-2" style="background: linear-gradient(90deg, #FBBF24, #F59E0B);">
            </div>

            {{-- Content --}}
            <div class="relative z-10 text-center px-12">
                {{-- Yellow icon badge --}}
                <div class="mx-auto mb-8 w-20 h-20 rounded-2xl flex items-center justify-center shadow-lg"
                    style="background: linear-gradient(135deg, #FBBF24, #F59E0B);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <h2 class="text-4xl font-bold text-white mb-3">Content Management</h2>
                <div class="mx-auto mb-6 w-16 h-1.5 rounded-full" style="background: #FBBF24;"></div>
                <p class="text-blue-100 text-lg leading-relaxed max-w-md mx-auto">
                    Platform pengelolaan konten kreatif. Buat, kelola, dan publikasikan konten dengan mudah.
                </p>

                {{-- Feature cards --}}
                <div class="mt-12 flex justify-center gap-5">
                    <div class="w-36 h-44 rounded-2xl flex flex-col items-center justify-center gap-3 shadow-lg"
                        style="background: rgba(251, 191, 36, 0.2); border: 2px solid rgba(251, 191, 36, 0.5);">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                            style="background: #FBBF24;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-white text-sm font-semibold">Template</span>
                    </div>
                    <div class="w-36 h-44 rounded-2xl flex flex-col items-center justify-center gap-3 mt-6 shadow-lg"
                        style="background: rgba(255, 255, 255, 0.1); border: 2px solid rgba(255, 255, 255, 0.25);">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                            style="background: rgba(255, 255, 255, 0.2);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <span class="text-white text-sm font-semibold">Konten</span>
                    </div>
                    <div class="w-36 h-44 rounded-2xl flex flex-col items-center justify-center gap-3 shadow-lg"
                        style="background: rgba(251, 191, 36, 0.2); border: 2px solid rgba(251, 191, 36, 0.5);">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                            style="background: #FBBF24;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <span class="text-white text-sm font-semibold">Download</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side - Login Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <div class="flex items-center justify-center mb-2">
                    <img src="{{ asset('images/tanbu.png') }}" alt="Tanah Bumbu" class="w-15">
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/beraksi.png') }}" alt="BerAKSI" class="h-13">
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold" style="color: #1D4ED8;">Prokom</h1>
                    <p class="mt-2 text-gray-500">Masuk ke akun Anda untuk melanjutkan.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            autofocus placeholder="nama@email.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition-colors"
                            onfocus="this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)'; this.style.borderColor='#2563EB'"
                            onblur="this.style.boxShadow='none'; this.style.borderColor='#d1d5db'">
                    </div>

                    <div class="mb-5" x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" id="password" required
                                placeholder="Masukkan password"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none transition-colors"
                                onfocus="this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)'; this.style.borderColor='#2563EB'"
                                onblur="this.style.boxShadow='none'; this.style.borderColor='#d1d5db'">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    
                    {{-- Captcha --}}
                    <div class="mb-5" x-data="captcha()" x-init="load()">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Captcha</label>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex-1 px-4 py-3 rounded-lg text-lg font-bold tracking-wider text-center select-none"
                                style="background: #EFF6FF; color: #1D4ED8; border: 2px dashed #93C5FD;">
                                <span x-text="question" class="inline-block min-w-[100px]">Memuat...</span>
                            </div>
                            <button type="button" @click="load()"
                                class="p-2.5 rounded-lg border border-gray-300 text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-colors"
                                title="Refresh captcha">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </div>
                        <input type="number" name="captcha" placeholder="Jawaban"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition-colors"
                            onfocus="this.style.boxShadow=&#039;0 0 0 3px rgba(37,99,235,0.15)&#039;; this.style.borderColor=&#039;#2563EB&#039;"
                            onblur="this.style.boxShadow=&#039;none&#039;; this.style.borderColor=&#039;#d1d5db&#039;">
                    </div>

                    <button type="submit"
                        class="w-full text-white font-semibold py-3 rounded-lg transition-all duration-200 hover:shadow-lg"
                        style="background: linear-gradient(135deg, #2563EB, #1D4ED8);"
                        onmouseover="this.style.background='linear-gradient(135deg, #1D4ED8, #1E40AF)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #2563EB, #1D4ED8)'">
                        Sign in
                    </button>
                </form>

                {{-- Bottom accent --}}
                <div class="mt-8 flex justify-center">
                    <div class="h-1.5 w-16 rounded-full"
                        style="background: linear-gradient(90deg, #FBBF24, #F59E0B);"></div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function captcha() {
            return {
                question: "Memuat...",
                load() {
                    fetch("/captcha")
                        .then(r => r.json())
                        .then(data => this.question = data.question)
                        .catch(() => this.question = "Gagal memuat");
                }
            }
        }
    </script>
</body>

</html>
