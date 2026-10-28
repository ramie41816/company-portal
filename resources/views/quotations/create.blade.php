@extends('layouts.quotation')

@section('quotation_content')
    <div class="page-head mb-4">
        <h2 class="mb-1"><i class="bi bi-plus-square"></i> Create Quotation</h2>
        <div class="sub">Fill in the customer details and the scope of work.</div>
    </div>

    @include('quotations._form', ['action' => route('quotations.store')])
@endsection
