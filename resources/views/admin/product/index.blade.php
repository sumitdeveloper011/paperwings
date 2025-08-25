@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Products</h1>
                <p class="content-subtitle">Manage your products</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">All Products</h5>
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex gap-2 flex-wrap">
                            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="subcategory_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Sub Categories</option>
                                @foreach($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}" {{ $subCategoryId == $subCategory->id ? 'selected' : '' }}>
                                        {{ $subCategory->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="brand_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Brands</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..." value="{{ $search }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search || $categoryId || $subCategoryId || $brandId)
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $product->category->name }}</span>
                                            @if($product->subCategory)
                                                <br><small class="text-muted">{{ $product->subCategory->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->brand)
                                                <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>${{ number_format($product->total_price, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">Ex. Tax: ${{ number_format($product->price_without_tax, 2) }}</small>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.products.updateStatus', $product) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ $product->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $products->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Products Found</h5>
                        @if($search)
                            <p class="text-muted">No products found matching "{{ $search }}"</p>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">View All Products</a>
                        @elseif($categoryId || $subCategoryId || $brandId)
                            <p class="text-muted">No products found with selected filters</p>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">View All Products</a>
                        @else
                            <p class="text-muted">Start by creating your first product</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
