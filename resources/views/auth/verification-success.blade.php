@extends('layouts.app')

@section('title', 'Email Terverifikasi - Rama Perfume')

@section('content')
<div class="container" style="max-width: 500px; margin: 4rem auto; padding: 2rem;">
    <div style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
        
        <!-- Success Icon -->
        <div style="width: 80px; height: 80px; background: #d1fae5; border-radius: 50%; margin: 0 auto 2rem; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 40px; height: 40px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 style="color: #1f2937; margin-bottom: 1rem; font-size: 2rem;">🎉 Email Berhasil Diverifikasi!</h1>
        
        <p style="color: #6b7280; margin-bottom: 2rem; font-size: 1.1rem;">
            Selamat! Akun Anda telah aktif dan siap digunakan.
        </p>

        <div style="background: #f0f9ff; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <h3 style="color: #0369a1; margin-bottom: 1rem;">✨ Akun Anda Sekarang Dapat:</h3>
            <ul style="text-align: left; color: #1e40af; list-style: none; padding: 0;">
                <li style="margin: 0.5rem 0;">🛍️ Berbelanja dan checkout</li>
                <li style="margin: 0.5rem 0;">📊 Mengakses dashboard pribadi</li>
                <li style="margin: 0.5rem 0;">📦 Melihat riwayat pesanan</li>
                <li style="margin: 0.5rem 0;">🎁 Mendapat promo eksklusif</li>
            </ul>
        </div>

        @auth
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('home') }}" 
                   style="display: inline-block; padding: 12px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 0.5rem;">
                    🏠 Kembali ke Beranda
                </a>
                <a href="{{ route('dashboard') }}" 
                   style="display: inline-block; padding: 12px 30px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 0.5rem;">
                    📊 Lihat Dashboard
                </a>
            </div>
        @else
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('login') }}" 
                   style="display: inline-block; padding: 12px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 0.5rem;">
                    🔐 Login Sekarang
                </a>
                <a href="{{ route('home') }}" 
                   style="display: inline-block; padding: 12px 30px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 0.5rem;">
                    🏠 Kembali ke Beranda
                </a>
            </div>
        @endauth

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 0.9rem;">
                Terima kasih telah bergabung dengan <strong>{{ config('landing.site.name', 'Rama Perfume') }}</strong>!
            </p>
        </div>
    </div>
</div>
@endsection
