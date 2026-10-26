@extends('layouts.app')

@section('content')
<style>
    body {
        /* Night Sky Gradient */
        background: linear-gradient(to bottom, #020105 0%, #090a2f 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    /* Stars Container */
    .stars-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        pointer-events: none;
    }

    /* Twinkling Star */
    .star {
        position: absolute;
        background: white;
        border-radius: 50%;
        opacity: 0;
        animation: twinkle var(--duration) ease-in-out infinite;
    }

    @keyframes twinkle {
        0% { opacity: 0; transform: scale(0.5); }
        50% { opacity: var(--opacity); transform: scale(1); }
        100% { opacity: 0; transform: scale(0.5); }
    }

    /* Shooting Star */
    .shooting-star {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 4px;
        height: 4px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1), 
                    0 0 0 8px rgba(255, 255, 255, 0.1), 
                    0 0 20px rgba(255, 255, 255, 1);
        animation: animate-shooting 3s linear infinite;
        opacity: 0;
    }

    .shooting-star::before {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 300px;
        height: 1px;
        background: linear-gradient(90deg, #fff, transparent);
    }

    @keyframes animate-shooting {
        0% {
            transform: rotate(315deg) translateX(0);
            opacity: 1;
        }
        70% {
            opacity: 1;
        }
        100% {
            transform: rotate(315deg) translateX(-1000px);
            opacity: 0;
        }
    }

    .custom-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        overflow: hidden;
        width: 100%;
        max-width: 450px;
        z-index: 10;
        position: relative;
    }
    .custom-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px 20px;
        text-align: center;
        position: relative;
    }
    .custom-card-header::before, .custom-card-header::after {
        content: '';
        position: absolute;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .custom-card-header::before {
        top: -30px; left: -30px; width: 100px; height: 100px;
    }
    .custom-card-header::after {
        bottom: -30px; right: -30px; width: 80px; height: 80px;
    }
    .form-control {
        border-radius: 50px;
        padding: 12px 20px;
        background-color: #f7f9fc;
        border: none;
    }
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.2);
        background-color: #fff;
    }
    .btn-custom {
        background: linear-gradient(to right, #667eea, #764ba2);
        border: none;
        border-radius: 50px;
        padding: 12px;
        font-weight: bold;
        letter-spacing: 1px;
        width: 100%;
        color: white;
        transition: transform 0.2s;
    }
    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
        color: white;
    }
</style>

<div class="stars-container" id="starsContainer"></div>

<div class="container d-flex justify-content-center">
    <div class="custom-card">
         <div class="custom-card-header">
            <h4 class="fw-bold mb-0">Forgot Password?</h4>
            <p class="mb-0 small text-white-50">Enter your mobile number to receive a verification code.</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label text-muted ms-3 small fw-bold">Mobile Number</label>
                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus placeholder="07xxxxxxxx">
                    @error('phone')
                        <small class="text-danger ms-3">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-custom">
                        SEND CODE
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('student.login') }}" class="text-decoration-none small" style="color: #667eea;"><i class="bi bi-arrow-left me-1"></i> Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="text-center mt-3 text-white-50 small">
    Developed By Dinuja D @Emedia solution (pvt) Ltd
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starsContainer = document.getElementById('starsContainer');
        const starCount = 100; 

        for (let i = 0; i < starCount; i++) {
            const star = document.createElement('div');
            star.classList.add('star');
            
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const size = Math.random() * 2 + 1;
            const duration = Math.random() * 3 + 2; 
            const delay = Math.random() * 5; 
            const opacity = Math.random() * 0.7 + 0.3;

            star.style.left = `${x}%`;
            star.style.top = `${y}%`;
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.setProperty('--duration', `${duration}s`);
            star.style.setProperty('--opacity', `${opacity}`);
            star.style.animationDelay = `${delay}s`;

            starsContainer.appendChild(star);
        }

        setInterval(createShootingStar, 4000);

        function createShootingStar() {
            const shootingStar = document.createElement('div');
            shootingStar.classList.add('shooting-star');
            
            const startY = Math.random() * 30; 
            const startX = Math.random() * 50 + 50; 
            
            shootingStar.style.top = `${startY}%`;
            shootingStar.style.left = `${startX}%`;
            
            starsContainer.appendChild(shootingStar);

            setTimeout(() => {
                shootingStar.remove();
            }, 3500);
        }
    });
</script>
@endsection
