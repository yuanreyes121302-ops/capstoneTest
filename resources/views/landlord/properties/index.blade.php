@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Landlord-specific modern design */
    .landlord-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .landlord-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .landlord-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .property-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 2rem;
        border: none;
    }

    .property-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .property-images {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .property-images img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .property-images:hover img {
        transform: scale(1.05);
    }

    .property-content {
        padding: 1.5rem;
    }

    .property-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .property-location {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .property-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: #27ae60;
        margin-bottom: 1rem;
    }

    .property-description {
        color: #34495e;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .property-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-landlord {
        border-radius: 25px;
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-landlord-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-landlord-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
    }

    .btn-landlord-secondary {
        background: #ecf0f1;
        color: #2c3e50;
    }

    .btn-landlord-secondary:hover {
        background: #d5dbdb;
    }

    .btn-landlord-info {
        background: #3498db;
        color: white;
    }

    .btn-landlord-info:hover {
        background: #2980b9;
    }

    .btn-landlord-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-landlord-danger:hover {
        background: #c0392b;
    }

    .add-property-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 30px;
        padding: 0.8rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }

    .add-property-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .alert-landlord {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .landlord-header {
            padding: 1.5rem 0;
        }

        .landlord-header h2 {
            font-size: 2rem;
        }

        .property-actions {
            flex-direction: column;
        }

        .btn-landlord {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

<div class="landlord-header">
    <div class="landlord-container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('landlord.dashboard') }}" class="btn btn-light me-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                    ← Back
                </a>
                <h2>My Properties</h2>
            </div>
            <a href="{{ route('landlord.profile') }}" class="btn btn-light">
                <i class="fas fa-user-circle"></i> View Profile
            </a>
        </div>
    </div>
</div>

<div class="landlord-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Property Management</h3>
        <a href="{{ route('landlord.properties.create') }}" class="add-property-btn">
            <i class="fas fa-plus"></i> Add New Property
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error!</strong>
            @if(session('error'))
                {{ session('error') }}
            @else
                Please check the following errors:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($properties as $property)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="property-card">
                    @if($property->images->count())
                        <div class="property-images">
                            <img src="{{ asset('storage/property_images/' . $property->images->first()->image_path) }}"
                                 alt="{{ $property->title }}">
                        </div>
                    @else
                        <div class="property-images" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-home fa-3x text-white"></i>
                        </div>
                    @endif

                    <div class="property-content">
                        <h5 class="property-title">{{ $property->title }}</h5>
                        <p class="property-location">
                            <i class="fas fa-map-marker-alt"></i> {{ $property->location }}
                        </p>
                        <p class="property-price">₱{{ number_format($property->price, 0) }}</p>
                        <p class="property-description">{{ Str::limit($property->description, 80) }}</p>

                        <div class="property-actions">
                            <a href="{{ route('landlord.properties.edit', $property->id) }}" class="btn btn-landlord btn-landlord-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('landlord.rooms.index', $property->id) }}" class="btn btn-landlord btn-landlord-info btn-sm">
                                <i class="fas fa-bed"></i> Manage Rooms
                            </a>
                            <form action="{{ route('landlord.properties.destroy', $property->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this property?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-landlord btn-landlord-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h4>No Properties Yet</h4>
                    <p>Start building your portfolio by adding your first property.</p>
                    <a href="{{ route('landlord.properties.create') }}" class="add-property-btn">
                        <i class="fas fa-plus"></i> Add Your First Property
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
