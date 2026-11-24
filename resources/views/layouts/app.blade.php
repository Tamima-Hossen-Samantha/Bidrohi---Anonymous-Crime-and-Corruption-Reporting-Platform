<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'BIDROHI — বিদ্রোহী')</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;0,900;1,400;1,600&family=Noto+Sans+Bengali:wght@300;400;600;700&family=JetBrains+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="shortcut icon" href="{{ asset('images/bidrohi-logo.png') }}">
<link rel="stylesheet" href="{{ asset('CSS/landing.css') }}">
@stack('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div id="cur"></div>
<div id="cur-ring"></div>

@include('layouts.partials.landing_navbar')

<main>
  @yield('content')
</main>

@include('layouts.partials.landing_footer')

{{-- Global Scripts --}}
<script>
const cur  = document.getElementById('cur');
const ring = document.getElementById('cur-ring');
document.addEventListener('mousemove', e => {
  cur.style.transform  = `translate(${e.clientX}px,${e.clientY}px) translate(-50%,-50%)`;
  setTimeout(() => { ring.style.transform = `translate(${e.clientX}px,${e.clientY}px) translate(-50%,-50%)`; }, 80);
});

/* ── Theme ── */
function toggleTheme() {
  const h    = document.documentElement;
  const dark = h.getAttribute('data-theme') === 'dark';
  h.setAttribute('data-theme', dark ? 'light' : 'dark');
  document.getElementById('theme-lbl').textContent = dark ? 'Light' : 'Dark';
  localStorage.setItem('bidrohi-theme', dark ? 'light' : 'dark');
}
const saved = localStorage.getItem('bidrohi-theme');
if (saved) {
  document.documentElement.setAttribute('data-theme', saved);
  document.getElementById('theme-lbl').textContent = saved === 'dark' ? 'Dark' : 'Light';
}

/* ── Navbar shrink on scroll ── */
window.addEventListener('scroll', () => {
  document.getElementById('navbar').style.height = window.scrollY > 50 ? '54px' : '68px';
});

/* ── Nav indicator ── */
const navLinks  = document.querySelectorAll('.nav-a');
const indicator = document.getElementById('navIndicator');
const navList   = document.getElementById('navLinks');
function moveIndicator(el) {
  const lr = navList.getBoundingClientRect();
  const er = el.getBoundingClientRect();
  indicator.style.left  = (er.left - lr.left) + 'px';
  indicator.style.width = er.width + 'px';
}
const activeLink = document.querySelector('.nav-a.active');
if (activeLink) moveIndicator(activeLink);
navLinks.forEach(a => {
  a.addEventListener('mouseenter', () => moveIndicator(a));
  a.addEventListener('click', () => {
    navLinks.forEach(l => l.classList.remove('active'));
    a.classList.add('active');
    moveIndicator(a);
  });
});

/* ── Active section highlight ── */
const sections = ['home','about','problem','features','how','rangers','tech'];
window.addEventListener('scroll', () => {
  let c = 'home';
  sections.forEach(id => {
    const el = document.getElementById(id);
    if (el && window.scrollY >= el.offsetTop - 100) c = id;
  });
  navLinks.forEach(a => {
    const href = a.getAttribute('href').replace('#','');
    if (href === c) { a.classList.add('active'); moveIndicator(a); }
    else a.classList.remove('active');
  });
});

/* ── Scroll reveal ── */
const observer = new IntersectionObserver((entries) => {
  entries.forEach((e, i) => {
    if (e.isIntersecting) {
      const t = e.target;
      setTimeout(() => {
        t.classList.add('in');
        if (t.classList.contains('reveal-feat')) {
          document.querySelectorAll('.spark-path,.spark-area').forEach(el => el.classList.add('animate'));
          const r2 = document.getElementById('radial2');
          if (r2) r2.style.strokeDashoffset = String(220 - (220 * 0.5));
          document.querySelectorAll('.rank-badge').forEach(rb => {
            rb.classList.add('in');
            const fill = rb.querySelector('.rank-bar-fill');
            if (fill) fill.style.width = rb.style.getPropertyValue('--w');
          });
        }
        if (t.classList.contains('reveal-step')) {
          setTimeout(() => document.getElementById('howLine').classList.add('animate'), 400);
        }
        if (t.classList.contains('reveal-donut')) {
          document.querySelectorAll('.legend-bar').forEach(lb => { lb.style.width = lb.style.getPropertyValue('--w'); });
        }
      }, i * 130);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll(
  '.reveal-about,.reveal-stat,.reveal-problem,.reveal-feat,.reveal-step,.reveal-data,.reveal-donut,.reveal-rank,.reveal-pt,.reveal-tech,.reveal-tech-vis'
).forEach(el => observer.observe(el));

const barObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      document.querySelectorAll('.bar').forEach((b, i) => {
        setTimeout(() => { b.classList.add('active'); b.style.height = b.style.getPropertyValue('--h'); }, i * 150);
      });
    }
  });
}, { threshold: 0.3 });
const bc = document.getElementById('barChart');
if (bc) barObs.observe(bc);
</script>

@stack('scripts')
</body>
</html>