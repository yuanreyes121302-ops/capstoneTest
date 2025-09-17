@extends('layouts.app')

@push('styles')
<style>
    /* Tenant-specific modern property cards */
    .tenant-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .tenant-header h2 {
        font-weight: 300;
        margin-bottom: 0;
        font-size: 2.5rem;
    }

    .tenant-container {
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
        height: 100%;
        display: flex;
        flex-direction: column;
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

    .property-card:hover .property-images img {
        transform: scale(1.05);
    }

    .property-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
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
        flex: 1;
    }

    .property-landlord {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .btn-tenant {
        border-radius: 25px;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        width: 100%;
    }

    .btn-tenant-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-tenant-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
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

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .tenant-header {
            padding: 1.5rem 0;
        }

        .tenant-header h2 {
            font-size: 2rem;
        }

        .property-content {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="tenant-header">
    <div class="tenant-container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('tenant.profile') }}" class="btn btn-light me-3" style="border-radius: 8px; color: #2c3e50; background: white; border: 1px solid #e1e8ed; transition: all 0.3s ease; font-weight: 500;">
                    ← Back to Profile
                </a>
                <h2>Available Properties</h2>
            </div>
        </div>
    </div>
</div>

<div class="tenant-container">
    <div class="row">
        @forelse ($properties as $property)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="property-card">
                    @if($property->images->first())
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
                        <p class="property-description">{{ Str::limit($property->description, 100) }}</p>
                        <p class="property-landlord">By {{ $property->user->first_name }} {{ $property->user->last_name }}</p>

                        <a href="{{ route('tenant.properties.show', $property->id) }}" class="btn btn-tenant btn-tenant-primary">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h4>No Properties Available</h4>
                    <p>There are currently no properties listed. Please check back later.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
