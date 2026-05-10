@extends('layouts.app')

@section('content')

<div class="container py-5">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            Welcome back, <span class="text-success">{{ Auth::user()->name }}</span>
        </h2>
        <p class="text-muted">
            Manage your tasks easily and stay organized.
        </p>
    </div>

    <div class="row g-4">

        {{-- LEFT BOX --}}
        <div class="col-lg-7">
            <div class="p-5 border rounded bg-white h-100">

                <h5 class="fw-semibold mb-4">Quick Actions</h5>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="/tasks" class="btn btn-dark px-4 py-2">
                        Manage Tasks
                    </a>

                    <a href="/" class="btn btn-outline-secondary px-4 py-2">
                        Home
                    </a>
                </div>

                <hr class="my-4">

                <p class="text-muted mb-0">
                    You can create, update and track all your tasks from here.
                </p>

            </div>
        </div>

        {{-- RIGHT BOX --}}
        <div class="col-lg-5">
            <div class="p-5 border rounded bg-light h-100">

                <h6 class="text-muted mb-4">Task Overview</h6>

                <div class="mb-4 p-3 bg-white rounded border">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total Tasks</span>
                        <h4 class="mb-0">{{ $totalTasks }}</h4>
                    </div>
                </div>

                <div class="p-3 bg-white rounded border">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Pending Tasks</span>
                        <h4 class="mb-0 text-warning">{{ $pendingTasks }}</h4>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection