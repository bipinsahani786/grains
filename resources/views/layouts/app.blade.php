<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    @include('partials.head')
</head>
<body>
    @include('partials.sidebar')
    @include('partials.header')

    @if(session()->has('impersonated_by'))
        <div class="position-fixed bottom-0 end-0 m-4" style="z-index: 1050;">
            <div class="d-flex align-items-center gap-3 bg-dark text-white rounded-pill shadow-lg px-4 py-2 border border-secondary" style="backdrop-filter: blur(10px); --bs-bg-opacity: .85;">
                <div class="d-flex align-items-center gap-2">
                    <div class="spinner-grow spinner-grow-sm text-danger" role="status">
                        <span class="visually-hidden">Impersonating...</span>
                    </div>
                    <span class="fs-13">Viewing as <strong class="text-warning">{{ auth()->user()->name }}</strong></span>
                </div>
                <div class="vr bg-secondary opacity-50 my-1"></div>
                <a href="{{ route('impersonate.stop') }}" class="btn btn-sm btn-danger rounded-pill fw-bold px-3 py-1 fs-12 d-flex align-items-center gap-1">
                    <i class="feather-log-out"></i> Leave
                </a>
            </div>
        </div>
    @endif

    <main class="nxl-container">
        @yield('content')
    </main>

    @stack('modals')

    @include('partials.scripts')
    @stack('scripts')
</body>
</html>