<!-- ============================================================ -->
<!-- ADMIN DASHBOARD - MAYOR'S OFFICE                            -->
<!-- Photo Management System                                 -->
<!-- ============================================================ -->

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags & Title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mayor's Office</title>
    
    <!-- Tailwind CSS Framework -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for Reactive Components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>

<!-- ============================================================ -->
<!-- MAIN BODY - Dashboard Container                             -->
<!-- ============================================================ -->
<body class="bg-gray-50 font-sans"
    x-data="{
        tab: '{{ session('last_tab', 'manage') }}',
        tab: '{{ session('last_tab') ?? (session('status') ? 'manage' : 'upload') }}',
        sidebarOpen: false,
        search: '',
        page: 1,
        perPage: 10,
        isNewAlbum: false,

        // --- NEW DATA FOR MULTI-UPLOAD ---
        uploadRows: [{ id: Date.now(), name: '', desc: '', file: null, preview: null }],

        addPhotoRow() {
            this.uploadRows.push({ id: Date.now(), name: '', desc: '', file: null, preview: null });
        },

        removePhotoRow(index) {
            this.uploadRows.splice(index, 1);
        },

        handleFileChange(event, index) {
            const file = event.target.files[0];
            if (file) {
                this.uploadRows[index].file = file;
                this.uploadRows[index].preview = URL.createObjectURL(file);
            }
        },
        // --------------------------------

        // --- SETTINGS DATA ---
        albumSearch: '',
        availableAlbums: [
            @foreach($albums as $album)
                { id: {{ $album->id }}, name: '{{ addslashes($album->name) }}' },
            @endforeach
        ],
        activeAlbums: [],
        displayAlbumIds: '',

        init() {
            let rawSavedValue = '{{ $settings['display_album_ids'] ?? '' }}';
            let savedIds = rawSavedValue
                ? rawSavedValue.split(',').map(id => id.trim()).filter(id => id !== '')
                : [];
            this.activeAlbums = this.availableAlbums.filter(a => savedIds.includes(a.id.toString()));
            this.availableAlbums = this.availableAlbums.filter(a => !savedIds.includes(a.id.toString()));
            this.displayAlbumIds = this.activeAlbums.map(a => a.id).join(',');
        },

        moveToActive(album) {
            this.activeAlbums.push(album);
            this.availableAlbums = this.availableAlbums.filter(a => a.id !== album.id);
            this.displayAlbumIds = this.activeAlbums.map(a => a.id).join(',');
        },

        moveToAvailable(album) {
            this.availableAlbums.push(album);
            this.activeAlbums = this.activeAlbums.filter(a => a.id !== album.id);
            this.displayAlbumIds = this.activeAlbums.map(a => a.id).join(',');
        },
        // --------------------

        get totalVisible() { return document.querySelectorAll('.album-card').length; },
        get totalPages() { return Math.ceil(this.totalVisible / this.perPage) || 1; },
        trashSearch: ''
    }"
    x-init="init()">
    <div class="flex min-h-screen">
        <!-- ============================================================ -->
        <!-- MOBILE MENU TOGGLE BUTTON                                    -->
        <!-- Hidden on desktop, shown on mobile devices                    -->
        <!-- ============================================================ -->
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="bg-red-950 text-white p-2 rounded-lg shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

     <!-- ============================================================ -->
     <!-- SIDEBAR NAVIGATION                                           -->
     <!-- Responsive sidebar with navigation menu & logout button     -->
     <!-- ============================================================ -->
    <aside class="w-64 bg-blue-700 text-white fixed md:static inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
   <div class="p-6 flex items-center gap-4">   
    <img src="{{ asset('image/stc_logo5.png') }}" alt="Logo" class="w-16 h-16">
    
    <div>
        <h1 class="text-2xl font-bold tracking-wider uppercase leading-none">
            Mayor's <span class="text-blue-200">Office</span>
        </h1>
        <p class="text-[10px] text-blue-100 tracking-[0.2em] mt-1">
            UPLOADING SYSTEM
        </p>
    </div>
</div>

    <nav class="flex-1 px-4 space-y-2 mt-4">
        <a href="#" @click.prevent="tab = 'manage'; sidebarOpen = false" 
            :class="tab === 'manage' ? 'bg-white text-blue-700 shadow-lg' : 'text-blue-50 hover:bg-blue-600'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Albums
    </a>

        <a href="#" @click.prevent="tab = 'trash'; sidebarOpen = false" 
            :class="tab === 'trash' ? 'bg-white text-blue-700 shadow-lg' : 'text-blue-50 hover:bg-blue-600'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Recycle Bin
            <span class="ml-auto bg-blue-900 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">
                {{ \App\Models\Photo::onlyTrashed()->count() }}
            </span>
        </a>

        <a href="#" @click.prevent="tab = 'settings'; sidebarOpen = false" 
            :class="tab === 'settings' ? 'bg-white text-blue-700 shadow-lg' : 'text-blue-50 hover:bg-blue-600'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
        </a>
    </nav>

    <div class="p-4 border-t border-blue-800/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-3 px-4 py-3 text-blue-100 hover:bg-blue-800 hover:text-white rounded-xl transition text-sm font-bold uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

       <!-- ============================================================ -->
       <!-- MAIN CONTENT AREA                                            -->
       <!-- Displays different tab content based on user selection       -->
       <!-- ============================================================ -->
       <main class="flex-1 overflow-y-auto h-screen bg-gray-50 custom-scrollbar">
            <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
                <!-- Page Title -->
                <h2 class="text-xl font-bold text-gray-800" 
                    x-text="tab === 'upload' ? 'Upload New Content' : (tab === 'manage' ? 'Albums Management' : (tab === 'trash' ? 'Recycle Bin' : 'System Settings'))">
                </h2>

                <!-- User Info & Avatar -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                    </div>
                    
                </div>
            </header>

            <div class="p-4"> 
                @if (session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-2 flex items-center justify-between bg-green-50 border-l-2 border-green-500 p-2 rounded shadow-sm max-w-xs ml-auto">
                    <span class="text-green-700 text-xs font-semibold">{{ session('status') }}</span> 
                    <button @click="show = false" class="text-green-500 hover:text-green-700"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                @endif
            </div>

            <!-- ============================================================ -->
            <!-- UPLOAD TAB CONTENT                                           -->
            <!-- File upload form with drag & drop support                    -->
            <!-- ============================================================ -->
<div x-show="tab === 'upload'" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     x-cloak 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-data="{ 
        isNewAlbum: false,
        newAlbumName: '',
        uploadRows: [
            { id: Date.now(), preview: null, title: '' }
        ],
        existingAlbums: [
            @foreach($albums as $album)
                '{{ strtolower(addslashes($album->name)) }}',
            @endforeach
        ],
        get isDuplicate() {
            return this.existingAlbums.includes(this.newAlbumName.trim().toLowerCase());
        },
        addPhotoRow() {
            this.uploadRows.push({ id: Date.now(), preview: null, title: '' });
        },
        removePhotoRow(index) {
            this.uploadRows.splice(index, 1);
        },
        handleFileChange(event, index) {
            const file = event.target.files[0];
            if (file) {
                // Generate Preview
                this.uploadRows[index].preview = URL.createObjectURL(file);
                
                // Default Title Logic: Kunin ang filename, tanggalin ang extension
                let fileName = file.name.split('.').slice(0, -1).join('.');
                
                // I-set ang title kung wala pang nilalagay ang user
                if (!this.uploadRows[index].title) {
                    this.uploadRows[index].title = fileName;
                }
            }
        }
     }">
    
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="tab = 'manage'"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="max-w-5xl w-full bg-white p-8 rounded-3xl shadow-2xl border border-gray-100 relative" @click.stop>
            
            <button @click="tab = 'manage'" class="absolute top-6 right-6 text-gray-400 hover:text-blue-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="mb-8">
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Upload New Photos</h2>
                <p class="text-sm text-gray-500">Add images to your library or create a new collection.</p>
            </div>

            <form action="{{ route('Photo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-blue-900 uppercase tracking-widest">Target Album</label>
                        <select name="album_id" id="album_select" 
                                @change="isNewAlbum = $el.value === 'new'" 
                                class="w-full px-4 py-3 rounded-xl border border-blue-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-bold text-gray-700 bg-white transition-all">
                            <option value="">-- No Album (Unassigned) --</option>
                            @foreach($albums as $album)
                                <option value="{{ $album->id }}">{{ $album->name }}</option>
                            @endforeach
                            <option value="new" class="font-bold text-blue-600">+ Create New Album</option>
                        </select>
                    </div>

                    <div x-show="isNewAlbum" x-transition.scale.origin.left class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-blue-900 uppercase tracking-widest">New Album Details</label>
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       name="new_album_name" 
                                       x-model="newAlbumName"
                                       placeholder="Album Title" 
                                       :class="isDuplicate ? 'border-red-500 focus:ring-red-500/10 focus:border-red-500' : 'border-blue-200 focus:ring-blue-500/10 focus:border-blue-500'"
                                       class="w-full px-4 py-3 rounded-xl border text-sm font-bold transition-all outline-none">
                                
                                <template x-if="isDuplicate">
                                    <div class="absolute -bottom-5 left-0 text-[9px] text-red-600 font-black uppercase tracking-tighter">
                                        ⚠ Name already exists! Use a different name.
                                    </div>
                                </template>
                            </div>
                            <input type="text" name="new_album_desc" placeholder="Album Subtitle/Description" class="flex-1 px-4 py-3 rounded-xl border border-blue-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm bg-white transition-all">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="space-y-4 max-h-[40vh] overflow-y-auto px-2 custom-scrollbar">
                    <template x-for="(row, index) in uploadRows" :key="row.id">
                        <div class="flex flex-col md:flex-row gap-6 p-5 bg-white border border-gray-100 rounded-2xl relative group hover:border-blue-200 transition-all hover:shadow-sm">
                            
                            <div class="w-full md:w-32 h-32 bg-gray-50 rounded-xl flex-shrink-0 border-2 border-dashed border-gray-200 group-hover:border-blue-300 overflow-hidden relative transition-colors">
                                <template x-if="row.preview">
                                    <img :src="row.preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!row.preview">
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[8px] font-black uppercase">Browse</span>
                                    </div>
                                </template>
                                <input type="file" name="images[]" required class="absolute inset-0 opacity-0 cursor-pointer" @change="handleFileChange($event, index)">
                            </div>

                            <div class="flex-1 grid grid-cols-1 gap-3">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase ml-1">Image Title</label>
                                    <input type="text" 
                                           name="titles[]" 
                                           x-model="row.title"
                                           placeholder="Enter title (or leave blank for filename)" 
                                           class="w-full px-5 py-3 rounded-xl border border-gray-100 text-sm focus:border-blue-500 outline-none font-bold text-gray-700 bg-gray-50/30">
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase ml-1">Description (Optional)</label>
                                    <textarea name="descriptions[]" 
                                              placeholder="Write a subtitle..." 
                                              class="w-full px-5 py-3 rounded-xl border border-gray-100 text-sm focus:border-blue-500 outline-none h-16 bg-gray-50/30"></textarea>
                                </div>
                            </div>

                            <button type="button" x-show="uploadRows.length > 1" @click="removePhotoRow(index)" class="absolute -top-2 -right-2 bg-white text-gray-400 border border-gray-100 rounded-full p-1.5 shadow-md hover:text-red-600 hover:border-red-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-50">
                    <button type="button" @click="addPhotoRow()" class="flex items-center gap-2 text-blue-600 font-black text-xs uppercase tracking-widest hover:text-blue-800 transition-colors">
                        <div class="p-1.5 bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        Add Another Photo
                    </button>

                    <button type="submit" 
                            :disabled="isNewAlbum && (isDuplicate || newAlbumName.trim() === '')"
                            :class="(isNewAlbum && (isDuplicate || newAlbumName.trim() === '')) ? 'bg-gray-200 cursor-not-allowed text-gray-400 shadow-none' : 'bg-blue-700 hover:bg-blue-800 text-white shadow-xl shadow-blue-200'"
                            class="px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all active:scale-95">
                        Save All Images
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

      <!-- ============================================================ -->
      <!-- MANAGE ALBUMS TAB CONTENT                                    -->
      <!-- Display, search, edit & manage Photo albums                 -->
      <!-- ============================================================ -->
<div x-show="tab === 'manage'" x-cloak x-transition:enter="transition ease-out duration-300">
    <div class="max-w-6xl mx-auto px-4 pb-20">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4 bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
            <div class="relative w-full md:w-96 group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" x-model="search" placeholder="Search albums..." 
                    class="pl-12 pr-4 py-3 w-full border border-gray-100 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all bg-gray-50/50 font-medium">
            </div>

            <div class="flex items-center gap-1 bg-gray-100/50 p-1.5 rounded-2xl border border-gray-100 shadow-inner">
                <button @click="if(page > 1) page--" :disabled="page === 1" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition" :class="page === 1 ? 'text-gray-300' : 'text-gray-600 hover:bg-white hover:shadow-sm'">Prev</button>
                <div class="px-4 py-2 text-[10px] font-black text-blue-900 border-x border-gray-200">Page <span x-text="page"></span> of <span x-text="totalPages"></span></div>
                <button @click="if(page < totalPages) page++" :disabled="page === totalPages" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition" :class="page === totalPages ? 'text-gray-300' : 'text-blue-600 hover:bg-white hover:shadow-sm'">Next</button>
            </div>
        </div>

        @php $albumIndex = 0; @endphp 
        @forelse($albums as $album)
            @php 
                $albumIndex++; 
                $groupedSlides = $album->slides; 
            @endphp
    
            <div class="album-card bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-12 transition-all hover:shadow-xl hover:shadow-blue-500/5"
                 x-data="{ 
                    myIndex: {{ $albumIndex }},
                    localCategory: '{{ addslashes($album->name) }}',
                    localDesc: '{{ addslashes($album->description) }}',
                    PhotoSearch: '',
                    photoPage: 1,
                    photosPerPage: 8,
                    isUpdating: false,
                    get totalPhotos() { return {{ $groupedSlides->count() }} },
                    get totalPhotoPages() { return Math.ceil(this.totalPhotos / this.photosPerPage) }
                 }"
                 x-show="(search === '' || localCategory.toLowerCase().includes(search.toLowerCase())) && (myIndex > (page - 1) * perPage && myIndex <= page * perPage)"
                 x-transition:enter="transition ease-out duration-300">

                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-8 border-b border-gray-50 gap-6">
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-1.5 bg-blue-600 rounded-full"></div>
                            <h3 class="text-3xl font-black text-slate-800 tracking-tighter uppercase" x-text="localCategory"></h3>
                            <button @click="let newName = prompt('Rename Album:', localCategory); if(newName && newName !== localCategory) { localCategory = newName; $nextTick(() => $refs.editAlbumForm.submit()); }" 
                                    class="p-2 text-gray-300 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 group/albdesc pl-4">
                            <p class="text-sm text-slate-400 font-medium leading-relaxed" x-text="localDesc || 'Add an album description...'"></p>
                            <button @click="let newDesc = prompt('Edit Album Description:', localDesc); if(newDesc !== null) { localDesc = newDesc; $nextTick(() => $refs.editAlbumForm.submit()); }" 
                                    class="opacity-0 group-hover/albdesc:opacity-100 p-1 text-blue-400 hover:text-blue-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="relative">
                            <input type="text" x-model="PhotoSearch" @input="photoPage = 1" placeholder="Filter photos..." 
                               class="pl-8 pr-4 py-2.5 border border-gray-100 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none w-44 bg-gray-50/50 transition-all">
                            <svg class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3"/></svg>
                        </div>
                        
                       <button @click="tab = 'upload'; isNewAlbum = false; $nextTick(() => { document.getElementById('album_select').value = '{{ $album->id }}' })" 
                                class="flex items-center gap-2 px-3 py-2 bg-transparent text-blue-600 hover:bg-blue-50 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>

                        <form action="{{ route('albums.destroy', $album->id) }}" method="POST" onsubmit="return confirm('Delete entire album?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                            </button>
                        </form>
                    </div>

                    <form x-ref="editAlbumForm" action="{{ route('albums.update', $album->id) }}" method="POST" class="hidden">
                        @csrf @method('PATCH')
                        <input type="hidden" name="name" :value="localCategory">
                        <input type="hidden" name="description" :value="localDesc">
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @forelse($groupedSlides as $index => $slide)
                        <div x-data="{ 
                                photoId: {{ $slide->id }},
                                photoIdx: {{ $index + 1 }},
                                currentTitle: '{{ addslashes($slide->name) }}',
                                currentDesc: '{{ addslashes($slide->description) }}',
                                isSavingPhoto: false,
                                
                                async updateData(field) {
                                    let oldValue = field === 'title' ? this.currentTitle : this.currentDesc;
                                    let promptMsg = field === 'title' ? 'New Photo Title:' : 'New Description:';
                                    let newValue = prompt(promptMsg, oldValue);
                                    
                                    if (newValue === null || newValue === oldValue) return;

                                    this.isSavingPhoto = true;

                                    try {
                                        const response = await fetch(`/photos/${this.photoId}`, {
                                            method: 'PATCH',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                name: field === 'title' ? newValue : this.currentTitle,
                                                description: field === 'desc' ? newValue : this.currentDesc
                                            })
                                        });

                                        if (response.ok) {
                                            if(field === 'title') this.currentTitle = newValue;
                                            if(field === 'desc') this.currentDesc = newValue;
                                        } else {
                                            alert('Failed to save to database.');
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                    } finally {
                                        this.isSavingPhoto = false;
                                    }
                                }
                            }"
                            x-show="(PhotoSearch === '' || currentTitle.toLowerCase().includes(PhotoSearch.toLowerCase()) || currentDesc.toLowerCase().includes(PhotoSearch.toLowerCase())) && (photoIdx > (photoPage - 1) * photosPerPage && photoIdx <= photoPage * photosPerPage)"
                            x-transition
                            class="relative bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm group hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500">
                            
                            <div x-show="isSavingPhoto" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-50 flex items-center justify-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-blue-600 border-t-transparent"></div>
                                    <span class="text-[8px] font-black text-blue-600 uppercase tracking-widest">Saving...</span>
                                </div>
                            </div>

                            <div class="relative h-48 bg-slate-100">
                                <img src="{{ asset('storage/' . $slide->image_path) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest shadow-2xl backdrop-blur-md {{ $slide->is_active ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white' }}">
                                        {{ $slide->is_active ? 'Public' : 'Hidden' }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between group/title">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <h4 class="text-xs font-black text-slate-800 truncate uppercase tracking-tight" x-text="currentTitle || 'UNTITLED'"></h4>
                                    </div>
                                    <button @click="updateData('title')" class="opacity-0 group-hover/title:opacity-100 p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-all">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5"/></svg>
                                    </button>
                                </div>

                                <div class="bg-slate-50 p-3 rounded-2xl relative">
                                    <p class="text-[10px] text-slate-500 line-clamp-2 leading-relaxed italic pr-4" x-text="currentDesc || 'No description added...'"></p>
                                    <button @click="updateData('desc')" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 p-1 text-blue-400 hover:text-blue-600 transition-all">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg>
                                    </button>
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <form action="{{ route('Photo.toggle', $slide->id) }}" method="POST" class="flex-1">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-full py-2.5 rounded-xl text-[9px] font-black tracking-widest transition-all border-2 {{ $slide->is_active ? 'bg-slate-800 text-white border-slate-800 hover:bg-slate-900' : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-100' }}">
                                            {{ $slide->is_active ? 'HIDE' : 'ACTIVATE' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('Photo.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete this photo?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all border border-transparent hover:border-red-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>  
                    @empty
                        <div class="col-span-full text-center py-20 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 text-slate-400 font-bold italic text-sm">Empty Album.</div>
                    @endforelse
                </div>

                <div class="flex justify-center mt-12" x-show="totalPhotoPages > 1">
                    <div class="flex items-center gap-1 bg-white p-1.5 rounded-2xl border border-gray-100 shadow-lg shadow-blue-500/5">
                        <button @click="if(photoPage > 1) { photoPage--; $el.closest('.album-card').scrollIntoView({behavior: 'smooth'}); }" :disabled="photoPage === 1" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition" :class="photoPage === 1 ? 'text-gray-200' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600'">Prev</button>
                        <div class="px-6 text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Page <span class="text-blue-600" x-text="photoPage"></span> / <span x-text="totalPhotoPages"></span></div>
                        <button @click="if(photoPage < totalPhotoPages) { photoPage++; $el.closest('.album-card').scrollIntoView({behavior: 'smooth'}); }" :disabled="photoPage === totalPhotoPages" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition" :class="photoPage === totalPhotoPages ? 'text-gray-200' : 'text-blue-600 hover:bg-blue-50'">Next</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-10 w-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-slate-400 font-black uppercase tracking-widest text-sm">No albums found.</p>
            </div>
        @endforelse
    </div>
</div>
             <!-- ============================================================ -->
             <!-- RECYCLE BIN / TRASH TAB CONTENT                              -->
             <!-- Manage deleted items, restore or permanently delete         -->
             <!-- ============================================================ -->
             <div x-show="tab === 'trash'" x-cloak x-transition:enter="transition ease-out duration-300">
                <div class="max-w-6xl mx-auto space-y-8">
                    <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-center gap-4">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-blue-900 font-bold">Recycle Bin Storage</p>
                            <p class="text-xs text-blue-700">Deleted items are grouped by their original album. Restoring them will put them back into active rotation.</p>
                        </div>
                    </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="trashSearch" placeholder="Search deleted albums..." 
                    class="pl-10 pr-4 py-3 border border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none transition-all w-full md:w-96 shadow-sm bg-white font-medium">
            </div>
            
            <div class="flex items-center gap-3">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest" x-show="trashSearch.length > 0">
                    Results for: <span x-text="trashSearch" class="text-blue-600"></span>
                </p>
            </div>
        </div>

        @forelse(\App\Models\Photo::onlyTrashed()->get()->groupBy('album_id') as $albumId => $trashedSlides)
            @php $album = \App\Models\Album::withTrashed()->find($albumId); @endphp
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md"
                 x-show="trashSearch === '' || '{{ $album ? strtolower($album->name) : 'deleted' }}'.includes(trashSearch.toLowerCase())">
                
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-700 uppercase tracking-wide">
                                {{ $album ? $album->name : 'Deleted Album' }}
                            </h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $trashedSlides->count() }} Archived Items</p>
                        </div>
                    </div>
            
                    <div class="flex items-center gap-2">
                        <form action="{{ route('Photo.restore-album') }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="album_id" value="{{ $albumId }}">
                            <button type="submit" class="text-[10px] bg-blue-600 text-white px-4 py-2 rounded-xl font-black uppercase hover:bg-blue-700 transition shadow-lg shadow-blue-100 active:scale-95 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="3"></path></svg>
                                Restore Album
                            </button>
                        </form>

                        <form action="{{ route('Photo.delete-album', $albumId) }}" method="POST"
                              onsubmit="return confirm('WARNING: This will permanently delete the entire album. Continue?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="album_id" value="{{ $albumId }}">
                            <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($trashedSlides as $trash)
                        <div class="relative bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden group transition-all hover:border-blue-300">
                            <div class="relative aspect-square overflow-hidden bg-gray-200">
                                <img src="{{ asset('storage/' . $trash->image_path) }}" 
                                    class="w-full h-full object-cover opacity-50 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition duration-500 transform group-hover:scale-110">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 p-2 flex gap-1 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-white/80 backdrop-blur-sm">
                                <form action="{{ route('Photo.restore', $trash->id) }}" method="POST" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button class="w-full py-2 bg-blue-600 text-white text-[9px] font-black rounded-lg hover:bg-blue-700 transition shadow-sm active:scale-95 flex items-center justify-center">
                                        RESTORE
                                    </button>
                                </form>
                                
                                <form action="{{ route('Photo.force-delete', $trash->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Permanently delete?')"
                                      class="px-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <div class="absolute top-2 left-2">
                                <span class="bg-black/50 backdrop-blur-md text-white text-[8px] font-black px-2 py-1 rounded-lg uppercase">Trashed</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-gray-500 font-black uppercase tracking-widest text-sm">Recycle Bin is Empty</h3>
                <p class="text-gray-400 text-xs mt-2 font-medium">No items found in the trash. Your gallery is clean!</p>
            </div>
        @endforelse
    </div>
</div>

                <!-- ============================================================ -->
                <!-- SETTINGS TAB CONTENT                                        -->
                <!-- Configure Photo duration & transition effects           -->
                <!-- ============================================================ -->
<div x-show="tab === 'settings'" x-cloak x-transition:enter="transition ease-out duration-300"
    x-data="{
        albumSearch: '',
        allAlbums: [
            @foreach($albums as $album)
                { id: {{ $album->id }}, name: '{{ addslashes($album->name) }}' },
            @endforeach
        ],
        activeAlbums: [],
        displayAlbumIds: '',

        init() {
            let rawSavedValue = '{{ $settings['display_album_ids'] ?? '' }}';
            let savedIds = rawSavedValue ? rawSavedValue.split(',').map(id => id.trim()) : [];
            this.activeAlbums = this.allAlbums.filter(a => savedIds.includes(a.id.toString()));
            this.updateHiddenInput();
        },

        toggleAlbum(album) {
            const index = this.activeAlbums.findIndex(a => a.id === album.id);
            if (index === -1) {
                this.activeAlbums.push(album);
            } else {
                this.activeAlbums.splice(index, 1);
            }
            this.updateHiddenInput();
        },

        updateHiddenInput() {
            this.displayAlbumIds = this.activeAlbums.map(a => a.id).join(',');
        },

        isSelected(id) {
            return this.activeAlbums.some(a => a.id === id);
        }
    }"
    x-init="init()">

    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">System Configuration</h2>
            <p class="text-sm text-gray-500">Manage how your slideshow behaves and which albums are displayed.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-10">
            @csrf
            
            @php
                $currentDuration = $settings['slide_duration'] ?? 5;
                $currentEffect = $settings['transition_effect'] ?? 'fade';
            @endphp

            <input type="hidden" name="display_album_ids" :value="displayAlbumIds">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <label class="text-sm font-black text-gray-700 uppercase tracking-widest">Slide Duration</label>
                    </div>
                    <div class="relative group">
                        <input type="number" name="slide_duration" value="{{ $currentDuration }}" min="1" max="60"
                               class="w-full pl-5 pr-12 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none transition-all text-lg font-bold text-gray-800">
                        <span class="absolute right-5 top-4.5 text-gray-400 font-bold uppercase text-[10px] tracking-widest">sec</span>
                    </div>
                    <p class="text-[10px] text-gray-400 italic px-1">How many seconds each slide stays on screen.</p>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-indigo-50 rounded-lg">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z" stroke-width="2"></path>
                                <path d="M9 12l2 2 4-4" stroke-width="2"></path>
                            </svg>
                        </div>
                        <label class="text-sm font-black text-gray-700 uppercase tracking-widest">Transition Effect</label>
                    </div>
                    <select name="transition_effect" class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none bg-white transition-all font-bold text-gray-700 cursor-pointer appearance-none">
                        <option value="fade" {{ $currentEffect == 'fade' ? 'selected' : '' }}>Smooth Fade</option>
                        <option value="slide-up" {{ $currentEffect == 'slide-up' ? 'selected' : '' }}>Slide Upward</option>
                        <option value="slide-down" {{ $currentEffect == 'slide-down' ? 'selected' : '' }}>Slide Downward</option>
                        <option value="slide-left" {{ $currentEffect == 'slide-left' ? 'selected' : '' }}>Slide Leftward</option>
                        <option value="slide-right" {{ $currentEffect == 'slide-right' ? 'selected' : '' }}>Slide Rightward</option>
                    </select>
                </div>
            </div>

            <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <label class="text-sm font-black text-gray-700 uppercase tracking-widest">Active Albums</label>
                        <p class="text-xs text-gray-400">Select which photo albums will appear in the rotation.</p>
                    </div>
                    <div class="relative w-64">
                        <input type="text" x-model="albumSearch" placeholder="Filter albums..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border-none rounded-xl text-xs focus:ring-2 focus:ring-blue-600 outline-none transition-all">
                        <svg class="h-3.5 w-3.5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            Available Library 
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full" x-text="allAlbums.length"></span>
                        </h4>
                        <div class="bg-gray-50/50 rounded-2xl p-3 border border-gray-100 h-[300px] overflow-y-auto space-y-2">
                            <template x-for="album in allAlbums.filter(a => a.name.toLowerCase().includes(albumSearch.toLowerCase()))" :key="album.id">
                                <div @click="toggleAlbum(album)" 
                                     class="group flex items-center justify-between p-3 rounded-xl cursor-pointer transition-all border"
                                     :class="isSelected(album.id) ? 'bg-white border-blue-500 shadow-md scale-[1.02]' : 'bg-white border-transparent hover:border-gray-300 shadow-sm'">
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                             :class="isSelected(album.id) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200'">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold transition-colors" :class="isSelected(album.id) ? 'text-blue-800' : 'text-gray-600'" x-text="album.name"></span>
                                    </div>
                                    
                                    <div class="transition-transform duration-300" :class="isSelected(album.id) ? 'rotate-90' : ''">
                                        <svg class="w-4 h-4" :class="isSelected(album.id) ? 'text-blue-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            Slideshow Queue
                            <span class="bg-blue-600 text-white px-2 py-0.5 rounded-full" x-text="activeAlbums.length"></span>
                        </h4>
                        <div class="bg-blue-50/30 rounded-2xl p-3 border-2 border-dashed border-blue-100 h-[300px] overflow-y-auto space-y-2">
                            <template x-for="album in activeAlbums" :key="album.id">
                                <div @click="toggleAlbum(album)" class="flex items-center justify-between p-3 bg-white border border-blue-100 rounded-xl cursor-pointer hover:border-blue-600 transition-all shadow-sm group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></div>
                                        <span class="text-sm font-bold text-gray-800" x-text="album.name"></span>
                                    </div>
                                    <button type="button" class="p-1 rounded-md group-hover:bg-blue-50 text-blue-300 group-hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            
                            <template x-if="activeAlbums.length === 0">
                                <div class="h-full flex flex-col items-center justify-center text-center p-6">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-gray-400">Queue Empty</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Select albums from the library to start.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex items-center justify-between">
                <a href="/" target="_blank" class="group flex items-center gap-2 text-sm font-black text-gray-400 hover:text-blue-700 transition-colors">
                    <div class="p-2 bg-gray-50 group-hover:bg-blue-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"></path>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"></path>
                        </svg>
                    </div>
                    LIVE PREVIEW
                </a>
                
                <button type="submit" class="relative overflow-hidden group bg-blue-700 text-white px-12 py-4 rounded-2xl font-black tracking-widest text-xs hover:bg-blue-800 transition-all shadow-xl shadow-blue-200 active:scale-95">
                    <span class="relative z-10 uppercase">Apply Changes</span>
                    <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<!-- ============================================================ -->
<!-- END OF DASHBOARD                                             -->
<!-- ============================================================ -->