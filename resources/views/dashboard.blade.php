<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mayor's Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }

    /* Siguraduhin na kita ang arrows */
.custom-pagination svg {
    width: 20px !important;
    height: 20px !important;
    display: inline !important;
    color: #4b5563; /* Gray-600 */
}

.custom-pagination nav div div p {
    margin-bottom: 0;
    padding-top: 10px;
}

/* Fix para sa spacing ng active links */
.custom-pagination span[aria-current="page"] span {
    background-color: #7f1d1d !important; /* red-950 */
    color: white !important;
    border-radius: 8px;
}
    </style>
</head>

<body class="bg-gray-50 font-sans" 
    x-data="{ 
        tab: new URLSearchParams(window.location.search).has('trash_page') ? 'trash' : 
             (new URLSearchParams(window.location.search).has('page') ? 'manage' : 
             '{{ session('last_tab') ?? (session('status') ? 'manage' : 'upload') }}'), 
        sidebarOpen: false,
        search: '', 
        selectedFiles: [],
        updateFileList(event) {
            const files = event.target.files || event.dataTransfer.files;
            this.selectedFiles = [...this.selectedFiles, ...Array.from(files)];
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
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="bg-red-950 text-white p-2 rounded-lg shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <aside class="w-64 bg-red-950 text-white fixed md:static inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl" 
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="p-6">   
               <img src="{{ asset('image/stc_logo2.png') }}" alt="Logo" class="w-20 h-20 mb-4 mx-auto md:mx-0">
                <h1 class="text-2xl font-bold tracking-wider uppercase">Mayor's <span class="text-red-400">Office</span></h1>
                <p class="text-[10px] text-red-300 tracking-[0.2em] mt-1">UPLOADING SYSTEM</p>
            </div>  

            <nav class="flex-1 px-4 space-y-2 mt-4">
                <a href="#" @click.prevent="tab = 'manage'; sidebarOpen = false" 
                    :class="tab === 'manage' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Albums
                </a>

                <a href="#" @click.prevent="tab = 'upload'; sidebarOpen = false" 
                    :class="tab === 'upload' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload 
                </a>

                <a href="#" @click.prevent="tab = 'trash'; sidebarOpen = false" 
                    :class="tab === 'trash' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Recycle Bin
                    <span class="ml-auto bg-white text-red-950 text-[10px] px-2 py-0.5 rounded-full font-bold">
                        {{ \App\Models\Slideshow::onlyTrashed()->count() }}
                    </span>
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
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-600 hover:text-white rounded-xl transition text-sm font-bold uppercase tracking-widest">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto h-screen bg-gray-50 custom-scrollbar">
            <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-8">
                    <h2 class="text-xl font-bold text-gray-800" 
                        x-text="tab === 'upload' ? 'Upload New Content' : (tab === 'manage' ? 'Albums Management' : (tab === 'trash' ? 'Recycle Bin' : 'System Settings'))">
                    </h2>
                    <div x-show="tab === 'manage' || tab === 'trash'" class="relative hidden lg:block">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" x-model="search" placeholder="Search albums..." 
                            class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 outline-none transition w-64">
                    </div>
                </div>

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
                        <span class="text-sm font-medium">{{ session('status') }}</span>
                        <button @click="$el.parentElement.remove()" class="text-green-900 font-bold">&times;</button>
                    </div>
                @endif

                <div x-show="tab === 'upload'" x-cloak x-transition>
                    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <form action="{{ route('slideshow.store') }}" method="POST" enctype="multipart/form-data" @submit="submitForm" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Album Name</label>
                                <input type="text" name="category_name" placeholder="e.g. Mayor's Cup 2024" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none transition" required>
                            </div>

                            <div id="drop-zone" 
                                @dragover.prevent="$el.classList.add('border-red-500', 'bg-red-50')" 
                                @dragleave.prevent="$el.classList.remove('border-red-500', 'bg-red-50')"
                                @drop.prevent="$el.classList.remove('border-red-500', 'bg-red-50'); updateFileList($event)"
                                class="border-2 border-dashed border-gray-200 rounded-2xl p-10 text-center hover:border-red-400 transition cursor-pointer bg-gray-50 relative">
                                
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

                            <button type="submit" class="w-full md:w-auto bg-red-700 text-white px-10 py-4 rounded-xl font-bold hover:bg-red-800 transition shadow-lg">
                                Upload All <span x-text="selectedFiles.length"></span> Photos
                            </button>
                        </form>
                    </div>
                </div>

<div x-show="tab === 'manage'" x-cloak x-transition>
    <div class="max-w-6xl mx-auto space-y-8">
        
        @php
            // We group the $slides (the paginated object) into a collection for display
            $grouped = $slides->groupBy('category_name'); 
        @endphp

        @forelse($grouped as $category => $groupedSlides)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6"
                 x-show="search === '' || '{{ strtolower($category) }}'.includes(search.toLowerCase())">
                
                <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-red-900">{{ $category ?: 'Uncategorized' }}</h3>
                        <p class="text-xs text-gray-500">{{ $groupedSlides->count() }} photos in this album</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($groupedSlides as $slide)
                        <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden group">
                            <img src="{{ asset('storage/' . $slide->image_path) }}" class="w-full h-32 object-cover">
                            <div class="p-3 flex gap-2">
                                <form action="{{ route('slideshow.toggle', $slide->id) }}" method="POST" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button class="w-full py-1.5 bg-white border border-gray-200 text-[10px] font-bold rounded-lg hover:bg-gray-100 transition">
                                        {{ $slide->is_active ? 'HIDE' : 'SHOW' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                <p class="text-gray-400">No albums found.</p>
            </div>
        @endforelse

        {{-- INSERTED PAGINATION BLOCK START --}}
        @if(isset($slides) && method_exists($slides, 'hasPages') && $slides->hasPages())
            <div class="mt-8 px-4 py-4 bg-white border border-gray-100 rounded-xl shadow-sm custom-pagination">
                {{ $slides->appends(request()->query())->links() }}
            </div>
        @endif
   {{-- INSERTED PAGINATION BLOCK START --}}
    @if(isset($slides) && $slides instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-8 px-4 py-4 bg-white border border-gray-100 rounded-xl shadow-sm custom-pagination">
            {{-- Use the original $slides object here, NOT $grouped --}}
            {{ $slides->appends(request()->query())->links() }}
        </div>
        
        <div class="mt-2 text-center text-xs text-gray-500">
            Showing {{ $slides->firstItem() }} to {{ $slides->lastItem() }} of {{ $slides->total() }} total photos
        </div>
    @endif
    {{-- INSERTED PAGINATION BLOCK END --}}
        </div>
    </div>

                <div x-show="tab === 'trash'" x-cloak x-transition>
                    <div class="max-w-6xl mx-auto space-y-8">
                        @php $trashedGrouped = \App\Models\Slideshow::onlyTrashed()->get()->groupBy('category_name'); @endphp

                        @forelse($trashedGrouped as $category => $trashedSlides)
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
                                 x-show="search === '' || '{{ strtolower($category) }}'.includes(search.toLowerCase())">
                                <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-3">
                                    <h3 class="text-md font-bold text-gray-700">
                                        Album: <span class="text-red-900">{{ $category ?: 'Uncategorized' }}</span>
                                    </h3>
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-md font-bold uppercase">
                                        {{ $trashedSlides->count() }} Items
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($trashedSlides as $trash)
                                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden shadow-sm group">
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $trash->image_path) }}" class="w-full h-32 object-cover opacity-60 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition">
                                            </div>
                                            <div class="p-3 flex gap-2">
                                                <form action="{{ route('slideshow.restore', $trash->id) }}" method="POST" class="flex-1">
                                                    @csrf @method('PATCH')
                                                    <button class="w-full py-1.5 bg-green-600 text-white text-[10px] font-bold rounded-lg hover:bg-green-700 transition">RESTORE</button>
                                                </form>
                                                <form action="{{ route('slideshow.force-delete', $trash->id) }}" method="POST" onsubmit="return confirm('Permanently delete?')">
                                                    @csrf @method('DELETE')
                                                    <button class="p-1.5 text-red-500 hover:bg-red-50 border border-red-100 rounded-lg transition">
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
                            <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-100">
                                <p class="text-gray-400 font-medium">The Recycle Bin is empty.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div x-show="tab === 'settings'" x-cloak x-transition>
                    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <form action="{{ route('settings.update') }}" method="POST" class="space-y-8">
                            @csrf
                            @php
                                $currentDuration = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
                                $currentEffect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Slide Duration (Seconds)</label>
                                    <div class="relative">
                                        <input type="number" name="slide_duration" value="{{ $currentDuration }}" min="1" max="60"
                                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none transition pr-12">
                                        <span class="absolute right-4 top-3.5 text-gray-400 text-sm font-bold">sec</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Transition Effect</label>
                                    <select name="transition_effect" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none bg-white transition cursor-pointer">
                                        <option value="fade" {{ $currentEffect == 'fade' ? 'selected' : '' }}>Fade (Smooth)</option>
                                        <option value="zoom" {{ $currentEffect == 'zoom' ? 'selected' : '' }}>Ken Burns (Zoom)</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="bg-red-700 text-white px-10 py-3 rounded-xl font-bold hover:bg-red-800 transition shadow-lg">Save Changes</button>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>