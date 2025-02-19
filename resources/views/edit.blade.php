@extends('index')

@section('content')
    <div class="container">
        <h2>Edit Product: {{ $product->name }}</h2>
        <form method="POST" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- This makes the form method PUT to update -->
            
            <div class="mb-3">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="mb-3">
                <label>Image:</label>
                <input type="file" name="image" class="form-control">
                <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" width="100">
            </div>

            <div class="mb-3">
                <label>Type:</label>
                <input type="text" name="type" class="form-control" value="{{ old('type', $product->type) }}" required>
            </div>

            <div class="mb-3">
                <label>Purchase Price:</label>
                <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" required>
            </div>

            <div class="mb-3">
                <label>Selling Price:</label>
                <input type="number" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required>
            </div>

            <div class="mb-3">
                <label>Total Sold Quantity:</label>
                <input type="number" name="total_sold_quantity" class="form-control" value="{{ old('total_sold_quantity', $product->total_sold_quantity) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
@endsection
