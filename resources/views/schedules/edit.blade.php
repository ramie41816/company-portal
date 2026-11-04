@extends('layouts.schedule')
@section('schedule_content')
    <div class="page-head mb-4">
        <h2 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Schedule</h2>
        <div class="sub">{{ $schedule->schedule_number }} &middot; Update information below.</div>
    </div>
    @include('schedules._form', ['action' => route('schedules.update', $schedule)])
@endsection
