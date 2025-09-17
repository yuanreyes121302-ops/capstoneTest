<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet Routing Machine CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>


<style>

     body {

        margin: 0;
    }

   /*
    .cover-background {
        background-image: url('{{ asset('https://static.where-e.com/Philippines/Central_Luzon_Region/Pampanga/Don-Honorio-Ventura-State-University_d4f0672b8be875221f3eb060d5a99fe4.jpg') }}'); /* Update the path to your image */
      /*  background-size: cover;
        background-position: center;
        height: 100vh; /* Full screen height */
     /*   display: flex;
        justify-content: center;
        align-items: center;
        color: black;
        text-align: center;
        opacity: 60%;
    }

*/

    .container {
        z-index: 10; /* Ensure content appears above the background */
    }
    .navbar {
        background-color: rgba(200, 50, 50, 1) !important;

    }
    /* Custom background for the entire page */
.custom-bg {
    background-color: rgba(200, 50, 50, 1); /* Custom background color */
    color: black; /* Optional: Change text color to white for better contrast */
}



</style>

</head>

<body class="{{ Auth::check() && Auth::user()->role == 'tenant' ? 'custom-bg' : '' }}">
    <div id="app">
         <div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
        <div class="container">

            <a class="navbar-brand fw-bold text-white" href="{{ url('/') }}">
                <i class="fa-solid fa-house"></i> {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>



           <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side -->
                <ul class="navbar-nav me-auto"></ul>

                <!-- Right Side -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        @if(Auth::user()->role === 'tenant')
                            <li class="nav-item">
                                <a class="nav-link text-white fw-semibold" href="{{ route('tenant.profile') }}">
                                    <i class="fa-regular fa-user"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white fw-semibold" href="{{ route('tenant.properties.index') }}">
                                    <i class="fa-solid fa-magnifying-glass-location"></i> Browse
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white fw-semibold" href="{{ route('messages.index') }}">
                                    <i class="fa-solid fa-inbox"></i> Messages
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white fw-semibold" href="{{ route('bookings.tenant.index') }}">
                                    <i class="fa-solid fa-file"></i> Requests
                                </a>
                            </li>
                        @elseif(Auth::user()->role === 'landlord')
                            @php
                                $unreadNotificationsCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                            @endphp
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('landlord.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('landlord.profile') }}"><i class="fa-regular fa-user"></i> Profile</a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('landlord.properties.index') }}"><i class="fa-solid fa-house"></i> Properties</a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('messages.index') }}"><i class="fa-solid fa-inbox"></i> Messages</a></li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="{{ route('bookings.landlord.index') }}">
                                    <i class="fa-solid fa-file"></i> Requests
                                    @if($unreadNotificationsCount > 0)
                                        <span class="badge bg-danger ms-1">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('landlord.contracts') }}"><i class="fa-solid fa-file-contract"></i> Contracts</a></li>
                        @elseif(Auth::user()->role === 'admin')
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.users.all') }}">👥 Users</a></li>
                        @endif
                    @endauth

                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item"><a class="btn btn-outline-light me-2" href="{{ route('login') }}">Login</a></li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item"><a class="btn btn-warning" href="{{ route('register') }}">Register</a></li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white fw-semibold" href="#"
                               role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

   <main class="py-4 bg-light min-vh-100">
    <div class="container">
        @yield('content')
    </div>
</main>

</div>
@stack('scripts')
    </div>
</body>

</html>
