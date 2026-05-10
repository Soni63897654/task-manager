@extends('layouts.app')

@section('content')
<div class="row align-items-center">
    <div class="col-lg-6 mb-5 mb-lg-0">
        <h1 class="display-5 fw-bold text-dark">Task  Manager</h1>
        <p class="lead text-secondary mt-3">
            A professional way to organize, track, and manage your daily tasks with ease.
        </p>
        <ul class="list-unstyled mt-4">
            <li class="mb-3">
                <span class="text-success me-2"></span><strong>Secure:</strong> Robust authentication system.
            </li>
            <li class="mb-3">
                <span class="text-success me-2"></span><strong>Organized:</strong> Easy CRUD operations for tasks.
            </li>
            <li class="mb-3">
                <span class="text-success me-2"></span><strong>Flexible:</strong> Full file upload support included.
            </li>
        </ul>
    </div>

    <div class="col-lg-5 offset-lg-1">
        @auth
            <div class="card auth-card p-5 text-center border-0 shadow">
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 p-4 rounded-circle d-inline-block">
                        <img src="https://cdn-icons-png.flaticon.com/512/4697/4697260.png" style="width: 80px;">
                    </div>
                </div>
                <h2 class="fw-bold">Welcome, {{ Auth::user()->name }}!</h2>
                <p class="text-muted">You are currently logged in. Ready to check your schedule?</p>
                
                <div class="d-grid gap-3 mt-4">
                    <a href="/dashboard" class="btn btn-success py-2 rounded-pill">Go to Dashboard</a>
                    
                    <form action="/logout" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger py-2 w-100 rounded-pill">Logout Session</button>
                    </form>
                </div>
            </div>
        @else
            <div class="card auth-card p-5 text-center border-0 shadow">
                <img src="https://cdn-icons-png.flaticon.com/512/4697/4697260.png" class="mx-auto mb-4" style="width: 100px;">
                <h2 class="fw-bold">Get Started</h2>
                <p class="text-muted">Boost your productivity today.</p>
                <div class="d-grid gap-3 mt-4">
                    <a href="/login" class="btn btn-success py-2 rounded-pill">Login Now</a>
                    <a href="/register" class="btn btn-outline-dark py-2 rounded-pill">Create Account</a>
                </div>
            </div>
        @endauth
    </div>
</div>
@endsection