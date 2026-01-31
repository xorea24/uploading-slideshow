@php
    // 1. Fetch Global Settings
    $seconds = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
    $effect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';
    
    // 2. Identify the image table (using your provided logic)
    $imageTable = \Schema::hasTable('slideshows') ? 'slideshows' : (\Schema::hasTable('slides') ? 'slides' : 'albums');
    
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
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; }
        .swiper { width: 100%; height: 100vh; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

        .title-overlay {
            position: absolute;
            bottom: 10%;
            left: 5%;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-black">

    <div id="loading-overlay" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black transition-opacity duration-700 opacity-0 pointer-events-none">
        <div class="relative w-24 h-24 mb-6">
            <div class="absolute inset-0 border-4 border-gray-800 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <h2 class="text-white text-xl font-light tracking-[0.2em] uppercase animate-pulse">
            Updating Album...
        </h2>
    </div>

    <div class="absolute top-5 left-5 z-20 pointer-events-none">
        <h1 class="text-white text-3xl font-bold drop-shadow-lg">Mayor's Office Gallery</h1>
        <p class="text-white text-sm drop-shadow-lg">Enjoy the slideshow of our city's highlights</p>
    </div>

    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
        @forelse($slides as $slide)
            <div class="swiper-slide">
                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}">
                <div class="title-overlay bg-black/50 text-white px-6 py-3 rounded-lg backdrop-blur-md">
                    <h2 class="text-2xl font-bold">{{ $slide->title }}</h2>
                    <p class="text-sm text-gray-200">{{ $slide->category_name ?? 'Gallery' }}</p>
                </div>
            </div>
        @empty
            <div class="swiper-slide flex items-center justify-center bg-gray-900 text-white">
                <p>No images available in the gallery.</p>
            </div>
        @endforelse
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // MASTER UPDATE LOGIC
       // Initialize with the timestamp from the server
        let currentUpdateTimestamp = "{{ $masterTimestamp }}";

        async function fetchLatestData() {
            try {
                // Add a cache-buster query string to prevent browser caching the API response
                const response = await fetch('/api/get-latest-settings?t=' + Date.now()); 
                const data = await response.json();

                // Compare timestamps
                if (data.last_update && data.last_update > currentUpdateTimestamp) {
                    console.log("New content detected! Reloading...");
                    showLoadingAndReload();
                }
            } catch (e) {
                console.error("Update check failed. Retrying in 10s...");
            }
        }

        function showLoadingAndReload() {
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');

            // Give the user 2.5 seconds to read "Updating Album..."
            setTimeout(() => {
                window.location.reload();
            }, 2500);
        }

    // Check every 10 seconds
    setInterval(fetchLatestData, 10000);
        // SWIPER INIT
        const slideDuration = {{ $seconds }} * 1000; 
        const effectSetting = "{{ $effect }}";

        let swiperOptions = {
            loop: true,
            speed: 1000,
            autoplay: {
                delay: slideDuration,
                disableOnInteraction: false,
            },
            pagination: { el: ".swiper-pagination", clickable: true },
        };

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
            document.styleSheets[0].insertRule('.swiper-slide-active img { transform: scale(1.15); transition: transform ' + (slideDuration + 1000) + 'ms linear; }', 0);
        }

        var swiper = new Swiper(".mySwiper", swiperOptions);
    </script>
</body>
</html>