@extends('layouts.app')

@section('content')

<style>
    /* Container setup */
    .container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Typography */
    h1, h2, h3 {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

   footer {
    

        padding: 1.5rem 0;

        text-align: center;

        font-family: 'Open Sans', sans-serif;

        font-size: 0.9rem;

        color: #666;

        border-top: 1px solid #ddd;

        margin-top: 4rem;

        background: none !important;

        box-shadow: none !important;

    }
    

    /* Hero section */
    .hero {
        text-align: center;
        padding: 4rem 1rem 3rem;
        background: linear-gradient(20deg, #f84242ff, #D3D3D3);
        color: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        margin-bottom: 3rem;
    }

    .hero h1 {
        font-size: 2.8rem;
        margin-bottom: 0.5rem;
    }

    .hero p {
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto 1.5rem;
    }

    .search-button {
        display: inline-block;
        background-color: #ff6b6b;
        color: #fff;
        font-weight: 600;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .search-button:hover {
        background-color: #ff4b4b;
    }

    /* Features */
    .feature-section {
        padding: 2rem 0 4rem;
        background: #f9f9fb;
        border-radius: 12px;
        box-shadow: inset 0 0 20px #eee;
    }

    .feature-section .feature {
        background: #fff;
        padding: 1.8rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .feature-section .feature:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .feature-section .feature h3 {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        color: #34495e;
    }

    .feature-section .feature p {
        font-size: 1rem;
        color: #6c7a89;
        line-height: 1.5;
    }

    .feature-section .container {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
        gap: 2rem;
    }

    /* Testimonial */
    .testimonial-section {
        padding: 4rem 1rem;
        text-align: center;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 30px rgba(0,0,0,0.07);
        margin-top: 3rem;
    }

    .testimonial-section .section-title {
        font-size: 2rem;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .testimonial-section .subtitle {
        font-size: 1.1rem;
        margin-bottom: 2.5rem;
        color: #7f8c8d;
    }

    .testimonial-card {
        max-width: 600px;
        margin: 0 auto;
        padding: 2rem 2.5rem;
        background: #f7f9fc;
        border-left: 6px solid #ff6b6b;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.15);
    }

    .testimonial-card blockquote {
        font-style: italic;
        font-size: 1.125rem;
        color: #34495e;
        margin-bottom: 1rem;
    }

    .testimonial-card footer {
        font-weight: 600;
        color: #ff6b6b;
        font-size: 0.9rem;
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .hero h1 {
            font-size: 2rem;
        }

        .search-button {
            padding: 0.6rem 1.5rem;
            font-size: 0.95rem;
        }
    }
</style>

<div class="hero">
    <h1>Welcome to DormHub</h1>
    <p>Find and manage student dorms easily. Start your search for the perfect living space now.</p>
    <a href="register" class="search-button" role="button" aria-label="Search for Dorms">Register</a>
</div>

<div class="feature-section">
    <div class="container">
        <div class="feature">
            <h3>Find Your Perfect Dorm</h3>
            <p>Search and compare various dorms based on location, price, and amenities.</p>
        </div>
        <div class="feature">
            <h3>Easy Booking</h3>
            <p>Book your dorm online with a few clicks and secure your spot with just one click.</p>
        </div>
        <div class="feature">
            <h3>Manage Your Stay</h3>
            <p>Keep track of your booking, payment, and rental duration with our easy-to-use dashboard.</p>
        </div>
    </div>
</div>


<footer>

    &copy; {{ date('Y') }} DormHub. All rights reserved.

</footer>
</div>

@endsection