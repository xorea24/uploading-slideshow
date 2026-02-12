@php
    // 1. FETCH SETTINGS
    $settings = \DB::table('settings')->get()->keyBy('key');
    
    $seconds = $settings->get('slide_duration')->value ?? 5;
    $effect = $settings->get('transition_effect')->value ?? 'fade';
    $displayAlbumIds = $settings->get('display_album_ids')->value ?? '';

    // Convert saved string "1,2,3" into an array
    $albumIdArray = array_filter(explode(',', $displayAlbumIds));

    // 2. FETCH SLIDES CONNECTED TO SELECTED ALBUMS
    $slidesQuery = \DB::table('photos')
        ->join('albums', 'photos.album_id', '=', 'albums.id')
        ->select('photos.*', 'albums.name as album_title', 'albums.description as album_desc');

    // Filter by albums selected in settings
    if (!empty($albumIdArray)) {
        $slidesQuery->whereIn('photos.album_id', $albumIdArray);
    }

    $slides = $slidesQuery->orderBy('photos.created_at', 'desc')->get();

    // 3. MASTER TIMESTAMP (Para sa Auto-Reload)
    $lastSettingUpdate = \DB::table('settings')->max('updated_at');
    $lastImageUpdate = \DB::table('photos')->max('updated_at');
    $lastAlbumUpdate = \DB::table('albums')->max('updated_at');
    $masterTimestamp = max($lastSettingUpdate, $lastImageUpdate, $lastAlbumUpdate);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Access - Mayor's Office</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
   <style>
    html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; background-color: black; }
    .swiper { width: 100%; height: 100vh; }
    .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

    /* SIMPLE TOP OVERLAY */
    .title-overlay {
        position: absolute;
        top: 30px;
        left: 30px;
        z-index: 100;
        max-width: 80%;
        pointer-events: none;
    }

    .title-card {
        padding: 15px 20px;
        color: white;
    }

    /* SIMPLE LOADING OVERLAY */
    #loading-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: black;
        color: white;
        transition: opacity 0.5s;
    }

    .hidden-overlay { opacity: 0; pointer-events: none; }
    .visible-overlay { opacity: 1; pointer-events: auto; }
</style>
</head>
<body>

    <div id="loading-overlay" class="hidden-overlay">
        <div class="relative w-24 h-24 mb-6">
            <div class="absolute inset-0 border-4 border-gray-800 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-red-600 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <h2 class="text-white text-xl font-light tracking-[0.2em] uppercase animate-pulse">
            Updating Album...
        </h2>
    </div>    b
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
        @forelse($slides as $slide)
            <div class="swiper-slide relative">
                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Slideshow Image">
                
                <div class="title-overlay">
                    <div class="title-card">
                        <h1 class="text-white text-5xl font-black uppercase tracking-tighter">
                            {{ $slide->album_title }}
                        </h1>
                        @if($slide->album_desc)
                            <p class="text-gray-200 text-lg mt-2 font-medium border-t border-white/10 pt-2">
                                {{ $slide->album_desc }}
                            </p>
                        @endif
                        <div class="mt-3 flex items-center gap-3">
                            <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded">OFFICIAL</span>
                            <span class="text-gray-400 text-xs uppercase tracking-widest">
                                {{ date('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="swiper-slide flex items-center justify-center bg-gray-900 text-white text-center">
                <div>
                    <p class="text-2xl font-bold mb-2">No Albums Selected</p>
                    <p class="text-gray-400">Go to Settings to select which albums to display.</p>
                </div>
            </div>
        @endforelse
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
    // 1. DATA REFRESH LOGIC (Auto-reloads when settings change)
    let currentUpdateTimestamp = "{{ $masterTimestamp }}";

    async function fetchLatestData() {
        try {
            const response = await fetch('/api/get-latest-settings?t=' + Date.now()); 
            const data = await response.json();

            if (data.last_update && data.last_update > currentUpdateTimestamp) {
                showLoadingAndReload();
            }
        } catch (e) {
            console.log("Check update failed, retrying later...");
        }
    }

    function showLoadingAndReload() {
        const overlay = document.getElementById('loading-overlay');
        overlay.classList.remove('hidden-overlay');
        overlay.classList.add('visible-overlay');
        setTimeout(() => { window.location.reload(); }, 2000);
    }

    // Check every 10 seconds for database changes
    setInterval(fetchLatestData, 10000);

    // 2. SWIPER INITIALIZATION
    const slideDuration = {{ $seconds }} * 1000; 
    const effectSetting = "{{ $effect }}";

    let swiperOptions = {
        loop: true,
        speed: 1200,
        autoplay: {
            delay: slideDuration,
            disableOnInteraction: false,
        },
        pagination: { el: ".swiper-pagination", clickable: true },
    };

    // Apply Effects based on Settings
    if (effectSetting === 'fade') {
        swiperOptions.effect = 'fade';
        swiperOptions.fadeEffect = { crossFade: true };
    } 
    else if (effectSetting === 'slide-up') {
        swiperOptions.direction = 'vertical';
    } 
    else if (effectSetting === 'slide-down') {
        swiperOptions.direction = 'vertical';
        swiperOptions.effect = 'creative';
        swiperOptions.creativeEffect = {
            prev: { translate: [0, '100%', 0] },
            next: { translate: [0, '-100%', 0] },
        };
    }
    else if (effectSetting === 'slide-right') {
        swiperOptions.effect = 'creative';
        swiperOptions.creativeEffect = {
            prev: { translate: ['100%', 0, 0] },
            next: { translate: ['-100%', 0, 0] },
        };
    }

    var swiper = new Swiper(".mySwiper", swiperOptions);
    </script>
</body>
</html>