<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Access - Mayor's Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        /* Full screen setup */
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; }
        
        .swiper { width: 100%; height: 100vh; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

        /* Styling para sa title overlay */
        .title-overlay {
            position: absolute;
            bottom: 10%;
            left: 5%;
            z-index: 10;
        }

        /* Floating login button */
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
    <a href="/login" class="admin-btn text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-white hover:text-black transition">
        Admin Login
    </a>

    <div class="absolute top-5 left-5 z-20 pointer-events-none">
        <h1 class="text-white text-3xl font-bold drop-shadow-lg">Mayor's Office Gallery</h1>
    </div>

    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
        @forelse($slides as $slide)
            <div class="swiper-slide">
                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}">
                <div class="title-overlay bg-black/50 text-white px-6 py-3 rounded-lg backdrop-blur-md">
                    <h2 class="text-2xl font-bold">{{ $slide->title }}</h2>
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
    var swiper = new Swiper(".mySwiper", {
        loop: true, // Paulit-ulit na iikot ang slides
        speed: 1000, // Bilis ng mismong pag-swipe (1 second)
        
        // Tinanggal ang effect: "fade" para maging slide transition
        autoplay: { 
            delay: 2000, // Lilipat bawat 3 segundo
            disableOnInteraction: false, // Kahit i-click, tuloy pa rin ang auto-play
            pauseOnMouseEnter: false, // Hindi titigil kahit itapat ang mouse
        },

        pagination: { 
            el: ".swiper-pagination", 
            clickable: true 
        },

        navigation: { 
            nextEl: ".swiper-button-next", 
            prevEl: ".swiper-button-prev" 
        },
    });
</script>
</body>
</html>