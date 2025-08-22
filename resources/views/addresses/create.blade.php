@extends('layouts.app')

@section('title', 'Add New Address - ' . config('app.name'))

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
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
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
        .form-row {
            grid-template-columns: 1fr;
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
        <h1 class="form-title">Add New Address</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('addresses.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="nama_depan" class="form-label">Nama Depan *</label>
                    <input type="text" id="nama_depan" name="nama_depan" value="{{ old('nama_depan') }}" 
                           class="form-input" required>
                    @error('nama_depan')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama_belakang" class="form-label">Nama Belakang *</label>
                    <input type="text" id="nama_belakang" name="nama_belakang" value="{{ old('nama_belakang') }}" 
                           class="form-input" required>
                    @error('nama_belakang')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="alamat" class="form-label">Alamat Lengkap *</label>
                <textarea id="alamat" name="alamat" class="form-input form-textarea" 
                          placeholder="Contoh: Jl. Sudirman No. 123, RT 01/RW 02" required>{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kelurahan" class="form-label">Kelurahan *</label>
                    <input type="text" id="kelurahan" name="kelurahan" value="{{ old('kelurahan') }}" 
                           class="form-input" required>
                    @error('kelurahan')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kecamatan" class="form-label">Kecamatan *</label>
                    <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan') }}" 
                           class="form-input" required>
                    @error('kecamatan')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kota" class="form-label">Kota *</label>
                    <input type="text" id="kota" name="kota" value="{{ old('kota') }}" 
                           class="form-input" required>
                    @error('kota')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="provinsi" class="form-label">Provinsi *</label>
                    <input type="text" id="provinsi" name="provinsi" value="{{ old('provinsi') }}" 
                           class="form-input" required>
                    @error('provinsi')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kode_pos" class="form-label">Kode Pos *</label>
                    <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}" 
                           class="form-input" placeholder="12345" maxlength="5" required>
                    @error('kode_pos')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="hp" class="form-label">Nomor HP</label>
                    <input type="text" id="hp" name="hp" value="{{ old('hp') }}" 
                           class="form-input" placeholder="08123456789">
                    @error('hp')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Address</button>
                <a href="{{ route('addresses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
