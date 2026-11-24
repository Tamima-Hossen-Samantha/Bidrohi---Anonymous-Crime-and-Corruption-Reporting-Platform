{{--
    resources/views/ranger-auth.blade.php

    Combined Ranger Login + Register page.
    Users can toggle between the two forms.

    What Rangers are:
    - Optional upgrade from anonymous witness
    - They choose a username (no real name needed)
    - They choose a district to protect
    - They earn points for submitting and verifying reports
    - They climb 5 Bangla rank tiers

    After login:
    - JWT token stored in localStorage as 'ranger_auth_token'
    - Username stored as 'ranger_username'
    - User redirected to /ranger/profile
--}}

@extends('layouts.app')

@section('title', 'Rangers — BIDROHI বিদ্রোহী')

@section('content')

<style>
.auth-page {
  min-height: 100vh;
  background: #060606;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 60px 16px 80px;
}
.auth-card {
  width: 100%;
  max-width: 480px;
}
.auth-header {
  margin-bottom: 32px;
  text-align: center;
}
.auth-eyebrow {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 10px;
  letter-spacing: 3px;
  color: #8B5CF6;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.auth-title {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 24px;
  color: #e8e8e8;
  font-weight: 700;
  margin-bottom: 8px;
}
.auth-title em { color: #8B5CF6; font-style: normal; }
.auth-sub {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 9px;
  color: #444;
  letter-spacing: 1px;
  line-height: 1.8;
}

/* Toggle tabs */
.tab-row {
  display: flex;
  gap: 0;
  margin-bottom: 28px;
  border: 1px solid #111;
}
.tab-btn {
  flex: 1;
  padding: 10px;
  background: transparent;
  border: none;
  color: #444;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 9px;
  letter-spacing: 2px;
  text-transform: uppercase;
  cursor: pointer;
  transition: all 0.2s;
}
.tab-btn.active {
  background: #8B5CF6;
  color: #fff;
}

/* Rank preview */
.rank-preview {
  display: flex;
  gap: 4px;
  justify-content: center;
  margin-bottom: 28px;
  flex-wrap: wrap;
}
.rank-pip {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 8px;
  padding: 3px 8px;
  border: 1px solid #1a1a1a;
  color: #333;
  letter-spacing: 0.5px;
  cursor: default;
}

/* Form fields */
.field { margin-bottom: 20px; }
.field-label {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 9px;
  color: #555;
  letter-spacing: 2px;
  text-transform: uppercase;
  display: block;
  margin-bottom: 7px;
}
.field-label em { color: #DC2626; font-style: normal; }
.field-input, .field-select {
  width: 100%;
  background: #0a0a0a;
  border: 1px solid #1a1a1a;
  color: #e0e0e0;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  padding: 12px 14px;
  outline: none;
  transition: border-color 0.2s;
  letter-spacing: 0.5px;
}
.field-input:focus, .field-select:focus { border-color: #333; }
.field-select option { background: #0a0a0a; }
.field-hint {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 8px;
  color: #333;
  letter-spacing: 1px;
  margin-top: 5px;
  line-height: 1.6;
}

.divider { height: 1px; background: #111; margin: 24px 0; }

.submit-btn {
  width: 100%;
  padding: 14px;
  background: #8B5CF6;
  color: #fff;
  border: none;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.2s;
}
.submit-btn:hover { background: #7c3aed; }
.submit-btn:disabled { background: #1a1a1a; color: #444; cursor: not-allowed; }

.error-bar {
  display: none;
  padding: 11px 14px;
  background: rgba(220,38,38,0.08);
  border: 1px solid rgba(220,38,38,0.2);
  font-family: 'IBM Plex Mono', monospace;
  font-size: 9px;
  color: #DC2626;
  letter-spacing: 1px;
  margin-bottom: 16px;
}
.success-bar {
  display: none;
  padding: 11px 14px;
  background: rgba(0,255,136,0.05);
  border: 1px solid rgba(0,255,136,0.15);
  font-family: 'IBM Plex Mono', monospace;
  font-size: 9px;
  color: #00FF88;
  letter-spacing: 1px;
  margin-bottom: 16px;
}

/* Verify email notice */
.verify-box {
  display: none;
  text-align: center;
  padding: 32px;
  border: 1px solid #111;
  background: #0a0a0a;
}
.verify-title {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 14px;
  color: #00FF88;
  letter-spacing: 2px;
  margin-bottom: 10px;
  font-weight: 700;
}
.verify-text {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 9px;
  color: #555;
  letter-spacing: 1px;
  line-height: 1.9;
}
</style>

<div class="auth-page">
<div class="auth-card">

  {{-- Header --}}
  <div class="auth-header">
    <div class="auth-eyebrow">◆ Ranger Network</div>
    <h1 class="auth-title">Become a <em>Ranger</em></h1>
    <p class="auth-sub">
      Join Bangladesh's citizen intelligence network.<br>
      Earn ranks. Protect your district. Remain anonymous.
    </p>
  </div>

  {{-- Rank preview pills --}}
  <div class="rank-preview">
    <div class="rank-pip" style="color:#555;">নাগরিক</div>
    <div class="rank-pip" style="color:#00FF88;border-color:#00FF88;">সাক্ষী</div>
    <div class="rank-pip" style="color:#0891B2;border-color:#0891B2;">প্রতিজ্ঞা</div>
    <div class="rank-pip" style="color:#F4C430;border-color:#F4C430;">রক্ষক</div>
    <div class="rank-pip" style="color:#DC2626;border-color:#DC2626;">বিদ্রোহী</div>
  </div>

  {{-- Tab toggle: Login / Register --}}
  <div class="tab-row">
    <button class="tab-btn active" id="tabLogin" onclick="showTab('login')">LOGIN</button>
    <button class="tab-btn"        id="tabReg"   onclick="showTab('register')">REGISTER</button>
  </div>

  {{-- Error / Success bars --}}
  <div class="error-bar"   id="errorBar"></div>
  <div class="success-bar" id="successBar"></div>

  {{-- ── LOGIN FORM ── --}}
  <div id="loginForm">
    <div class="field">
      <label class="field-label">Email <em>*</em></label>
      <input class="field-input" type="email" id="loginEmail" placeholder="your@email.com" autocomplete="email">
    </div>
    <div class="field">
      <label class="field-label">Password <em>*</em></label>
      <input class="field-input" type="password" id="loginPass" placeholder="••••••••" autocomplete="current-password">
    </div>
    <div class="divider"></div>
    <button class="submit-btn" id="loginBtn" onclick="doLogin()">LOGIN TO RANGER ACCOUNT</button>
    <p style="font-family:'IBM Plex Mono',monospace;font-size:8px;color:#333;
               text-align:center;margin-top:14px;letter-spacing:1px;">
      Don't have an account? <span onclick="showTab('register')"
        style="color:#8B5CF6;cursor:pointer;text-decoration:underline;">Register here</span>
    </p>
  </div>

  {{-- ── REGISTER FORM ── --}}
  <div id="registerForm" style="display:none;">

    <div class="field">
      <label class="field-label">Username <em>*</em></label>
      <input class="field-input" type="text" id="regUsername" placeholder="your_ranger_alias" autocomplete="off">
      <p class="field-hint">This is your public identity on the platform. No real name needed. Letters, numbers, underscores only.</p>
    </div>

    <div class="field">
      <label class="field-label">Email <em>*</em></label>
      <input class="field-input" type="email" id="regEmail" placeholder="your@email.com" autocomplete="email">
      <p class="field-hint">Used only for account verification. Never shown publicly.</p>
    </div>

    <div class="field">
      <label class="field-label">Password <em>*</em></label>
      <input class="field-input" type="password" id="regPass" placeholder="Min 8 characters" autocomplete="new-password">
    </div>

    <div class="field">
      <label class="field-label">Confirm Password <em>*</em></label>
      <input class="field-input" type="password" id="regPassConfirm" placeholder="Repeat password" autocomplete="new-password">
    </div>

    <div class="field">
      <label class="field-label">Your District <em>*</em></label>
      <select class="field-select" id="regDistrict">
        <option value="">— Choose your district to protect —</option>
        @foreach([
          'Bagerhat','Bandarban','Barguna','Barisal','Bhola','Bogura','Brahmanbaria',
          'Chandpur','Chapainawabganj','Chattogram','Chuadanga','Cox\'s Bazar','Cumilla',
          'Dhaka','Dinajpur','Faridpur','Feni','Gaibandha','Gazipur','Gopalganj',
          'Habiganj','Jamalpur','Jashore','Jhenaidah','Jhalokati','Joypurhat',
          'Khagrachhari','Khulna','Kishoreganj','Kurigram','Kushtia','Lakshmipur',
          'Lalmonirhat','Madaripur','Magura','Manikganj','Meherpur','Moulvibazar',
          'Munshiganj','Mymensingh','Naogaon','Narail','Narayanganj','Narsingdi',
          'Natore','Netrokona','Nilphamari','Noakhali','Pabna','Panchagarh',
          'Patuakhali','Pirojpur','Rajbari','Rajshahi','Rangamati','Rangpur',
          'Satkhira','Shariatpur','Sherpur','Sirajganj','Sunamganj','Sylhet',
          'Tangail','Thakurgaon'
        ] as $d)
          <option value="{{ $d }}">{{ $d }}</option>
        @endforeach
      </select>
      <p class="field-hint">Your district determines your zone leaderboard position. You can change this later.</p>
    </div>

    <div class="divider"></div>
    <button class="submit-btn" id="registerBtn" onclick="doRegister()">CREATE RANGER ACCOUNT</button>
    <p style="font-family:'IBM Plex Mono',monospace;font-size:8px;color:#333;
               text-align:center;margin-top:14px;letter-spacing:1px;">
      Already a Ranger? <span onclick="showTab('login')"
        style="color:#8B5CF6;cursor:pointer;text-decoration:underline;">Login here</span>
    </p>

    {{-- Email verification notice --}}
    <div class="verify-box" id="verifyBox">
      <div class="verify-title">✓ CHECK YOUR EMAIL</div>
      <p class="verify-text">
        We sent a verification link to your email.<br>
        Click the link to activate your Ranger account.<br><br>
        <span style="color:#333;">Didn't receive it?</span>
        <span onclick="resendVerification()"
          style="color:#8B5CF6;cursor:pointer;text-decoration:underline;">Resend email</span>
      </p>
    </div>
  </div>

</div>
</div>

<script>
let registeredEmail = '';

// ── Tab switcher ─────────────────────────────────────────────────────────────
function showTab(tab) {
  document.getElementById('loginForm').style.display    = tab === 'login'    ? 'block' : 'none';
  document.getElementById('registerForm').style.display = tab === 'register' ? 'block' : 'none';
  document.getElementById('tabLogin').classList.toggle('active', tab === 'login');
  document.getElementById('tabReg').classList.toggle('active',   tab === 'register');
  hideMessages();
}

function showError(msg) {
  const el = document.getElementById('errorBar');
  el.textContent = '✗ ' + msg;
  el.style.display = 'block';
  document.getElementById('successBar').style.display = 'none';
}
function showSuccess(msg) {
  const el = document.getElementById('successBar');
  el.textContent = '✓ ' + msg;
  el.style.display = 'block';
  document.getElementById('errorBar').style.display = 'none';
}
function hideMessages() {
  document.getElementById('errorBar').style.display   = 'none';
  document.getElementById('successBar').style.display = 'none';
}

// ── LOGIN ────────────────────────────────────────────────────────────────────
async function doLogin() {
  const email = document.getElementById('loginEmail').value.trim();
  const pass  = document.getElementById('loginPass').value;

  if (!email || !pass) { showError('Please enter your email and password.'); return; }

  const btn = document.getElementById('loginBtn');
  btn.disabled = true;
  btn.textContent = 'LOGGING IN...';

  try {
    const res  = await fetch('/api/ranger/login', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({ email, password: pass }),
    });
    const json = await res.json();

    if (json.success && json.token) {
      // Store auth token + username in localStorage
      localStorage.setItem('ranger_auth_token', json.token);
      localStorage.setItem('ranger_username',   json.user?.username || '');
      localStorage.setItem('ranger_rank',       json.ranger?.rank   || 'nagorik');

      showSuccess('Logged in! Redirecting to your profile...');
      setTimeout(() => { window.location.href = '/ranger/profile'; }, 1200);
    } else {
      showError(json.message || 'Login failed. Check your credentials.');
      btn.disabled = false;
      btn.textContent = 'LOGIN TO RANGER ACCOUNT';
    }
  } catch (e) {
    showError('Network error. Please try again.');
    btn.disabled = false;
    btn.textContent = 'LOGIN TO RANGER ACCOUNT';
  }
}

// ── REGISTER ─────────────────────────────────────────────────────────────────
async function doRegister() {
  const username = document.getElementById('regUsername').value.trim();
  const email    = document.getElementById('regEmail').value.trim();
  const pass     = document.getElementById('regPass').value;
  const passConf = document.getElementById('regPassConfirm').value;
  const district = document.getElementById('regDistrict').value;

  if (!username || !email || !pass || !district) {
    showError('Please fill in all required fields.');
    return;
  }
  if (pass !== passConf) {
    showError('Passwords do not match.');
    return;
  }
  if (pass.length < 8) {
    showError('Password must be at least 8 characters.');
    return;
  }

  const btn = document.getElementById('registerBtn');
  btn.disabled = true;
  btn.textContent = 'CREATING ACCOUNT...';

  try {
    const res  = await fetch('/api/ranger/register', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        username,
        email,
        password:              pass,
        password_confirmation: passConf,
        district_name:         district,
      }),
    });
    const json = await res.json();

    if (json.success || res.status === 201) {
      registeredEmail = email;
      // Hide the form fields, show email verification notice
      document.getElementById('verifyBox').style.display = 'block';
      btn.style.display = 'none';
      showSuccess('Account created! Please verify your email to activate your Ranger account.');
    } else {
      const errors = json.errors
        ? Object.values(json.errors).flat().join(' ')
        : (json.message || 'Registration failed.');
      showError(errors);
      btn.disabled = false;
      btn.textContent = 'CREATE RANGER ACCOUNT';
    }
  } catch (e) {
    showError('Network error. Please try again.');
    btn.disabled = false;
    btn.textContent = 'CREATE RANGER ACCOUNT';
  }
}

// ── RESEND VERIFICATION EMAIL ─────────────────────────────────────────────────
async function resendVerification() {
  if (!registeredEmail) return;
  try {
    await fetch('/api/ranger/resend-verification', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ email: registeredEmail }),
    });
    showSuccess('Verification email resent. Please check your inbox.');
  } catch (e) {
    showError('Could not resend. Please try again.');
  }
}

// ── On load: redirect if already logged in ────────────────────────────────────
window.addEventListener('load', () => {
  if (localStorage.getItem('ranger_auth_token')) {
    window.location.href = '/ranger/profile';
  }
});
</script>

@endsection