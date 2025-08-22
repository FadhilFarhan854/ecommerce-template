<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category: {{ $category->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Category: {{ $category->name }}</h1>
                    <div>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Category Details</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>ID:</th>
                                        <td>{{ $category->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name:</th>
                                        <td>{{ $category->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Slug:</th>
                                        <td><code>{{ $category->slug }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $category->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Products in this Category ({{ $category->products->count() }})</h5>
                            </div>
                            <div class="card-body">
                                @if($category->products->count() > 0)
                                    <div class="list-group">
                                        @foreach($category->products as $product)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                                    <small class="text-success">${{ number_format($product->price, 2) }}</small>
                                                </div>
                                                <p class="mb-1">{{ Str::limit($product->description, 100) }}</p>
                                                <small>Stock: {{ $product->stock }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No products in this category yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
