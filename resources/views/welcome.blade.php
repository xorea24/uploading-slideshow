<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mayor's Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col sm:justify-center items-center py-6">
        
        <div class="w-full sm:max-w-md bg-white shadow-sm overflow-hidden sm:rounded-2xl px-10 py-12">
            
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Login</h1>
                <p class="text-gray-500 mt-2 text-lg">Enter your credentials to access the admin dashboard</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label class="block font-bold text-gray-900 text-lg">Username</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter username" required autofocus 
                        class="block mt-2 w-full px-4 py-3 bg-[#f3f4f6] border-none rounded-xl focus:ring-2 focus:ring-gray-300">
                </div>

              <div class="mt-8 space-y-3">
    <button type="submit" class="w-full py-4 bg-red-600 rounded-xl font-bold text-white text-lg hover:bg-red-700 transition shadow-lg shadow-red-200">
        Admin Login
    </button>

    <a href="/" class="w-full inline-flex justify-center py-4 bg-white border border-gray-200 rounded-xl font-bold text-gray-900 text-lg hover:bg-gray-50 transition">
        Public Access
    </a>
</div>
            </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>