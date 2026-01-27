<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Access - Mayor's Office</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        /* CSS RESET & FULLSCREEN LOGIC */
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            overflow: hidden; /* Pinipigilan ang scrolling ng buong page */
        }
        
        /* Ginagawa nating 100% ng screen height ang slider */
        .swiper { width: 100%; height: 100vh; }

        /* Sinisiguro na ang image ay hindi gepay (stretched). 
           'object-fit: cover' acts like a background-size: cover. */
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

        /* TITLE OVERLAY: Positioning the text box over the image */
        .title-overlay {
            position: absolute;
            bottom: 10%;
            left: 5%;
            z-index: 10;
        }

        /* FLOATING ADMIN BUTTON: Naka-fixed sa top-right kahit umiikot ang slides */
        .admin-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 20; /* Dapat mas mataas sa swiper para mapindot */
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px); /* Modern glassmorphism effect */
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
        
        {{-- LARAVEL LOOP: Kinukuha ang data mula sa database --}}
        @forelse($slides as $slide)
            <div class="swiper-slide">
                {{-- Asset helper targets the public/storage folder --}}
                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}">
                
                <div class="title-overlay bg-black/50 text-white px-6 py-3 rounded-lg backdrop-blur-md">
                    <h2 class="text-2xl font-bold">{{ $slide->title }}</h2>
                </div>
            </div>
        @empty
            {{-- FALLBACK: Kung walang data sa database, ito ang lalabas --}}
            <div class="swiper-slide flex items-center justify-center bg-gray-900 text-white">
                <p>No images available in the gallery.</p>
            </div>
        @endforelse
        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-next !text-white"></div> <div class="swiper-button-prev !text-white"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
    // INITIALIZE SWIPER
    var swiper = new Swiper(".mySwiper", {
        loop: true,        // Babalik sa simula kapag natapos ang slides
        speed: 1000,       // Duration ng transition (1 second)
        
        autoplay: { 
            delay: 2000,              // 2 seconds na display bawat slide
            disableOnInteraction: false, // Kahit pindutin ng user, itutuloy pa rin ang pag-ikot
            pauseOnMouseEnter: false,   // Hindi titigil kahit itapat ang mouse cursor
        },

        // Enable navigation dots
        pagination: { 
            el: ".swiper-pagination", 
            clickable: true 
        },

        // Enable next/prev arrows
        navigation: { 
            nextEl: ".swiper-button-next", 
            prevEl: ".swiper-button-prev" 
        },
    });
    </script>
</body>
</html>