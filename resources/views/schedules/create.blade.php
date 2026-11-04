@extends('layouts.schedule')
@section('schedule_content')
    <div class="page-head mb-4">
        <h2 class="mb-1"><i class="bi bi-plus-square"></i> Create Schedule</h2>
        <div class="sub">Fill out the schedule and item details.</div>
    </div>
    @include('schedules._form', ['action' => route('schedules.store')])
@endsection
