@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="hero mb-5">
            <p class="text-uppercase small opacity-75 mb-2" style="letter-spacing: .12em;">{{ config('company.tagline') }}</p>
            <h1 class="display-6 mb-2">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="opacity-75 mb-0">What would you like to work on today?</p>
        </div>

        @php
            $services = [
                [
                    'title' => 'Quotation',
                    'desc' => 'Create, manage and send customer quotations.',
                    'icon' => 'bi-file-earmark-text',
                    'url' => route('quotations.index'),
                    'available' => true,
                ],
                [
                    'title' => 'User Management',
                    'desc' => 'Manage staff accounts and their access.',
                    'icon' => 'bi-people',
                    'url' => route('users.index'),
                    'available' => true,
                ],
            ];
        @endphp

        <h5 class="fw-semibold mb-3">Our Services</h5>

        <div class="row g-4">
            @foreach ($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card h-100 p-4 d-flex flex-column">
                        <div class="service-icon mb-3"><i class="bi {{ $service['icon'] }}"></i></div>
                        <h5 class="fw-semibold mb-1">{{ $service['title'] }}</h5>
                        <p class="text-muted flex-grow-1">{{ $service['desc'] }}</p>

                        @if ($service['available'])
                            <a href="{{ $service['url'] }}" class="btn btn-brand align-self-start">
                                Open <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @else
                            <span class="badge rounded-pill badge-soon align-self-start px-3 py-2">Coming soon</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
