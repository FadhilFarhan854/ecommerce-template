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

        @if (session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" id="registerForm">
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
                <label for="phone" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">No. Telepon (Opsional)</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}" 
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
                    id="registerBtn"
                    style="width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.3s;">
                <span id="registerBtnText">Daftar</span>
                <span id="registerBtnLoading" style="display: none;">📧 Mengirim verifikasi...</span>
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

<!-- Email Verification Popup Modal -->
<div id="emailVerificationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; margin: 2rem; text-align: center; position: relative;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">📧</div>
        
        <h3 style="color: #10b981; margin-bottom: 1rem;">Email Verifikasi Terkirim!</h3>
        
        <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6;">
            Kami telah mengirim email verifikasi ke <strong id="userEmail"></strong>. 
            Silakan periksa inbox atau <strong>folder spam</strong> Anda dan klik tombol verifikasi untuk mengaktifkan akun.
        </p>
        
        <div style="background: #fef3c7; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #f59e0b;">
            <p style="margin: 0; font-size: 0.9rem; color: #92400e;">
                <strong>💡 Tips:</strong> Jika email tidak masuk dalam 5 menit, periksa folder spam atau junk mail.
            </p>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <button onclick="resendVerification()" 
                    id="resendBtn"
                    style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                🔄 Kirim Ulang
            </button>
            
            <button onclick="closeModal()" 
                    style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                ✅ Mengerti
            </button>
        </div>
        
        <p style="margin-top: 1rem; font-size: 0.8rem; color: #9ca3af;">
            Setelah verifikasi, Anda akan diarahkan ke halaman login.
        </p>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const registerBtn = document.getElementById('registerBtn');
    const registerBtnText = document.getElementById('registerBtnText');
    const registerBtnLoading = document.getElementById('registerBtnLoading');
    
    // Show loading state
    registerBtnText.style.display = 'none';
    registerBtnLoading.style.display = 'inline';
    registerBtn.disabled = true;
    registerBtn.style.background = '#9ca3af';
    
    // Submit form via fetch
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success popup
            document.getElementById('userEmail').textContent = data.data.email;
            document.getElementById('emailVerificationModal').style.display = 'flex';
            
            // Store email for resend function
            window.userEmail = data.data.email;
        } else {
            // Show error
            alert(data.message || 'Terjadi kesalahan saat mendaftar');
            resetButton();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        resetButton();
    });
    
    function resetButton() {
        registerBtnText.style.display = 'inline';
        registerBtnLoading.style.display = 'none';
        registerBtn.disabled = false;
        registerBtn.style.background = '#3b82f6';
    }
});

function resendVerification() {
    const resendBtn = document.getElementById('resendBtn');
    const originalText = resendBtn.textContent;
    
    resendBtn.textContent = '⏳ Mengirim...';
    resendBtn.disabled = true;
    
    fetch('{{ route("verification.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            email: window.userEmail
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resendBtn.textContent = '✅ Terkirim!';
            setTimeout(() => {
                resendBtn.textContent = originalText;
                resendBtn.disabled = false;
            }, 3000);
        } else {
            alert(data.message || 'Gagal mengirim ulang email');
            resendBtn.textContent = originalText;
            resendBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
        resendBtn.textContent = originalText;
        resendBtn.disabled = false;
    });
}

function closeModal() {
    document.getElementById('emailVerificationModal').style.display = 'none';
    // Optionally redirect to login page
    // window.location.href = '{{ route("login") }}';
}

// Close modal on outside click
document.getElementById('emailVerificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
