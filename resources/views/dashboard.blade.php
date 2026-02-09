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
        
        get totalVisible() { return document.querySelectorAll('.album-card').length; },
        get totalPages() { return Math.ceil(this.totalVisible / this.perPage) || 1; },
        trashSearch: ''
    }">
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
     <aside class="w-64 bg-red-950 text-white fixed md:static inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <!-- Sidebar Header with Logo -->
    <div class="p-6">   
       <img src="{{ asset('image/stc_logo2.png') }}" alt="Logo" class="w-20 h-20 mb-4 mx-auto md:mx-0">
        <h1 class="text-2xl font-bold tracking-wider uppercase">Mayor's <span class="text-red-400">Office</span></h1>
        <p class="text-[10px] text-red-300 tracking-[0.2em] mt-1">UPLOADING SYSTEM</p>
    </div>  

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 space-y-2 mt-4">
        <a href="#" @click.prevent="tab = 'manage'; sidebarOpen = false" 
            :class="tab === 'manage' ? 'bg-red-600 text-white shadow-lg' : 'text-red-200 hover:bg-red-900'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
                {{ \App\Models\Photo::onlyTrashed()->count() }}
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

    <!-- Logout Section -->
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
                        <p class="text-xs font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">System Administrator</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold border-2 border-red-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
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
          <div x-show="tab === 'upload'" x-cloak x-transition>
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('Photo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase">Target Album</label>
                    <select name="album_id" @change="isNewAlbum = $el.value === 'new'" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 outline-none text-sm">
                        <option value="">-- No Album (Unassigned) --</option>
                        @foreach($albums as $album)
                            <option value="{{ $album->id }}">{{ $album->name }}</option>
                        @endforeach
                        <option value="new" class="font-bold text-red-600">+ Create New Album</option>
                    </select>
                </div>

                <div x-show="isNewAlbum" x-transition class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase">New Album Details</label>
                    <input type="text" name="new_album_name" placeholder="Album Name" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm mb-2">
                    <input type="text" name="new_album_desc" placeholder="Brief Description" class="w-full px-4 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="space-y-4">
                <template x-for="(row, index) in uploadRows" :key="row.id">
                    <div class="flex flex-col md:flex-row gap-4 p-4 bg-white border border-gray-200 rounded-xl relative group">
                        <div class="w-full md:w-32 h-32 bg-gray-100 rounded-lg flex-shrink-0 border-2 border-dashed border-gray-300 overflow-hidden relative">
                            <template x-if="row.preview">
                                <img :src="row.preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!row.preview">
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </template>
                            <input type="file" name="images[]" required class="absolute inset-0 opacity-0 cursor-pointer" @change="handleFileChange($event, index)">
                        </div>

                        <div class="flex-1 grid grid-cols-1 gap-3">
                            <input type="text" name="titles[]" placeholder="Image Title (Required)" required class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-red-500 outline-none">
                            <textarea name="descriptions[]" placeholder="Description (Optional)" class="w-full px-4 py-2 rounded-lg border border-gray-200 text-sm focus:border-red-500 outline-none h-16"></textarea>
                        </div>

                        <button type="button" x-show="uploadRows.length > 1" @click="removePhotoRow(index)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
            </div>

            <div class="flex justify-between items-center pt-4">
                <button type="button" @click="addPhotoRow()" class="flex items-center gap-2 text-red-700 font-bold text-sm hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Add More Photo
                </button>

                <button type="submit" class="bg-red-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-900 transition shadow-lg">
                    Save All Images
                </button>
            </div>
        </form>
    </div>
</div>

      <!-- ============================================================ -->
      <!-- MANAGE ALBUMS TAB CONTENT                                    -->
      <!-- Display, search, edit & manage Photo albums                 -->
      <!-- ============================================================ -->
   <div x-show="tab === 'manage'" x-cloak x-transition>
    <div class="max-w-6xl mx-auto px-4 pb-20">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" x-model="search" placeholder="Search albums..." class="pl-10 pr-4 py-2 w-full border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 outline-none shadow-sm">
            </div>

            <div class="flex items-center gap-1 bg-gray-50 p-1 rounded-xl border border-gray-100 shadow-sm">
                <button @click="if(page > 1) page--" :disabled="page === 1" class="px-3 py-1.5 text-xs font-bold rounded-lg" :class="page === 1 ? 'text-gray-300' : 'text-gray-600 hover:bg-white'">Prev</button>
                <div class="px-3 py-1.5 text-xs font-bold text-red-900 border-x border-gray-200">Page <span x-text="page"></span> of <span x-text="totalPages"></span></div>
                <button @click="if(page < totalPages) page++" :disabled="page === totalPages" class="px-3 py-1.5 text-xs font-bold rounded-lg" :class="page === totalPages ? 'text-gray-300' : 'text-red-600 hover:bg-white'">Next</button>
            </div>
        </div>

        {{-- Album Card --}}
        @php $albumIndex = 0; @endphp 
            @forelse($albums as $album)
                 @php 
                    $albumIndex++; 
                        $groupedSlides = $album->slides; 
                @endphp
    
    <div class="album-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6"
    x-data="{ 
        myIndex: {{ $albumIndex }},
        localCategory: '{{ addslashes($album->name) }}',
        PhotoSearch: ''
         }"
         x-show="(search === '' || localCategory.toLowerCase().includes(search.toLowerCase())) && (myIndex > (page - 1) * perPage && myIndex <= page * perPage)"
         x-transition:enter="transition ease-out duration-300">

        <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-red-900" x-text="localCategory"></h3>
                    <button @click="newName = prompt('Enter new album name:', localCategory); if(newName && newName !== localCategory) { $refs.editFormInput.value = newName; $refs.editForm.submit(); }" 
                            class="p-1 text-gray-400 hover:text-blue-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <p class="text-xs text-gray-500">{{ $groupedSlides->count() }} total Photos</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                {{-- NEW: Photo Search Bar --}}
                <div class="relative hidden sm:block">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" x-model="PhotoSearch" placeholder="Search Photos..." 
                           class="pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-[11px] focus:ring-1 focus:ring-red-500 outline-none w-32 md:w-48 transition-all">
                </div>

                {{-- Add Photo Button --}}
                <button @click="tab = 'upload'; isNewAlbum = false; $nextTick(() => { document.getElementById('album_select').value = '{{ $album->id }}' })" 
                        class="p-1.5 text-blue-600 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </button>
                
                {{-- Delete Album Button --}}
                <form action="{{ route('albums.destroy', $album->id) }}" method="POST" onsubmit="return confirm('Move ENTIRE album to Recycle Bin?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-red-500 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Photo Grid with Filtering --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            @forelse($groupedSlides as $slide)
                <div x-show="PhotoSearch === '' || '{{ addslashes($slide->title) }}'.toLowerCase().includes(PhotoSearch.toLowerCase()) || '{{ addslashes($slide->description) }}'.toLowerCase().includes(PhotoSearch.toLowerCase())"
                     x-transition
                     class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden shadow-sm group transition hover:border-red-200">
                    
                    <div class="relative">
                        <img src="{{ asset('storage/' . $slide->image_path) }}" 
                            class="w-full h-32 object-cover group-hover:opacity-80 transition duration-300"
                            alt="{{ $slide->title }}">
                        
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase {{ $slide->is_active ? 'bg-green-500/80 text-white' : 'bg-gray-500/80 text-white' }}">
                                {{ $slide->is_active ? 'Visible' : 'Hidden' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="mb-3">
                            <h4 class="text-xs font-bold text-gray-800 truncate" title="{{ $slide->title}}">
                                {{ $slide->title }}
                            </h4>
                            @if($slide->description)
                                <p class="text-[10px] text-gray-500 line-clamp-1 mt-0.5">
                                    {{ $slide->description }}
                                </p>
                            @else
                                <p class="text-[10px] text-gray-400 italic mt-0.5">No description</p>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <form action="{{ route('Photo.toggle', $slide->id) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <button class="w-full py-1.5 {{ $slide->is_active ? 'bg-yellow-600' : 'bg-green-600' }} text-white text-[10px] font-bold rounded-lg transition">
                                    {{ $slide->is_active ? 'HIDE' : 'SHOW' }}
                                </button>
                            </form>
                            <form action="{{ route('Photo.destroy', $slide->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-500 hover:bg-red-50 border border-red-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-4 text-gray-400 italic text-sm">No Photos in this album.</div>
            @endforelse
        </div>
    </div>
@empty
    <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
        <p class="text-gray-400">No albums found.</p>
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
        <div class="bg-red-50 p-4 rounded-xl border border-red-100 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-red-800 font-medium">Deleted items are grouped by their original album. Restore them to put them back in the gallery.</p>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="trashSearch" placeholder="Search deleted albums..." 
                    class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 outline-none transition w-full md:w-80 shadow-sm bg-white">
            </div>
            <p class="text-xs text-gray-400 font-medium" x-show="trashSearch.length > 0">
                Found in Recycle bin: "<span x-text="trashSearch" class="text-red-900"></span>"
            </p>
        </div>

        @forelse(\App\Models\Photo::onlyTrashed()->get()->groupBy('album_id') as $albumId => $trashedSlides)
            @php $album = \App\Models\Album::withTrashed()->find($albumId); @endphp
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
                 x-show="trashSearch === '' || '{{ $album ? strtolower($album->name) : 'deleted' }}'.includes(trashSearch.toLowerCase())">
                
                <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-3">
                    <h3 class="text-md font-bold text-gray-700">
                        Album: <span class="text-red-900">{{ $album ? $album->name : 'Deleted Album' }}</span>
                    </h3>
            
                    <div class="flex items-center gap-2">
                    <form action="{{ route('Photo.restore-album') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="album_id" value="{{ $albumId }}">
                                <button type="submit" class="text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-md font-bold uppercase hover:bg-green-200 transition">
                                    Restore All
                                </button>
                            </form>

                        <form action="{{ route('Photo.delete-album', $albumId) }}" method="POST"
                          onsubmit="return confirm('Are you sure? This will Be permanently deleted.')">
                            
                            @csrf @method('DELETE')
                            <input type="hidden" name="album_id" value="{{ $albumId }}">
                             <button class="p-1.5 text-red-500 hover:bg-red-50 border border-red-100 rounded-lg transition active:scale-95">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                        </form>
                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-md font-bold uppercase">
                            {{ $trashedSlides->count() }} Items 
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($trashedSlides as $trash)
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden shadow-sm group transition hover:border-red-200">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $trash->image_path) }}" 
                                    class="w-full h-32 object-cover opacity-60 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition duration-300">
                                <div class="absolute top-2 left-2">
                                    <span class="bg-red-600 text-white text-[8px] font-bold px-2 py-1 rounded-full uppercase shadow-sm">Deleted</span>
                                </div>
                            </div>

                            <div class="p-3 space-y-2">
                                <div class="flex gap-2">
                                    <form action="{{ route('Photo.restore', $trash->id) }}" method="POST" class="flex-1">
                                        @csrf @method('PATCH')
                                   <button class="w-full py-1.5 bg-green-600 text-white text-[10px] font-bold rounded-lg hover:bg-green-700 transition shadow-sm active:scale-95 flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    </form>
                                    
                                    <form action="{{ route('Photo.force-delete', $trash->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Permanently delete this image? This cannot be undone.')"
                                        class="flex-1">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-1.5 bg-red-600 text-white text-[10px] font-bold rounded-lg hover:bg-red-700 transition shadow-sm active:scale-95">
                                            DELETE PERMANENTLY
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-100">
                <div class="mb-4 flex justify-center text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <p class="text-gray-400 font-medium">The Recycle Bin is empty.</p>
            </div>
        @endforelse
    </div>
</div>

                <!-- ============================================================ -->
                <!-- SETTINGS TAB CONTENT                                        -->
                <!-- Configure Photo duration & transition effects           -->
                <!-- ============================================================ -->
    <div x-show="tab === 'settings'" x-cloak x-transition:enter="transition ease-out duration-300">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @php
                $currentDuration = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
                $currentEffect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';
                // Fetch the currently selected album ID and all available albums
                $selectedAlbumId = \DB::table('settings')->where('key', 'display_album_id')->value('value');
                $albums = \App\Models\Album::all(); // Ensure you have an Album model
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Slide Duration (Seconds)</label>
                    <div class="relative">
                        <input type="number" name="slide_duration" value="{{ $currentDuration }}" min="1" max="60"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none transition pr-12">
                        <span class="absolute right-4 top-3.5 text-gray-400 text-sm font-bold">sec</span>
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium">Controls the speed of the Photo.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Transition Effect</label>
                    <select name="transition_effect" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none bg-white transition cursor-pointer">
                        <option value="fade" {{ $currentEffect == 'fade' ? 'selected' : '' }}>Fade (Smooth)</option>
                        <option value="slide-up" {{ $currentEffect == 'slide-up' ? 'selected' : '' }}>Slide Up</option>
                        <option value="slide-down" {{ $currentEffect == 'slide-down' ? 'selected' : '' }}>Slide Down</option>
                        <option value="slide-left" {{ $currentEffect == 'slide-left' ? 'selected' : '' }}>Slide Left</option>
                        <option value="slide-right" {{ $currentEffect == 'slide-right' ? 'selected' : '' }}>Slide Right</option>
                    </select>
                    <p class="text-[10px] text-gray-400 font-medium">How the images move during change.</p>
                </div>

            
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Display Album</label>
                    
                    <!-- Search Input -->
                    <div class="relative mb-4">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" x-model="albumSearch" placeholder="Search albums..." 
                            class="pl-10 pr-4 py-2 w-full border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 outline-none shadow-sm transition">
                    </div>

                    <!-- Hidden input for form submission -->
        <input type="hidden" name="display_album_id" :value="selectedAlbumId">

{{-- Photo Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
    @forelse($groupedSlides as $slide)
        <div class="bg-gray-50 rounded-xl border-2 transition-all duration-200 overflow-hidden shadow-sm group"
             :class="{{ $slide->is_active }} ? 'border-green-500 shadow-md' : 'border-gray-200 hover:border-red-200'">
            
            <div class="relative">
                <img src="{{ asset('storage/' . $slide->image_path) }}" 
                     class="w-full h-32 object-cover transition duration-300"
                     :class="{{ $slide->is_active }} ? 'opacity-100' : 'opacity-60 grayscale-[50%]'"
                     alt="{{ $slide->title }}">
                
                <div class="absolute top-2 right-2">
                    <span x-show="{{ $slide->is_active }}" class="bg-green-600 text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase">Visible</span>
                    <span x-show="!{{ $slide->is_active }}" class="bg-gray-500 text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase">Hidden</span>
                </div>
            </div>

            <div class="p-3">
                <div class="flex gap-2">
                    {{-- Toggle Visibility Button --}}
                    <form action="{{ route('Photo.toggle', $slide->id) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        
                        <button x-show="!{{ $slide->is_active }}" 
                                class="w-full py-1.5 bg-green-600 text-white text-[10px] font-bold rounded-lg hover:bg-green-700 transition active:scale-95 flex items-center justify-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            SHOW
                        </button>

                        <button x-show="{{ $slide->is_active }}" 
                                class="w-full py-1.5 bg-yellow-500 text-white text-[10px] font-bold rounded-lg hover:bg-yellow-600 transition active:scale-95 flex items-center justify-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            HIDE
                        </button>
                    </form>

                    {{-- Delete Button --}}
                    <form action="{{ route('Photo.destroy', $slide->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-red-500 hover:bg-red-50 border border-red-100 rounded-lg transition active:scale-95" title="Move to trash">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <p class="text-gray-400 italic text-sm">No Photos in this album.</p>
        </div>
    @endforelse
</div>

                    <p class="text-[10px] text-gray-400 font-medium mt-2">Click "Show" to display this album in the Photo, or "Hide" to deselect it.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                <a href="/" target="_blank" class="text-sm font-bold text-red-700 hover:underline flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Preview Photo
                </a>
                <button type="submit" class="bg-red-700 text-white px-10 py-3 rounded-xl font-bold hover:bg-red-800 transition">
                    Save Settings
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