<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name') }} - Authentication</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CDN Dependencies -->
    <link rel="icon" type="image/png" href="https://via.placeholder.com/32x32/667eea/ffffff?text=A"/>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    @livewireStyles
    
    <!-- Custom styles matching the template -->
    <style>
        * {
            margin: 0px; 
            padding: 0px; 
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .wrap-login100 {
            width: 960px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 77px 130px 33px 95px;
            box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
        }

        .login100-pic {
            width: 316px;
        }

        .login100-pic img {
            max-width: 100%;
        }

        .login100-form {
            width: 290px;
        }

        .login100-form-title {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            color: #333333;
            line-height: 1.2;
            text-align: center;
            width: 100%;
            display: block;
            padding-bottom: 54px;
        }

        .wrap-input100 {
            position: relative;
            width: 100%;
            z-index: 1;
            margin-bottom: 10px;
        }

        .input100 {
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            line-height: 1.5;
            color: #666666;
            display: block;
            width: 100%;
            background: transparent;
            height: 50px;
            border-radius: 25px;
            padding: 0 30px 0 68px;
            border: 2px solid #e6e6e6;
            outline: none;
        }

        .focus-input100 {
            display: block;
            position: absolute;
            border-radius: 25px;
            bottom: 0;
            left: 0;
            z-index: -1;
            width: 100%;
            height: 100%;
            box-shadow: 0px 0px 0px 0px;
            color: rgba(87,184,70, 0.8);
        }

        .input100:focus + .focus-input100 {
            animation: anim-shadow 0.5s ease-in-out forwards;
        }

        @keyframes anim-shadow {
            to {
                box-shadow: 0px 0px 70px 25px;
                opacity: 0;
            }
        }

        .symbol-input100 {
            font-size: 15px;
            display: flex;
            align-items: center;
            position: absolute;
            border-radius: 25px;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding-left: 35px;
            pointer-events: none;
            color: #666666;
            transition: all 0.4s;
        }

        .input100:focus + .focus-input100 + .symbol-input100 {
            color: #57b846;
            padding-left: 28px;
        }

        .container-login100-form-btn {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 20px;
        }

        .login100-form-btn {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            line-height: 1.5;
            color: #fff;
            text-transform: uppercase;
            width: 100%;
            height: 50px;
            border-radius: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 25px;
            transition: all 0.4s;
            border: none;
            outline: none;
        }

        .login100-form-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
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
            color: #57b846;
            text-decoration: none;
        }

        .txt2:hover {
            color: #57b846;
            text-decoration: underline;
        }

        .p-t-12 {
            padding-top: 12px;
        }

        .p-t-136 {
            padding-top: 136px;
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

        /* Alert styles */
        .alert {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 25px;
            font-size: 14px;
            border: none;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
        }
        .alert-success {
            background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
            color: white;
        }
        .alert-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }

        /* Step indicator */
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }
        .step {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.1);
        }
        .step.completed {
            background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
            color: white;
        }
        .step.inactive {
            background-color: #e2e8f0;
            color: #718096;
        }
        .step-connector {
            width: 50px;
            height: 3px;
            background-color: #e2e8f0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .step-connector.completed {
            background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
        }

        /* Button styles */
        .btn-link {
            background: none;
            border: none;
            color: #667eea;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
            padding: 5px 0;
            transition: all 0.3s ease;
        }
        .btn-link:hover {
            color: #5a67d8;
            transform: translateY(-1px);
        }
        .btn-link:disabled {
            color: #999;
            cursor: not-allowed;
            transform: none;
        }

        /* OTP input */
        .otp-input {
            text-align: center;
            letter-spacing: 0.3em;
            font-size: 18px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        /* Loading state */
        .loading-btn {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .wrap-login100 {
                padding: 50px 50px 33px 50px;
            }
            
            .login100-pic {
                width: 100%;
                text-align: center;
                margin-bottom: 30px;
            }
            
            .login100-form {
                width: 100%;
            }
            
            .p-t-136 {
                padding-top: 50px;
            }
        }

        @media (max-width: 576px) {
            .wrap-login100 {
                padding: 30px 20px 33px 20px;
                flex-direction: column;
            }
            
            .step-indicator {
                margin-bottom: 20px;
            }
            
            .step {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }
            
            .step-connector {
                width: 30px;
            }
        }

        /* Animation for progress bar */
        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }

        /* Input validation states */
        .has-error .input100 {
            border-color: #e74c3c;
        }

        .has-error .symbol-input100 {
            color: #e74c3c;
        }

        /* Success icons and animations */
        .animate__animated {
            animation-duration: 0.8s;
        }

        /* Custom tilt effect */
        .js-tilt {
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }

        .js-tilt:hover {
            transform: scale(1.05) rotateY(5deg) rotateX(5deg);
        }
    </style>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt animate__animated animate__fadeInLeft">
                    <!-- Using a beautiful SVG illustration instead of local image -->
                    <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#00d2d3;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#54a0ff;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        
                        <!-- Background circles -->
                        <circle cx="320" cy="80" r="60" fill="url(#gradient1)" opacity="0.1"/>
                        <circle cx="80" cy="220" r="40" fill="url(#gradient2)" opacity="0.1"/>
                        
                        <!-- Main illustration -->
                        <rect x="120" y="100" width="160" height="120" rx="20" fill="url(#gradient1)" opacity="0.8"/>
                        
                        <!-- Screen -->
                        <rect x="130" y="110" width="140" height="80" rx="10" fill="white"/>
                        
                        <!-- Screen content -->
                        <rect x="140" y="120" width="80" height="8" rx="4" fill="#e0e0e0"/>
                        <rect x="140" y="135" width="120" height="8" rx="4" fill="#e0e0e0"/>
                        <rect x="140" y="150" width="60" height="8" rx="4" fill="#e0e0e0"/>
                        
                        <!-- Button -->
                        <rect x="140" y="165" width="60" height="20" rx="10" fill="url(#gradient2)"/>
                        
                        <!-- Keyboard -->
                        <rect x="125" y="235" width="150" height="40" rx="8" fill="#f8f9fa"/>
                        <rect x="135" y="245" width="20" height="8" rx="2" fill="#e0e0e0"/>
                        <rect x="160" y="245" width="20" height="8" rx="2" fill="#e0e0e0"/>
                        <rect x="185" y="245" width="20" height="8" rx="2" fill="#e0e0e0"/>
                        <rect x="210" y="245" width="20" height="8" rx="2" fill="#e0e0e0"/>
                        <rect x="235" y="245" width="20" height="8" rx="2" fill="#e0e0e0"/>
                        
                        <rect x="135" y="258" width="30" height="8" rx="2" fill="#e0e0e0"/>
                        <rect x="170" y="258" width="80" height="8" rx="2" fill="#e0e0e0"/>
                        <rect x="255" y="258" width="20" height="8" rx="2" fill="#e0e0e0"/>
                        
                        <!-- Security icons -->
                        <circle cx="60" cy="120" r="25" fill="url(#gradient2)" opacity="0.9"/>
                        <path d="M50 120 L55 125 L70 110" stroke="white" stroke-width="3" fill="none" stroke-linecap="round"/>
                        
                        <circle cx="340" cy="200" r="20" fill="url(#gradient1)" opacity="0.9"/>
                        <rect x="335" y="195" width="10" height="12" fill="white" rx="2"/>
                        <circle cx="340" cy="190" r="3" stroke="white" stroke-width="2" fill="none"/>
                    </svg>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- CDN Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Simple tilt effect script -->
    <script>
        // Simple tilt effect
        document.addEventListener('DOMContentLoaded', function() {
            const tiltElements = document.querySelectorAll('.js-tilt');
            
            tiltElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05) rotateY(5deg) rotateX(5deg)';
                });
                
                element.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) rotateY(0deg) rotateX(0deg)';
                });
            });
        });
    </script>

    @livewireScripts
    
    <!-- Livewire specific scripts -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('redirect-after-delay', (event) => {
                setTimeout(() => {
                    window.location.href = event.url;
                }, event.delay);
            });
        });
    </script>
</body>
</html>