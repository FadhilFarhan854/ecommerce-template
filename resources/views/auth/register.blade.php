@extends('layouts.app')

@section('title', 'Daftar - TokoKu Store')

@section('content')
<div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Daftar Akun Baru</h2>
        
        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Nama Lengkap</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus
                       style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required
                       style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="password_confirmation" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Konfirmasi Password</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;">
            </div>

            <button type="submit" 
                    style="width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; cursor: pointer;">
                Daftar
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: #6b7280;">
                Sudah punya akun? 
                <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
