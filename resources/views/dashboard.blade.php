<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploading-slideshow </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen py-6">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Admin Dashboard</h1>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Logout
                    </button>
                </form>
            @if (session('status'))
                <div class="mb-4 text-green-600 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <h4 class="text-xl font-bold text-gray-900 mb-4">Upload New Slide</h4>

                <form action="{{ route('slideshow.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Slide Title</label>
                            <input type="text" name="title" placeholder="e.g., Monday Flag Ceremony" required
                                class="w-full px-4 py-3 bg-[#f3f4f6] border-none rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Select Image</label>
                            <input type="file" name="image" required
                                class="w-full px-4 py-2 bg-[#f3f4f6] border-none rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-900 file:text-white hover:file:bg-blue-800">
                        </div>
                    </div>

                    <button type="submit" class="bg-[#0a0a14] text-white px-8 py-3 rounded-xl font-bold hover:bg-gray-800 transition">
                        Upload to Slideshow
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>