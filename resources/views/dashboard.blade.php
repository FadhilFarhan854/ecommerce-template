@extends('layouts.app')

@section('title', 'Dashboard - TokoKu Store')

@section('content')
<div class="container" style="margin: 4rem auto; padding: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1 style="color: #1f2937; margin-bottom: 1rem;">Dashboard</h1>
        <p style="color: #6b7280; margin-bottom: 2rem;">Selamat datang, {{ Auth::user()->name }}!</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div style="background: #f3f4f6; padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #374151; margin-bottom: 0.5rem;">Profil Pengguna</h3>
                <p style="color: #6b7280; margin-bottom: 0.5rem;"><strong>Nama:</strong> {{ Auth::user()->name }}</p>
                <p style="color: #6b7280; margin-bottom: 0.5rem;"><strong>Email:</strong> {{ Auth::user()->email }}</p>
                <p style="color: #6b7280;"><strong>Role:</strong> {{ Auth::user()->role ?? 'Customer' }}</p>
            </div>
            
            <div style="background: #f3f4f6; padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #374151; margin-bottom: 0.5rem;">Menu Cepat</h3>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="{{ url('/') }}" class="btn btn-secondary" style="text-align: center;">Kembali ke Beranda</a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('products.index') }}" class="btn btn-primary" style="text-align: center;">Kelola Produk</a>
                        <a href="{{ route('categories.index') }}" class="btn btn-primary" style="text-align: center;">Kelola Kategori</a>
                        <a href="{{ route('admin.discounts.index') }}" class="btn btn-primary" style="text-align: center;">Kelola Diskon</a>
                    @endif
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
