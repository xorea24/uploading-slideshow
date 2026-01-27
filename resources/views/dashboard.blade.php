<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Slideshow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
{{-- Alpine.js data for tab management and mobile sidebar --}}
<body class="bg-gray-50 font-sans" x-data="{ tab: 'upload', sidebarOpen: false, selectedFiles: [], updateFileList() { this.selectedFiles = Array.from(this.$refs.fileInput.files); } }">

    {{-- Main container with flex layout --}}
    <div class="flex min-h-screen">
        {{-- Mobile menu button, hidden on md and up --}}
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="bg-[#0a0a14] text-white p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Sidebar: fixed on mobile, static on md --}}
        <aside class="w-64 bg-[#0a0a14] text-white fixed md:static inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex flex-col" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider">Upload<span class="text-blue-400"></span></h1>
            </div>
            
            <nav class="flex-1 px-4 space-y-2 mt-4">
                {{-- Tab switchers: prevent default and close sidebar on mobile --}}
                <a href="#" @click.prevent="tab = 'upload'; sidebarOpen = false" 
                   :class="tab === 'upload' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800'"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                   </svg>
                   Upload New
                </a>

                <a href="#" @click.prevent="tab = 'manage'; sidebarOpen = false" 
                   :class="tab === 'manage' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800'"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                   </svg>
                   Manage Photos
                </a>
            </nav>

            
               

            <div class="p-4 border-t border-gray-800">
                {{-- Logout form --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-900/20 rounded-xl transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Overlay for mobile sidebar --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="md:hidden fixed inset-0 z-30 bg-black bg-opacity-50" x-transition></div>

        <main class="flex-1">
            <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800" x-text="tab === 'upload' ? 'Upload Center' : 'Photo Management'"></h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">Welcome, Admin</span>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">A</div>
                </div>
            </header>

            <div class="p-8">
                {{-- Success message from session --}}
                @if (session('status'))
                    <div class="max-w-4xl mx-auto mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Upload tab content --}}
                <div x-show="tab === 'upload'" x-transition>
                    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h4 class="text-xl font-bold text-gray-900 mb-4">Upload Multiple Slides</h4>
                        {{-- Upload form: drag-and-drop implemented --}}
                        <form action="{{ route('slideshow.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div id="drop-zone" class="border-2 border-dashed border-gray-200 rounded-2xl p-10 text-center hover:border-blue-400 transition cursor-pointer bg-gray-50">
                                {{-- Hidden file input --}}
                                <input type="file" name="images[]" id="images" x-ref="fileInput" multiple required class="hidden" @change="updateFileList()">
                                <label for="images" class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-blue-600 font-bold">Click to upload</span> or drag and drop
                                    <p class="text-xs text-gray-400 mt-2">JPG, PNG up to 2MB</p>
                                </label>
                                {{-- File list --}}
                                <div x-show="selectedFiles.length > 0" class="mt-4">
                                    <p class="text-sm text-gray-600">Selected files:</p>
                                    <ul class="text-xs text-gray-500">
                                        <template x-for="file in selectedFiles" :key="file.name">
                                            <li x-text="file.name"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-10 py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                                Start Uploading
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Manage tab content --}}
                <div x-show="tab === 'manage'" x-transition>
                    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Live Gallery</h4>
                                <p class="text-sm text-gray-500">Toggle visibility or delete slides from the frontend.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {{-- Loop through slides --}}
                            @forelse($slides as $slide)
                            <div class="bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden group">
                                <div class="relative">
                                    {{-- Image display: assumes storage link is set up --}}
                                    <img src="{{ asset('storage/' . $slide->image_path) }}" class="w-full h-44 object-cover">
                                    <div class="absolute top-3 left-3">
                                        {{-- Status badge --}}
                                        <span class="{{ $slide->is_active ? 'bg-green-500' : 'bg-red-500' }} text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm">
                                            {{ $slide->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4 flex gap-2">
                                    {{-- Toggle form: uses PATCH method --}}
                                    <form action="{{ route('slideshow.toggle', $slide->id) }}" method="POST" class="flex-1">
                                        @csrf @method('PATCH')
                                        <button class="w-full py-2 bg-white border border-gray-200 text-xs font-bold rounded-lg hover:bg-gray-100 transition">
                                            {{ $slide->is_active ? 'DEACTIVATE' : 'ACTIVATE' }}
                                        </button>
                                    </form>
                                    {{-- Delete form: uses DELETE method, with confirmation --}}
                                    <form action="{{ route('slideshow.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete photo?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            {{-- Empty state --}}
                            @empty
                            <div class="col-span-full text-center py-20 text-gray-400">
                                Walang litrato sa gallery.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Drag and drop functionality
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('images');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            const files = e.dataTransfer.files;
            fileInput.files = files;
            // Trigger change event to update Alpine
            fileInput.dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>