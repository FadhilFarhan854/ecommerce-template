@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
<style>
    .product-detail-container {
        margin: 2rem 0;
    }
    .product-detail {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .product-gallery {
        height: 400px;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 1.2rem;
        font-weight: 500;
    }
    .product-info {
        padding: 2rem;
    }
    .product-category {
        font-size: 0.9rem;
        color: #3b82f6;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    .product-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        line-height: 1.3;
    }
    .product-price {
        font-size: 2rem;
        font-weight: 700;
        color: #059669;
        margin-bottom: 1.5rem;
    }
    .product-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 2rem;
    }
    .product-actions {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        text-align: center;
    }
    .btn-primary {
        background: #3b82f6;
        color: white;
    }
    .btn-primary:hover {
        background: #2563eb;
        color: white;
    }
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    .btn-secondary:hover {
        background: #4b5563;
        color: white;
    }
    .btn-success {
        background: #059669;
        color: white;
    }
    .btn-success:hover {
        background: #047857;
        color: white;
    }
    .product-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 6px;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .meta-label {
        font-weight: 500;
        color: #6b7280;
        font-size: 0.9rem;
    }
    .meta-value {
        font-size: 1rem;
        color: #1f2937;
    }
    .related-products {
        margin-top: 3rem;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    .product-card {
        background: white;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    .product-card:hover {
        transform: translateY(-2px);
    }
    .product-image {
        height: 150px;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 0.9rem;
    }
    .card-info {
        padding: 1rem;
    }
    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .card-price {
        font-size: 1.1rem;
        font-weight: 600;
        color: #059669;
        margin-bottom: 0.5rem;
    }
    .breadcrumb {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #6b7280;
    }
    .breadcrumb a {
        color: #3b82f6;
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .product-info {
            padding: 1.5rem;
        }
        .product-title {
            font-size: 1.5rem;
        }
        .product-price {
            font-size: 1.5rem;
        }
        .product-actions {
            flex-direction: column;
        }
        .product-meta {
            grid-template-columns: 1fr;
        }
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="product-detail-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('products.catalog') }}">Katalog</a>
            <span>›</span>
            <span>{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
            <span>›</span>
            <span>{{ $product->name }}</span>
        </div>

        <div class="product-detail">
            <div class="product-gallery">
                <span>Foto Produk</span>
            </div>
            
            <div class="product-info">
                <div class="product-category">{{ $product->category->name ?? 'Tanpa Kategori' }}</div>
                <h1 class="product-title">{{ $product->name }}</h1>
                <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                
                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Kategori</span>
                        <span class="meta-value">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Stok</span>
                        <span class="meta-value">{{ $product->stock ?? 'Tersedia' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Ditambahkan</span>
                        <span class="meta-value">{{ $product->created_at->format('d F Y') }}</span>
                    </div>
                </div>

                <div class="product-description">
                    {{ $product->description }}
                </div>

                <div class="product-actions">
                    @auth
                        <button class="btn btn-success">Tambah ke Keranjang</button>
                        <button class="btn btn-primary">Beli Sekarang</button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Login untuk Membeli</a>
                    @endauth
                    <a href="{{ route('products.catalog') }}" class="btn btn-secondary">Kembali ke Katalog</a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="related-products">
                <h2 class="section-title">Produk Serupa</h2>
                <div class="products-grid">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="product-card">
                            <div class="product-image">
                                <span>Foto Produk</span>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title">{{ $relatedProduct->name }}</h3>
                                <div class="card-price">Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}</div>
                                <a href="{{ route('products.show-detail', $relatedProduct) }}" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Lihat Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
                                        <td><code>{{ $product->slug }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Category:</th>
                                        <td>
                                            <span class="badge bg-primary">{{ $product->category->name }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Price:</th>
                                        <td><strong class="text-success fs-5">${{ number_format($product->price, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Stock:</th>
                                        <td>
                                            <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                                {{ $product->stock }} units
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'warning' }}">
                                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $product->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Description</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $product->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit Product
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="bi bi-trash"></i> Delete Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
