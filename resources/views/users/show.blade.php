@extends('layouts.user')

@section('user_content')
    {{-- When printing: hide menus, always use light colors --}}
    <style>
        @media print {
            html[data-bs-theme] {
                --card: #ffffff;
                --surface: #ffffff;
                --ink: #111111;
                --muted: #555555;
                --line: #dddddd;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .topbar,
            .d-print-none {
                display: none !important;
            }

            .col-md-3 {
                display: none !important;
            }

            .col-md-9 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .panel {
                box-shadow: none !important;
                border: 0 !important;
            }
        }
    </style>

    {{-- Top Action Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to users
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
                data-action="{{ route('users.destroy', $user) }}"
                data-name="{{ $user->userInfo?->full_name ?? $user->email }}">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    {{-- Main Profile Panel (Theme-Aware) --}}
    <div class="panel border-0 shadow-sm"
        style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 0.75rem;">
        <div class="p-4 p-md-5">

            {{-- Profile Header / Cover Card --}}
            <div class="d-flex align-items-center flex-wrap gap-4 pb-4 mb-4 border-bottom"
                style="border-color: var(--line) !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white bg-primary shadow-sm"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    {{-- Call Initial Function in the Model --}}
                    {{ strtoupper($user->userInfo?->initials ?? substr($user->email, 0, 1)) }}
                </div>
                <div>
                    <h3 class="mb-1 fw-bold">{{ $user->userInfo?->full_name ?? 'Unnamed User' }}</h3>
                    <div class="text-muted mb-2"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="badge bg-{{ $user->access_level === 'admin' ? 'danger' : ($user->access_level === 'manager' ? 'warning text-dark' : 'secondary') }}">
                            Role: {{ ucfirst($user->access_level) }}
                        </span>
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                            Status: {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Profile Details Grid --}}
            <div class="row g-4">
                {{-- Personal Information Section --}}
                <div class="col-md-6">
                    <div class="card h-100 border shadow-none"
                        style="background: var(--surface, var(--card)); border-color: var(--line) !important; border-radius: 0.5rem;">
                        <div class="card-body">
                            <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                                <i class="bi bi-person-badge me-1"></i> Personal Information
                            </h6>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><strong>First Name:</strong> <span
                                        class="text-muted">{{ $user->userInfo?->first_name ?? '—' }}</span></li>
                                <li><strong>Middle Name:</strong> <span
                                        class="text-muted">{{ $user->userInfo?->middle_name ?? '—' }}</span></li>
                                <li><strong>Last Name:</strong> <span
                                        class="text-muted">{{ $user->userInfo?->last_name ?? '—' }}</span></li>
                                <li><strong>Suffix:</strong> <span
                                        class="text-muted">{{ $user->userInfo?->suffix ?? '—' }}</span></li>
                                <li><strong>Gender:</strong> <span
                                        class="text-muted">{{ $user->userInfo?->gender ?? '—' }}</span></li>
                                <li><strong>Birthday:</strong> <span
                                        class="text-muted">{{ $user->userInfo?->birthday ? \Carbon\Carbon::parse($user->userInfo->birthday)->format('F d, Y') : '—' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Account & System Metadata Section --}}
                <div class="col-md-6">
                    <div class="card h-100 border shadow-none"
                        style="background: var(--surface, var(--card)); border-color: var(--line) !important; border-radius: 0.5rem;">
                        <div class="card-body">
                            <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                                <i class="bi bi-shield-lock me-1"></i> Account & System Settings
                            </h6>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><strong>Email Address:</strong> <span class="text-muted">{{ $user->email }}</span>
                                </li>
                                <li><strong>Access Control Level:</strong> <span
                                        class="text-muted">{{ ucfirst($user->access_level) }}</span></li>
                                <li><strong>Account Status:</strong> <span
                                        class="text-muted">{{ ucfirst($user->status) }}</span></li>
                                <li><strong>Linked Profile ID:</strong> <span
                                        class="text-muted">#{{ $user->info_id ?? 'None' }}</span></li>
                                <li><strong>Account Created:</strong> <span
                                        class="text-muted">{{ $user->created_at->format('M d, Y h:i A') }}</span></li>
                                <li><strong>Last Modified:</strong> <span
                                        class="text-muted">{{ $user->updated_at->format('M d, Y h:i A') }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="deleteForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center;
                                font-size: 1.6rem; background: rgba(220, 53, 69, .12); color: #dc3545;">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" id="deleteModalLabel">Delete this user account?</h5>
                    <div class="fw-medium mb-2" id="deleteName"></div>
                    <p class="text-muted small mb-4">
                        This user will be permanently removed from the system along with their associated profile record.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('deleteModal').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('deleteForm').action = button.dataset.action;
                document.getElementById('deleteName').textContent = button.dataset.name;
            });
        </script>
    @endpush
@endsection
