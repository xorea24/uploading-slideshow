<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mayor's Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 font-sans" 
    x-data="{ 
        tab: 'upload', 
        sidebarOpen: false, 
        selectedFiles: [],
        settings: { timer: 5, effect: 'fade' },
        updateFileList(event) {
            const newFiles = Array.from(event.target.files || event.dataTransfer.files);
            this.selectedFiles = [...this.selectedFiles, ...newFiles];
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },
        submitForm(e) {
            const dataTransfer = new DataTransfer();
            this.selectedFiles.forEach(file => dataTransfer.items.add(file));
            this.$refs.fileInput.files = dataTransfer.files;
        }
    }">

    <div class="flex min-h-screen">
        {{-- Mobile menu button --}}
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="bg-red-950 text-white p-2 rounded-lg shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Sidebar --}}
        <aside class="w-64 bg-red-950 text-white fixed md:static inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider uppercase">Mayor's <span class="text-red-400">Office</span></h1>
                <p class="text-[10px] text-red-300 tracking-[0.2em] mt-1">UPLOADING SYSTEM</p>
            </div>
            
            <nav class="flex-1 px-4 space-y-2 mt-4">
                <a href="#" @click.prevent="tab = 'upload'; sidebarOpen = false" 
                    :class="tab === 'upload' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Center
                </a>

                <a href="#" @click.prevent="tab = 'manage'; sidebarOpen = false" 
                    :class="tab === 'manage' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Manage Gallery
                </a>

                <a href="#" @click.prevent="tab = 'settings'; sidebarOpen = false" 
                    :class="tab === 'settings' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </nav>

            <div class="p-4 border-t border-red-900/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-600 hover:text-white rounded-xl transition text-sm font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Section --}}
        <main class="flex-1 overflow-y-auto h-screen bg-gray-50">
            <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <h2 class="text-xl font-bold text-gray-800" x-text="tab === 'upload' ? 'Upload Center' : (tab === 'manage' ? 'Gallery Management' : 'System Settings')"></h2>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">System Administrator</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold border-2 border-red-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <div class="p-8">
                @if (session('status'))
                    <div class="max-w-4xl mx-auto mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center justify-between">
                        <span>{{ session('status') }}</span>
                        <button @click="$el.parentElement.remove()" class="text-green-900 font-bold">&times;</button>
                    </div>
                @endif

{{-- Tab: Settings --}}
<div x-show="tab === 'settings'" x-transition:enter="transition ease-out duration-300">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
    

        <form action="{{ route('settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @php
                // Fetch settings directly from the DB to ensure they reflect the latest save
                $currentDuration = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
                $currentEffect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Slide Duration --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
                        Slide Duration (Seconds)
                    </label>
                    <div class="relative">
                        <input type="number" 
                               name="slide_duration" 
                               value="{{ $currentDuration }}" 
                               min="1" 
                               max="60"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none transition pr-12">
                        <span class="absolute right-4 top-3.5 text-gray-400 text-sm font-bold">sec</span>
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium">Controls the speed of the slideshow.</p>
                </div>

                {{-- Transition Effect --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">
                        Transition Effect
                    </label>
                    <select name="transition_effect" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none bg-white transition cursor-pointer">
                        <option value="fade" {{ $currentEffect == 'fade' ? 'selected' : '' }}>Fade (Smooth)</option>
                        <option value="slide-up" {{ $currentEffect == 'slide-up' ? 'selected' : '' }}>Slide Up</option>
                        <option value="slide-down" {{ $currentEffect == 'slide-down' ? 'selected' : '' }}>Slide Down</option>
                        <option value="slide-left" {{ $currentEffect == 'slide-left' ? 'selected' : '' }}>Slide Left</option>
                        <option value="slide-right" {{ $currentEffect == 'slide-right' ? 'selected' : '' }}>Slide Right</option>
                        <option value="zoom" {{ $currentEffect == 'zoom' ? 'selected' : '' }}>Ken Burns (Zoom)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 font-medium">How the images move during change.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                <a href="/" target="_blank" class="text-sm font-bold text-red-700 hover:underline flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Preview Slideshow
                </a>
                
                <button type="submit" class="bg-red-700 text-white px-10 py-3 rounded-xl font-bold hover:bg-red-800 transition shadow-lg active:scale-95">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
                {{-- Tab: Upload --}}
                <div x-show="tab === 'upload'" x-transition>
                    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <form action="{{ route('slideshow.store') }}" method="POST" enctype="multipart/form-data" @submit="submitForm" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Category Name / Event Title</label>
                                <input type="text" name="category_name" placeholder="e.g. Mayor's Cup 2024" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none" required>
                            </div>

                            <div id="drop-zone" 
                                @dragover.prevent="$el.classList.add('border-red-500', 'bg-red-50')" 
                                @dragleave.prevent="$el.classList.remove('border-red-500', 'bg-red-50')"
                                @drop.prevent="$el.classList.remove('border-red-500', 'bg-red-50'); updateFileList($event)"

                                <input type="file" name="images[]" id="images" x-ref="fileInput" multiple class="hidden" @change="updateFileList($event)">
                                
                                <label for="images" class="cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-red-600 font-bold">Select Photos</span> or drag and drop
                                </label>
                                
                                <div x-show="selectedFiles.length > 0" class="mt-6 p-4 bg-white rounded-xl border border-gray-100 text-left">
                                    <div class="flex justify-between items-center mb-3">
                                        <p class="text-xs font-bold text-gray-500 uppercase">Selected Files (<span x-text="selectedFiles.length"></span>)</p>
                                        <button type="button" @click="selectedFiles = []" class="text-[10px] text-red-500 hover:underline">Clear All</button>
                                    </div>
                                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <template x-for="(file, index) in selectedFiles" :key="index">
                                            <li class="flex items-center justify-between text-[10px] text-gray-600 bg-gray-50 p-2 rounded border">
                                                <span class="truncate pr-2" x-text="file.name"></span>
                                                <button type="button" @click="removeFile(index)" class="text-red-500 font-bold px-1 hover:bg-red-100 rounded">&times;</button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            <button type="submit" class="w-full md:w-auto bg-red-700 text-white px-10 py-4 rounded-xl font-bold hover:bg-red-800 transition">
                                Upload All <span x-text="selectedFiles.length"></span> Photos
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Tab: Manage --}}
                <div x-show="tab === 'manage'" x-transition:enter="transition ease-out duration-300">
                    <div class="max-w-6xl mx-auto space-y-8">
                        @forelse($slides->groupBy('category_name') as $category => $groupedSlides)
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-red-900">{{ $category ?: 'Uncategorized' }}</h3>
                                        <p class="text-xs text-gray-500">{{ $groupedSlides->count() }} total photos</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($groupedSlides as $slide)
                                        <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden group">
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $slide->image_path) }}" class="w-full h-32 object-cover">
                                                <div class="absolute top-2 left-2">
                                                    <span class="{{ $slide->is_active ? 'bg-green-500' : 'bg-gray-500' }} text-white text-[8px] font-bold px-2 py-1 rounded-full">
                                                        {{ $slide->is_active ? 'LIVE' : 'HIDDEN' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="p-3 flex gap-2">
                                                <form action="{{ route('slideshow.toggle', $slide->id) }}" method="POST" class="flex-1">
                                                    @csrf @method('PATCH')
                                                    <button class="w-full py-1.5 bg-white border border-gray-200 text-[10px] font-bold rounded-lg hover:bg-gray-100 transition">
                                                        {{ $slide->is_active ? 'HIDE' : 'SHOW' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('slideshow.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                                    @csrf @method('DELETE')
                                                    <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                                <p class="text-gray-400">No photos found in the system.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>