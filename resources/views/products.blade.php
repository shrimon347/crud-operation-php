@extends('index')
@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <!-- Left Side - Menu Items (col-md-8) -->
        <div class="col-12 col-md-8">
            <div class="card ">
                <div class="card-body">
                    <!-- Search Bar and Add Item Button -->
                    <div class="row mb-4 align-items-center">
                        <div class="col-12 col-sm-auto mb-3 mb-sm-0">
                            <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus-circle me-1"></i> ADD NEW ITEM
                            </button>
                        </div>
                        <div class="col-12 col-sm">
                            <form method="GET" action="{{ route('product.search') }}" class="d-flex">
                                <input type="text" name="query" class="form-control" placeholder="Search items here..." value="{{ request('query') }}">
                                <button type="submit" class="btn btn-success ms-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Menu Grid -->
                    @if(isset($products) && $products->isNotEmpty())
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 overflow-auto" style="max-height: 600px;">
                        @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100 border-0">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="add-to-cart text-center border shadow-sm p-3" style="cursor: pointer;" onclick="this.closest('form').submit();">
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                            class="img-fluid rounded mx-auto d-block" 
                                            style="height: 120px; object-fit: contain;" 
                                            alt="{{ $product->name }}">
                                    </div>
                                </form>
                                <div class="card-body text-center">
                                    <h6 class="card-title text-truncate">{{ $product->name }}</h6>
                                    <p class="card-text text-success fw-bold">${{ number_format($product->selling_price, 2) }}</p>
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                                
            </div>
                    <!-- Category Navigation -->
                <div class="mt-4 pt-3 ">
                    <div class="d-flex flex-wrap align-items-center justify-content-between ">
                        <!-- Navigation Links -->
                        <ul class="nav nav-pills flex-wrap">
                            <li class="nav-item m-1">
                                <a class="nav-link {{ request('type') == null ? 'active text-white bg-success' : 'text-success border border-success' }}" 
                                href="{{ route('home') }}">All</a>
                            </li>
                            <li class="nav-item m-1">
                                <a class="nav-link {{ request('type') == 'coffee' ? 'active text-white bg-success' : 'text-success border border-success' }}" 
                                href="{{ route('product.index', ['type' => 'coffee']) }}">Coffee</a>
                            </li>
                            <li class="nav-item m-1">
                                <a class="nav-link {{ request('type') == 'beverages' ? 'active text-white bg-success' : 'text-success border border-success' }}" 
                                href="{{ route('product.index', ['type' => 'beverages']) }}">Beverages</a>
                            </li>
                            <li class="nav-item m-1">
                                <a class="nav-link {{ request('type') == 'bbq' ? 'active text-white bg-success' : 'text-success border border-success' }}" 
                                href="{{ route('product.index', ['type' => 'bbq']) }}">BBQ</a>
                            </li>
                            <li class="nav-item m-1">
                                <a class="nav-link {{ request('type') == 'snacks' ? 'active text-white bg-success' : 'text-success border border-success' }}" 
                                href="{{ route('product.index', ['type' => 'snacks']) }}">Snacks</a>
                            </li>
                            <li class="nav-item m-1">
                                <a class="nav-link {{ request('type') == 'desserts' ? 'active text-white bg-success' : 'text-success border border-success' }}" 
                                href="{{ route('product.index', ['type' => 'desserts']) }}">Desserts</a>
                            </li>
                        </ul>

                        <!-- Order Buttons -->
                        <div class="d-flex flex-wrap gap-2 mt-3 mt-sm-0">
                            <form method="POST" action="{{ route('cart.pay') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Cancel Order</button>
                            </form>
                            <button class="btn btn-outline-success">Hold Order</button>
                        </div>
                    </div>
                </div>

        </div>

        <!-- Right Side - Cart (col-md-4) -->
        <div class="col-12 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white text-center py-3">
                    <h5 class="mb-0">Checkout</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Cart Items -->
                    <div class="cart-items" style="max-height: 350px; overflow-y: auto;">
                    @if(session('cart') && count(session('cart')) > 0)
                    <table class="table border">
                        <thead>
                            <tr class="table-light text-center">
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                         @foreach(session('cart', []) as $id => $item)
                            <tr>
                                <td>
                                    <form action="{{ route('cart.remove') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $id }}">
                                        <button type="submit" class="btn btn-link text-success">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> 
                                    {{ $item['name'] }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!-- Decrease Quantity -->
                                        <form action="{{ route('cart.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-circle"
                                                    style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </form>

                                        <span class="mx-2">{{ $item['quantity'] }}</span>

                                        <!-- Increase Quantity -->
                                        <form action="{{ route('cart.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-circle"
                                                    style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>${{ number_format($item['total_price'] , 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    </table>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">Your cart is empty</p>
                    </div>
                     @endif
                </div>

                    <!-- Cart Summary -->
                    @php
                        // Retrieve cart totals from the session
                        $cartTotals = session('cart_totals', [
                            'subtotal' => 0,
                            'tax' => 0,
                            'discount_percentage' => 0,
                            'discount_amount' => 0,
                            'grand_total' => 0,
                        ]);
                    @endphp
                    <div class="border-top pt-3 p-4 bg-light" style="margin-top:50px">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Sub Total</span>
                            <span class="cart-subtotal">${{ number_format($cartTotals['subtotal'], 2) }}</span>
                        </div>
                        <form id="discount-form" method="POST" action="{{ route('apply.discount') }}" class="d-flex justify-content-between align-items-center mb-3">
                            @csrf
                            <span>Discount</span>
                            <div class="d-flex align-items-center">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="discount_percentage" class="form-control" style="width: 60px;" value="{{ $cartTotals['discount_percentage'] }}">
                                    <button type="submit" class="btn btn-sm btn-success">Apply</button>
                                </div>
                            </div>
                        </form>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax 1.5%</span>
                            <span class="cart-tax">${{ number_format($cartTotals['tax'] ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold pt-2 border-top mt-3">
                            <span>Total</span>
                            <span class="cart-total">${{ number_format($cartTotals['grand_total'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white py-3">
                        <form id="payment-form" method="POST" action="{{ route('cart.pay') }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100 process-payment">
                                Pay ($<span class="cart-total">{{ number_format($cartTotals['grand_total'] ?? 0, 2) }}</span>)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade mt-5" id="addProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image:</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Type:</label>
                        <select name="type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="coffee">Coffee</option>
                            <option value="beverages">Beverages</option>
                            <option value="bbq">BBQ</option>
                            <option value="snacks">Snacks</option>
                            <option value="desserts">Desserts</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Purchase Price:</label>
                        <input type="number" name="purchase_price" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selling Price:</label>
                        <input type="number" name="selling_price" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Sold Quantity:</label>
                        <input type="number" name="total_sold_quantity" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection