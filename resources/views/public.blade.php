@php
    // 1. Fetch Global Settings
    $seconds = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
    $effect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';
    
    // 2. Identify the image table
    $imageTable = \Schema::hasTable('PhotosController') ? 'PhotosController' : (\Schema::hasTable('slides') ? 'slides' : 'albums');
    
    // 3. Get the "Master Timestamp"
    $lastSettingUpdate = \DB::table('settings')->max('updated_at');
    $lastImageUpdate = \DB::table($imageTable)->max('updated_at');
    $masterTimestamp = max($lastSettingUpdate, $lastImageUpdate);
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

        .title-overlay {
            position: absolute;
            bottom: 10%;
            left: 5%;
            z-index: 10;
        }

        /* FIXED OVERLAY CSS */
        #loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999; /* Ensure it is above Swiper */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.95);
            transition: opacity 0.7s ease-in-out;
        }

        .hidden-overlay {
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        .visible-overlay {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
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
    </div>

    <div class="absolute top-5 left-5 z-20 pointer-events-none">
        <h1 class="text-white text-3xl font-bold drop-shadow-2xl">Mayor's Office Gallery</h1>
        <p class="text-white text-sm drop-shadow-lg opacity-80">Live Photo Feed</p>
    </div>

    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
        @forelse($slides as $slide)
            <div class="swiper-slide">
                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->category_name }}">
                <div class="title-overlay bg-black/40 text-white px-6 py-3 rounded-lg backdrop-blur-md border border-white/10">
                    <h2 class="text-2xl font-bold">{{ $slide->category_name }}</h2>
                    <p class="text-xs text-gray-300 uppercase tracking-widest">
                        {{ $slide->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
        @empty
            <div class="swiper-slide flex items-center justify-center bg-gray-900 text-white text-center">
                <div>
                    <p class="text-2xl font-bold mb-2">Gallery Empty</p>
                    <p class="text-gray-400">Waiting for administrator to upload PhotosController...</p>
                </div>
            </div>
        @endforelse
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
    // 1. DATA REFRESH LOGIC
    let currentUpdateTimestamp = "{{ $masterTimestamp }}";

    async function fetchLatestData() {
        try {
            const response = await fetch('/api/get-latest-settings?t=' + Date.now()); 
            const data = await response.json();

            // Compare timestamps
            if (data.last_update && data.last_update > currentUpdateTimestamp) {
                console.log("Change detected: Server " + data.last_update + " vs Local " + currentUpdateTimestamp);
                showLoadingAndReload();
            }
        } catch (e) {
            console.error("Update check failed.");
        }
    }

    function showLoadingAndReload() {
        const overlay = document.getElementById('loading-overlay');
        overlay.classList.remove('hidden-overlay');
        overlay.classList.add('visible-overlay');

        // Allow 2.5s for visual feedback before refreshing
        setTimeout(() => {
            window.location.reload();
        }, 2500);
    }

    // Check every 10 seconds
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

    // Effect Logic
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
    else if (effectSetting === 'zoom') {
        swiperOptions.effect = 'fade';
        // Apply Ken Burns style zoom via CSS rule injection
        const style = document.createElement('style');
        style.innerHTML = `.swiper-slide-active img { transform: scale(1.2); transition: transform ${slideDuration + 1000}ms linear !important; }`;
        document.head.appendChild(style);
    }

    var swiper = new Swiper(".mySwiper", swiperOptions);
    </script>
</body>
</html>