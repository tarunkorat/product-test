@extends('layouts.app')

@section('content')
    <h3>Product Form</h3>

    <form id="productForm">
        <div class="mb-2">
            <input class="form-control" name="name" placeholder="Product name">
        </div>
        <div class="mb-2">
            <input class="form-control" name="quantity" type="number" placeholder="Quantity in stock">
        </div>
        <div class="mb-2">
            <input class="form-control" name="price" type="number" step="0.01" placeholder="Price per item">
        </div>
        <button class="btn btn-primary">Submit</button>
    </form>

    <hr>

    <div id="table-container"></div>
@endsection
