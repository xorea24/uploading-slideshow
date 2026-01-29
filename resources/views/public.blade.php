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

        .admin-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 20;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-black">

    {{-- Fetch Global Settings --}}
    @php
        $seconds = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
        $effect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';
    @endphp

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
                    <p class="text-sm text-gray-200">{{ $slide->category_name }}</p>
                </div>
            </div>
        @empty
            <div class="swiper-slide flex items-center justify-center bg-gray-900 text-white">
                <p>No images available in the gallery.</p>
            </div>
        @endforelse
        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-next !text-white"></div> 
        <div class="swiper-button-prev !text-white"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // 1. Get Settings from PHP
        const slideDuration = {{ $seconds }} * 1000; 
        const effectSetting = "{{ $effect }}";

        // 2. Build Swiper Options
        let swiperOptions = {
            loop: true,
            speed: 1000,
            autoplay: {
                delay: slideDuration,
                disableOnInteraction: false,
            },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        };

     // 3. Apply the Effect based on Admin choice
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
else if (effectSetting === 'slide-left') {
    swiperOptions.direction = 'horizontal'; // Standard behavior
}
else if (effectSetting === 'slide-right') {
    swiperOptions.direction = 'horizontal';
    swiperOptions.effect = 'creative';
    swiperOptions.creativeEffect = {
        prev: { translate: ['100%', 0, 0] },
        next: { translate: ['-100%', 0, 0] },
    };
}
        else if (effectSetting === 'zoom') {
            swiperOptions.effect = 'fade';
            // Subtle zoom effect via CSS
            document.styleSheets[0].insertRule('.swiper-slide-active img { transform: scale(1.1); transition: transform ' + slideDuration + 'ms linear; }', 0);
        }

        // 4. Initialize
        var swiper = new Swiper(".mySwiper", swiperOptions);
    </script>
</body>
</html>