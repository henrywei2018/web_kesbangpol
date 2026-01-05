<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name') }} - Authentication</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        use App\Settings\GeneralSettings;
        $generalSettings = app(GeneralSettings::class);
        $faviconUrl = $generalSettings->site_favicon ? Storage::url($generalSettings->site_favicon) : null;
    @endphp
    
    <!-- Favicon -->
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/png" href="https://via.placeholder.com/32x32/667eea/ffffff?text=A"/>
    @endif
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @livewireStyles
    
    <!-- Custom styles with wave background -->
    <style>
        * {
            margin: 0px; 
            padding: 0px; 
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .limiter {
            width: 100%;
            margin: 0 auto;
        }

        .container-login100 {
            width: 100%;  
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            padding: 15px;
            position: relative;
            /* Main gradient background */
            background: linear-gradient(135deg, #ff1d1d 0%, #ff4757 25%, #ff3838 50%, #ff6b7a 75%, #ff5722 100%);
            overflow: hidden;
        }

        /* Animated Wave Background */
        .wave-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="%23ffffff"></path><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="%23ffffff"></path><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%23ffffff"></path></svg>') repeat-x;
            background-size: 1200px 120px;
            animation: wave-animation 15s ease-in-out infinite;
        }

        .wave:nth-child(1) {
            animation-delay: 0s;
            opacity: 0.3;
        }

        .wave:nth-child(2) {
            animation-delay: -2s;
            opacity: 0.2;
            animation-duration: 20s;
        }

        .wave:nth-child(3) {
            animation-delay: -4s;
            opacity: 0.1;
            animation-duration: 25s;
        }

        @keyframes wave-animation {
            0% {
                transform: translateX(0);
            }
            50% {
                transform: translateX(-25%);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        /* Floating particles for extra visual appeal */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 20px;
            height: 20px;
            top: 20%;
            left: 20%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 30px;
            height: 30px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            width: 15px;
            height: 15px;
            top: 40%;
            left: 70%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            width: 25px;
            height: 25px;
            top: 80%;
            left: 10%;
            animation-delay: 1s;
        }

        .particle:nth-child(5) {
            width: 18px;
            height: 18px;
            top: 10%;
            left: 60%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 1;
            }
        }

        .wrap-login100 {
            width: 720px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 40px 60px 25px 50px; /* Reduced from 77px 130px 33px 95px */
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 10px 20px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .wrap-login100::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 20px;
            z-index: -1;
        }

        .login100-pic {
            width: 316px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login100-pic img {
            max-width: 100%;
        }

        .login100-form {
            width: 290px;
        }

        .login100-form-title {
            font-family: 'Poppins', sans-serif;
            font-size: 28px; /* Reduced from 30px */
            color: #333333;
            line-height: 1.2;
            text-align: center;
            width: 100%;
            display: block;
            padding-bottom: 30px; /* Reduced from 54px */
            font-weight: 700;
            background: linear-gradient(135deg, #ff0000 0%, #ff1e1ed7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .wrap-input100 {
            width: 100%;
            position: relative;
            border-bottom: 2px solid #e6e6e6;
            margin-bottom: 20px; /* Reduced from 37px */
            background: rgba(255, 255, 255, 0.8);
            border-radius: 25px;
            padding: 0 30px;
            transition: all 0.3s;
        }

        .wrap-input100:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .input100 {
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            color: #555555;
            line-height: 1.2;
            border: none;
            display: block;
            width: 100%;
            height: 50px; /* Reduced from 55px */
            background: transparent;
            padding: 0 5px 0 38px;
            outline: none;
        }

        .focus-input100 {
            position: absolute;
            display: block;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            border-radius: 25px;
            border: 2px solid transparent;
            transition: all 0.4s;
        }

        .input100:focus + .focus-input100 {
            border-color: #ff4141;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .symbol-input100 {
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            height: 100%;
            width: 30px;
            left: 0;
            top: 0;
            padding-left: 15px;
            pointer-events: none;
            color: #666666;
            transition: all 0.4s;
        }

        .input100:focus + .focus-input100 + .symbol-input100 {
            color: #ff0404;
            transform: scale(1.1);
        }

        .container-login100-form-btn {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 15px; /* Reduced from 20px */
        }

        .login100-form-btn {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.5;
            color: #fff;
            text-transform: uppercase;
            width: 100%;
            height: 45px; /* Reduced from 50px */
            border-radius: 25px;
            background: linear-gradient(135deg, #ff00009d 0%, #ff0000 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 25px;
            transition: all 0.4s;
            border: none;
            outline: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .login100-form-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login100-form-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 74, 74, 0.4);
        }

        .login100-form-btn:hover::before {
            left: 100%;
        }

        .login100-form-btn:active {
            transform: translateY(-1px);
        }

        .txt1 {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #999999;
        }

        .txt2 {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #ff3131;
            text-decoration: none;
            transition: all 0.3s;
        }

        .txt2:hover {
            color: #ff0000;
            text-decoration: underline;
            transform: translateY(-1px);
        }

        .p-t-12 {
            padding-top: 8px; /* Reduced from 12px */
        }

        .p-t-136 {
            padding-top: 20px; /* Reduced from 136px */
        }

        .m-l-5 {
            margin-left: 5px;
        }

        .m-r-5 {
            margin-right: 5px;
        }

        .text-center {
            text-align: center;
        }

        /* Enhanced Alert styles */
        .alert {
            margin-bottom: 12px; /* Reduced from 15px */
            padding: 12px 18px; /* Reduced from 15px 20px */
            border-radius: 20px; /* Reduced from 25px */
            font-size: 14px;
            border: none;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(10px);
            z-index: -1;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.9) 0%, rgba(238, 90, 36, 0.9) 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(0, 210, 211, 0.9) 0%, rgba(84, 160, 255, 0.9) 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(116, 185, 255, 0.9) 0%, rgba(9, 132, 227, 0.9) 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Enhanced Step indicator */
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.4s ease;
            position: relative;
            z-index: 2;
        }

        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.2);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .step.completed {
            background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 210, 211, 0.3);
        }

        .step.inactive {
            background-color: rgba(226, 232, 240, 0.8);
            color: #718096;
        }

        .step-connector {
            width: 60px;
            height: 4px;
            background-color: rgba(226, 232, 240, 0.6);
            border-radius: 2px;
            transition: all 0.4s ease;
            position: relative;
        }

        .step-connector.completed {
            background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
            box-shadow: 0 2px 10px rgba(0, 210, 211, 0.3);
        }

        /* Enhanced Button styles */
        .btn-link {
            background: none;
            border: none;
            color: #667eea;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
            padding: 8px 0;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-link:hover {
            color: #5a67d8;
            transform: translateY(-1px);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-link:disabled {
            color: #a0aec0;
            cursor: not-allowed;
            transform: none;
            text-shadow: none;
        }

        /* Loading state */
        .loading-btn {
            opacity: 0.8;
            cursor: not-allowed;
            transform: none !important;
        }

        .loading-btn::before {
            display: none;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            font-size: 16px;
            z-index: 10;
            padding: 5px;
            transition: all 0.3s ease;
            border-radius: 50%;
        }

        .password-toggle:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .wrap-login100 {
                padding: 50px 50px 33px 50px;
                width: 90%;
                margin: 20px auto;
            }
            
            .login100-pic {
                width: 100%;
                text-align: center;
                margin-bottom: 30px;
            }
            
            .login100-form {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .wrap-login100 {
                padding: 30px 20px;
                border-radius: 15px;
            }
            
            .login100-form-title {
                font-size: 24px;
                padding-bottom: 30px;
            }
            
            .step {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }
            
            .step-connector {
                width: 40px;
            }
        }

        @media (max-width: 576px) {
            .container-login100 {
                padding: 10px;
            }
            
            .wrap-login100 {
                padding: 20px 15px;
                margin: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <!-- Animated Wave Background -->
            <div class="wave-container">
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>
            </div>
            
            <!-- Floating Particles -->
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            
            <div class="wrap-login100">
                <div class="login100-pic js-tilt animate__animated animate__fadeInLeft" 
                     style="display: flex; align-items: center; justify-content: center; height: 100%; min-height: 400px;">
                    @php
                        // Use the already imported GeneralSettings from the top
                        $logoUrl = $generalSettings->brand_logo ? Storage::url($generalSettings->brand_logo) : null;
                        $logoHeight = $generalSettings->brand_logoHeight ?? '300px';
                        $brandName = $generalSettings->brand_name ?? config('app.name');
                    @endphp

                    <div style="text-align: center; max-width: 100%; ">
                        @if($logoUrl)
                            <!-- Dynamic Logo from General Settings -->
                            <img src="{{ $logoUrl }}" 
                                 alt="{{ $brandName }} Logo" 
                                 style="max-height: 800px; max-width: 100%; object-fit: contain; display: block; margin: 0 auto;"
                                 class="animate__animated animate__pulse">
                        @else
                            <!-- Fallback: Default Settings Icon SVG when no logo is set -->
                            <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" 
                                 style="max-height: 800px; max-width: 100%; display: block; margin: 0 auto;">
                                <defs>
                                    <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                                    </linearGradient>
                                    <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#f093fb;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#f5576c;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Background decorative elements -->
                                <circle cx="80" cy="60" r="30" fill="url(#gradient2)" opacity="0.1"/>
                                <circle cx="320" cy="80" r="25" fill="url(#gradient1)" opacity="0.1"/>
                                
                                <!-- Main settings gear -->
                                <g transform="translate(200,150)">
                                    <path d="M-30,-50 L-15,-55 L-15,-45 L-30,-40 
                                             L-40,-30 L-45,-15 L-55,-15 L-50,-30
                                             L-40,30 L-45,15 L-55,15 L-50,30
                                             L-30,50 L-15,55 L-15,45 L-30,40
                                             L30,50 L15,55 L15,45 L30,40
                                             L40,30 L45,15 L55,15 L50,30
                                             L40,-30 L45,-15 L55,-15 L50,-30
                                             L30,-50 L15,-55 L15,-45 L30,-40 Z" 
                                          fill="url(#gradient1)"/>
                                    <circle cx="0" cy="0" r="35" fill="white"/>
                                    <circle cx="0" cy="0" r="12" fill="url(#gradient1)"/>
                                </g>
                                
                                <!-- Brand name below -->
                                <text x="200" y="250" text-anchor="middle" fill="url(#gradient1)" 
                                      font-family="Arial, sans-serif" font-size="18" font-weight="bold">
                                    {{ $brandName }}
                                </text>
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="login100-form validate-form animate__animated animate__fadeInRight">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    @livewireScripts
    @stack('scripts')
    
    <!-- Tilt.js for 3D effect -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-tilt').tilt({
                scale: 1.05,
                glare: true,
                maxGlare: 0.2
            });
        });
    </script>
</body>
</html>