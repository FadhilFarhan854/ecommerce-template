@extends('layouts.app')

@section('title', 'Lupa Password - Rama Perfume')

@section('content')
<div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 1rem; color: #1f2937;">Lupa Password?</h2>
        <p style="text-align: center; color: #6b7280; margin-bottom: 2rem;">
            Masukkan email Anda dan kami akan mengirimkan instruksi reset password.
        </p>
        
        @if (session('status'))
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;"
                       placeholder="Masukkan email Anda">
            </div>

            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; cursor: pointer; margin-bottom: 1rem;">
                📧 Kirim Instruksi Reset
            </button>
        </form>

        <div style="text-align: center;">
            <p style="color: #6b7280;">
                Ingat password Anda? 
                <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Kembali ke Login</a>
            </p>
        </div>

        <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-top: 1.5rem; font-size: 0.9rem; color: #6b7280;">
            <strong>💡 Catatan:</strong> Email reset akan dikirim dari alamat email admin. Silakan cek inbox dan folder spam Anda.
        </div>
    </div>
</div>
@endsection
