@extends('layouts.quotation')

@section('quotation_content')
    <div class="page-head mb-4">
        <h2 class="mb-1">Edit Quotation</h2>
        <div class="sub">{{ $quotation->quotation_number }} &middot; Update the details below and save.</div>
    </div>

    @include('quotations._form', ['action' => route('quotations.update', $quotation)])
@endsection
