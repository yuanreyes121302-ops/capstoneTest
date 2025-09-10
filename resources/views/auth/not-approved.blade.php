@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Account Pending Approval') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p>Your account is currently pending approval by an administrator. You will be able to access the system once your account is approved.</p>

                    <p>Please check back later or contact support if you have any questions.</p>

                    <a href="{{ route('logout') }}" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
