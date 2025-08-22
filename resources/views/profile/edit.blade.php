@extends('layouts.app')

@section('title', 'Edit Profile - ' . config('app.name'))

@push('styles')
<style>
    .form-container {
        max-width: 600px;
        margin: 2rem auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 2rem;
    }
    .form-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
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
    }
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    .btn-secondary:hover {
        background: #4b5563;
        color: white;
    }
    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
    }
    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    @media (max-width: 768px) {
        .form-container {
            margin: 1rem;
            padding: 1.5rem;
        }
        .form-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-container">
        <h1 class="form-title">Edit Profile</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                       class="form-input" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="form-input" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                       class="form-input" placeholder="Contoh: 08123456789">
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- Alamat sekarang dikelola terpisah di tabel addresses --}}
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 1rem;">
                    <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
                        Alamat dikelola secara terpisah. 
                        <a href="{{ route('addresses.index') }}" style="color: #3b82f6; text-decoration: none;">
                            Kelola alamat Anda di sini →
                        </a>
                    </p>
                    @if($user->addresses->count() > 0)
                        <div style="margin-top: 0.75rem;">
                            <small style="color: #374151; font-weight: 500;">Alamat yang tersimpan: {{ $user->addresses->count() }}</small>
                            <ul style="margin: 0.5rem 0 0 1rem; padding: 0; color: #6b7280; font-size: 0.875rem;">
                                @foreach($user->addresses->take(2) as $address)
                                    <li style="margin-bottom: 0.25rem;">{{ Str::limit($address->alamat, 50) }}</li>
                                @endforeach
                                @if($user->addresses->count() > 2)
                                    <li style="color: #9ca3af;">... dan {{ $user->addresses->count() - 2 }} alamat lainnya</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('profile.show') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
