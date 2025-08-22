@extends('layouts.app')

@section('title', 'Profile - ' . config('app.name'))

@push('styles')
<style>
    .profile-container {
        max-width: 800px;
        margin: 2rem auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2.5rem;
    }
    .profile-name {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .profile-email {
        font-size: 1rem;
        opacity: 0.9;
    }
    .profile-body {
        padding: 2rem;
    }
    .profile-section {
        margin-bottom: 2rem;
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0.5rem;
    }
    .profile-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .info-label {
        font-weight: 500;
        color: #6b7280;
        font-size: 0.9rem;
    }
    .info-value {
        font-size: 1rem;
        color: #1f2937;
        padding: 0.5rem 0;
    }
    .profile-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
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
    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    @media (max-width: 768px) {
        .profile-container {
            margin: 1rem;
        }
        .profile-actions {
            flex-direction: column;
        }
        .profile-info {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>
        </div>

        <div class="profile-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="profile-section">
                <h2 class="section-title">Informasi Personal</h2>
                <div class="profile-info">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value">{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nomor Telepon</span>
                        <span class="info-value">{{ $user->phone ?? 'Belum diisi' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Role</span>
                        <span class="info-value">{{ ucfirst($user->role) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Bergabung Sejak</span>
                        <span class="info-value">{{ $user->created_at->format('d F Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Alamat</span>
                        <span class="info-value">{{ $user->address ?? 'Belum diisi' }}</span>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h2 class="section-title">Aksi</h2>
                <div class="profile-actions">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    <a href="{{ route('profile.change-password') }}" class="btn btn-secondary">Ubah Password</a>
                    <a href="{{ route('products.catalog') }}" class="btn btn-secondary">Kembali ke Katalog</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
