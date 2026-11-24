{{-- resources/views/landing.blade.php --}}
@extends('layouts.app')

@section('title', 'BIDROHI — বিদ্রোহী')

@section('content')

{{-- ══════════════════ HERO ══════════════════ --}}
<section class="hero" id="home">
  <div class="hero-left">
    <div class="hero-title-row">
      <span class="silence">SILENCE</span>
      <span class="ends">ENDS</span>
    </div>
    <div class="poem-wrap">
      <span class="poem-bn">
        <span class="w" style="animation-delay:0.85s">অন্যায়</span>&nbsp;
        <span class="w" style="animation-delay:0.97s">যে</span>&nbsp;
        <span class="w" style="animation-delay:1.09s">করে</span>&nbsp;
        <span class="w" style="animation-delay:1.21s">আর</span>&nbsp;
        <span class="w" style="animation-delay:1.33s">অন্যায়</span>&nbsp;
        <span class="w" style="animation-delay:1.45s">যে</span>&nbsp;
        <span class="w" style="animation-delay:1.57s">সহে</span>
      </span>
      <span class="poem-bn">
        <span class="w" style="animation-delay:1.7s">তব</span>&nbsp;
        <span class="w" style="animation-delay:1.82s">ঘৃণা</span>&nbsp;
        <span class="w" style="animation-delay:1.94s">তারে</span>&nbsp;
        <span class="w" style="animation-delay:2.06s">যেন</span>&nbsp;
        <span class="w" style="animation-delay:2.18s"><strong>তৃণসম</strong></span>&nbsp;
        <span class="w" style="animation-delay:2.3s"><strong>দহে।</strong></span>
      </span>
      <div class="poem-bottom">
        <span class="poem-quote-text">"চুপ থাকা মানেই অন্যায়ের পাশে থাকা"</span>
        <span class="poem-attr">— রবীন্দ্রনাথ ঠাকুর</span>
      </div>
    </div>
    <div class="hero-btns">
      <a href="/report" class="btn-primary">Submit Anonymous Report</a>
      <a href="/dashboard"    class="btn-outline">View Crime Map →</a>
    </div>
  </div>

  <div class="hero-right">
    <div class="map-wrap">
      <div class="pulse-ring"></div>
      <div class="pulse-ring"></div>
      <div class="pulse-ring"></div>
      <div class="map-img-wrap">
        <img src="{{ asset('images/bd-map.png') }}" alt="Bangladesh Map">
        <svg class="map-svg" viewBox="0 0 430 430" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <clipPath id="circ"><circle cx="215" cy="215" r="215"/></clipPath>
            <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
              <feGaussianBlur in="SourceGraphic" stdDeviation="4" result="blur"/>
              <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
            </filter>
          </defs>
        </svg>
      </div>
      <div class="live-badge"><div class="live-dot"></div>Live</div>
      <div class="f-stat" style="bottom:28px;left:-12px;">
        <div class="f-stat-n">64<em>+</em></div>
        <div class="f-stat-l">Districts</div>
      </div>
      <div class="f-stat" style="top:60px;left:-20px;">
        <div class="f-stat-n">0<em>%</em></div>
        <div class="f-stat-l">Identity Stored</div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════════════ TICKER ══════════════════ --}}
<div class="ticker">
  <div class="ticker-inner">
    <span class="tick">বেনামী রিপোর্ট</span><span class="tick">AI ক্রাইম ম্যাপ</span>
    <span class="tick">শিশু নির্যাতন সনাক্ত</span><span class="tick">রেঞ্জার নেটওয়ার্ক</span>
    <span class="tick">Anonymous Reporting</span><span class="tick">Encrypted Evidence</span>
    <span class="tick">Zero Identity Stored</span><span class="tick">NGO Auto-Alerts</span>
    <span class="tick">District Leaderboard</span><span class="tick">AI Crime Intelligence</span>
    <span class="tick">বেনামী রিপোর্ট</span><span class="tick">AI ক্রাইম ম্যাপ</span>
    <span class="tick">শিশু নির্যাতন সনাক্ত</span><span class="tick">রেঞ্জার নেটওয়ার্ক</span>
    <span class="tick">Anonymous Reporting</span><span class="tick">Encrypted Evidence</span>
    <span class="tick">Zero Identity Stored</span><span class="tick">NGO Auto-Alerts</span>
    <span class="tick">District Leaderboard</span><span class="tick">AI Crime Intelligence</span>
  </div>
</div>

{{-- ══════════════════ ABOUT ══════════════════ --}}
<section class="about-sec" id="about">
  <div class="about-stats">
    <div class="stat-card reveal-stat">
      <div class="stat-num">64<em>+</em></div>
      <div class="stat-body"><h4>Districts Covered</h4><p>Every thana in Bangladesh<br>monitored in real time</p></div>
    </div>
    <div class="stat-card reveal-stat">
      <div class="stat-num">0<em>%</em></div>
      <div class="stat-body"><h4>Identity Stored</h4><p>No name, email or IP address<br>ever logged by our system</p></div>
    </div>
    <div class="stat-card reveal-stat">
      <div class="stat-num">5<em>+</em></div>
      <div class="stat-body"><h4>NGO Partners</h4><p>BLAST, ASK, UNICEF BD<br>receive auto-alerts instantly</p></div>
    </div>
  </div>
  <div class="about-text">
    <div class="about-bn-title reveal-about">বিদ্রোহী</div>
    <p class="about-desc reveal-about">
      BIDROHI is Bangladesh's <strong>Anonymous crime witness platform</strong> built for every citizen who has witnessed corruption, child abuse, or harassment
      but had no safe way to speak up.<br><br>
      We use <strong>artificial intelligence, encrypted evidence storage,</strong> and a
      <strong>gamified ranger network</strong> to turn millions of silent witnesses into
      a collective force for truth and accountability.<br><br>
      <span class="evidence">No name. No trace. Just evidence.</span>
    </p>
  </div>
</section>

{{-- ══════════════════ PROBLEM ══════════════════ --}}
<section class="problem-sec" id="problem">
  <div class="problem-ghost">04</div>
  <h2 class="sec-title">Bangladesh has <em>no system</em> for anonymous truth</h2>
  <div class="problem-cards">
    <div class="p-card reveal-problem">
      <div class="p-top"><h3>No Centralized Reporting</h3></div>
      <p>Bangladesh has <strong>zero digital platform</strong> for anonymous reporting. Families use paper posters. Police use phone calls.</p>
      <div class="p-stat">170M<em>+</em> <span>citizens · zero system</span></div>
    </div>
    <div class="p-card reveal-problem">
      <div class="p-top"><h3>Silence Through Fear</h3></div>
      <p>Workers witness bribery → <strong>stay silent.</strong> Students see abuse → no safe channel. Thousands of crimes go unreported daily.</p>
      <div class="p-stat">1000<em>s</em> <span>crimes unreported daily</span></div>
    </div>
    <div class="p-card reveal-problem">
      <div class="p-top"><h3>Child Abuse Undetected</h3></div>
      <p>Abuse follows patterns — <strong>same location, same perpetrator.</strong> Without data, NGOs cannot identify or intervene in time.</p>
      <div class="p-stat">64<em>+</em> <span>districts · no monitoring</span></div>
    </div>
    <div class="p-card reveal-problem">
      <div class="p-top"><h3>Built Elsewhere, Not Here</h3></div>
      <p>I Paid A Bribe is for India. Ushahidi for Kenya. <strong>Nothing exists for Bangladesh</strong> — no Bangla, no local NGOs, no AI.</p>
      <div class="p-stat">0 <span>platforms built for Bangladesh</span></div>
    </div>
  </div>
</section>

{{-- ══════════════════ FEATURES ══════════════════ --}}
<section class="features-sec" id="features">
  <h2 class="sec-title">Everything built for <em>Bangladesh</em></h2>

  {{-- TOP ROW — 2 big cards --}}
  <div class="features-top">
    {{-- CARD 1: Evidence Vault --}}
    <div class="feat-card big reveal-feat">
      <h3>Anonymous Evidence Vault</h3>
      <p>Submit reports with photo, video or audio. Zero identity stored. AES-256 encrypted. Tamper-proof timestamping. Cannot be deleted.</p>
      <div class="feat-chart">
        <div class="bar-chart" id="barChart">
          <div class="bar" style="--h:45%" data-val="Corruption"><span class="bar-val">Corruption</span></div>
          <div class="bar" style="--h:72%" data-val="Child Abuse"><span class="bar-val">Child Abuse</span></div>
          <div class="bar" style="--h:38%" data-val="Harassment"><span class="bar-val">Harassment</span></div>
          <div class="bar" style="--h:28%" data-val="Other"><span class="bar-val">Other</span></div>
        </div>
      </div>
      <div style="display:flex;gap:24px;">
        <div><div class="feat-num">0<em>%</em></div><div class="feat-sub">identity stored</div></div>
      </div>
    </div>

    {{-- CARD 2: Crime Map --}}
    <div class="feat-card big reveal-feat">
      <h3>Bangladesh Crime Intelligence Map</h3>
      <p>Live heatmap of all 64 districts. Red, Yellow, Green zones update in real time. Click any thana for full intelligence brief.</p>
      <div class="feat-chart">
        <svg class="sparkline" viewBox="0 0 300 60" preserveAspectRatio="none">
          <defs>
            <linearGradient id="sparkGrad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#e03a3a" stop-opacity="0.3"/>
              <stop offset="100%" stop-color="#e03a3a" stop-opacity="0"/>
            </linearGradient>
          </defs>
          <path class="spark-area" id="sparkArea" d="M0 55 L30 48 L60 42 L90 35 L120 38 L150 28 L180 22 L210 18 L240 14 L270 10 L300 6 L300 60 L0 60 Z"/>
          <path class="spark-path" id="sparkPath" d="M0 55 L30 48 L60 42 L90 35 L120 38 L150 28 L180 22 L210 18 L240 14 L270 10 L300 6"/>
        </svg>
      </div>
      <div style="display:flex;justify-content:space-between;font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text3);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">
        <span>Jan</span><span>Mar</span><span>May</span><span>Jul</span><span>Sep</span><span>Now</span>
      </div>
      <div style="display:flex;gap:24px;">
        <div><div class="feat-num">64<em>+</em></div><div class="feat-sub">districts live</div></div>
        <div><div class="feat-num">3<em>×</em></div><div class="feat-sub">zone levels</div></div>
      </div>
    </div>
  </div>

  {{-- BOTTOM ROW — Crime Type Breakdown · Rangers Network · Public Dashboard --}}
  <div class="features-bottom">

    {{-- Crime Type Breakdown (was donut-wrap, now a proper card) --}}
    <div class="feat-card reveal-donut">
      <h3>Crime Type Breakdown</h3>
      <div class="donut-inner">
        <div class="donut">
          <svg viewBox="0 0 160 160">
            <circle class="donut-bg" cx="80" cy="80" r="60"/>
            <circle class="donut-seg" cx="80" cy="80" r="60" stroke="#e03a3a" style="--len:170;--off:0;stroke-dasharray:170 502;"/>
            <circle class="donut-seg" cx="80" cy="80" r="60" stroke="#c9a84c" style="--len:113;--off:-170;stroke-dasharray:113 502;"/>
            <circle class="donut-seg" cx="80" cy="80" r="60" stroke="#4a9a6a" style="--len:57;--off:-283;stroke-dasharray:57 502;"/>
            <circle class="donut-seg" cx="80" cy="80" r="60" stroke="#3a3a36" style="--len:38;--off:-340;stroke-dasharray:38 502;"/>
          </svg>
          <div class="donut-center">
            <div class="donut-center-num">100%</div>
            <div class="donut-center-lbl">Reports</div>
          </div>
        </div>
        <div class="donut-legend">
          <div class="legend-item"><div class="legend-dot" style="background:#e03a3a"></div><span class="legend-name">Corruption</span></div>
          <div class="legend-item"><div class="legend-dot" style="background:#c9a84c"></div><span class="legend-name">Child Abuse</span></div>
          <div class="legend-item"><div class="legend-dot" style="background:#4a9a6a"></div><span class="legend-name">Harassment</span></div>
          <div class="legend-item"><div class="legend-dot" style="background:#3a3a36"></div><span class="legend-name">Other</span></div>
        </div>
      </div>
    </div>

    {{-- Rangers Network --}}
    <div class="feat-card reveal-feat">
      <h3>Rangers Network</h3>
      <p>Pokémon GO-style civic engagement. Earn Bangla ranks. Compete by district. Own your zone.</p>
      <div class="rank-badges" id="rankBadges">
        <div class="rank-badge" style="--w:10%"><span class="rank-name">নাগরিক</span><div class="rank-bar-wrap"><div class="rank-bar-fill" style="background:#888"></div></div><span class="rank-pts">0</span></div>
        <div class="rank-badge" style="--w:25%"><span class="rank-name">সাক্ষী</span><div class="rank-bar-wrap"><div class="rank-bar-fill" style="background:#c9a84c"></div></div><span class="rank-pts">50</span></div>
        <div class="rank-badge" style="--w:50%"><span class="rank-name">প্রতিজ্ঞা</span><div class="rank-bar-wrap"><div class="rank-bar-fill"></div></div><span class="rank-pts">150</span></div>
        <div class="rank-badge" style="--w:75%"><span class="rank-name">রক্ষক</span><div class="rank-bar-wrap"><div class="rank-bar-fill"></div></div><span class="rank-pts">400</span></div>
        <div class="rank-badge" style="--w:100%"><span class="rank-name">বিদ্রোহী</span><div class="rank-bar-wrap"><div class="rank-bar-fill" style="background:#e03a3a"></div></div><span class="rank-pts">1000</span></div>
      </div>
    </div>

    {{-- Public Dashboard --}}
    <div class="feat-card reveal-feat">
      <h3>Public Dashboard</h3>
      <p>Live accountability scores per district. Journalist-ready data. NGO partner feeds. Corruption rankings.</p>
      <div class="feat-chart" style="height:80px;">
        <div class="radial-wrap">
          <div class="radial">
            <svg viewBox="0 0 80 80">
              <circle class="radial-bg"   cx="40" cy="40" r="35"/>
              <circle class="radial-fill" id="radial2" cx="40" cy="40" r="35" style="stroke:#27ae60;"/>
            </svg>
            <div class="radial-label" style="font-size:14px;">5<em style="color:var(--red2);font-style:normal;">+</em></div>
          </div>
          <div class="radial-info">
            <h4>NGO Partners</h4>
            <p>BLAST, ASK, UNICEF BD auto-alerted on pattern detection</p>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /features-bottom --}}
</section>

{{-- ══════════════════ HOW IT WORKS ══════════════════ --}}
<section class="how-sec" id="how">
  <div class="sec-eyebrow">How It Works</div>
  <h2 class="sec-title">Four steps. <em>Zero fear.</em></h2>
  <div class="how-flow">
    <div class="how-line"></div>
    <div class="how-line-fill" id="howLine"></div>
    <div class="how-steps">
      <div class="how-step reveal-step">
        <div class="step-circle"><span class="step-icon">📱</span></div>
        <div class="step-body">
          <div class="step-tag">Step 01</div>
          <h3>Report Anonymously</h3>
          <p>Open app. Select crime type. Add description + optional photo. Your identity is never stored — only an encrypted token on your device.</p>
        </div>
      </div>
      <div class="how-step reveal-step">
        <div class="step-circle"><span class="step-icon">🤖</span></div>
        <div class="step-body">
          <div class="step-tag">Step 02</div>
          <h3>AI Analyzes</h3>
          <p>Python microservice classifies the report, assigns a severity score, and checks for matching patterns from the same location automatically.</p>
        </div>
      </div>
      <div class="how-step reveal-step">
        <div class="step-circle"><span class="step-icon">🗺️</span></div>
        <div class="step-body">
          <div class="step-tag">Step 03</div>
          <h3>Map Updates</h3>
          <p>Zone scores recalculate instantly. District color changes if threshold crossed. Cluster circles update live on the Bangladesh heatmap.</p>
        </div>
      </div>
      <div class="how-step reveal-step">
        <div class="step-circle"><span class="step-icon">🔔</span></div>
        <div class="step-body">
          <div class="step-tag">Step 04</div>
          <h3>NGO Alerted</h3>
          <p>If an abuse pattern is detected — BLAST, Ain o Salish Kendra are automatically notified. Rangers verify. Collective truth prevails.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="how-data">
    <div class="how-data-card reveal-data">
      <div class="hd-label">Report submitted</div>
      <div class="hd-val">&lt;2<em>s</em></div>
      <div class="hd-desc">Time from submission to AI classification</div>
    </div>
    <div class="how-data-card reveal-data">
      <div class="hd-label">Zone update</div>
      <div class="hd-val">Real<em>-</em>time</div>
      <div class="hd-desc">Map zones recalculate after every report</div>
    </div>
    <div class="how-data-card reveal-data">
      <div class="hd-label">NGO alert trigger</div>
      <div class="hd-val">3<em>+</em></div>
      <div class="hd-desc">Reports from same location triggers auto-alert</div>
    </div>
    <div class="how-data-card reveal-data">
      <div class="hd-label">Cluster sync</div>
      <div class="hd-val">6<em>h</em></div>
      <div class="hd-desc">AI hotspot clustering runs every 6 hours</div>
    </div>
  </div>
</section>

{{-- ══════════════════ RANGERS ══════════════════ --}}
{{-- rangers-ghost div removed --}}
<section class="rangers-sec" id="rangers">
  <div class="sec-eyebrow">Rangers System</div>
  <h2 class="sec-title">Every witness is a <em>Ranger.</em></h2>
  <div class="rangers-layout">
    <div>
      <div class="rank-list">
        <div class="rank-row reveal-rank">
          <span class="rn">01</span>
          <div class="rank-info"><span class="rank-en">Nagorik</span><span class="rank-bn">নাগরিক</span></div>
          <span class="rank-desc">New citizen. Just joined the network.</span>
          <span class="rank-pts-badge">0 pts</span>
        </div>
        <div class="rank-row reveal-rank">
          <span class="rn">02</span>
          <div class="rank-info"><span class="rank-en">Shakkhi</span><span class="rank-bn">সাক্ষী</span></div>
          <span class="rank-desc">Active witness. Submitted first verified reports.</span>
          <span class="rank-pts-badge">50 pts</span>
        </div>
        <div class="rank-row reveal-rank">
          <span class="rn">03</span>
          <div class="rank-info"><span class="rank-en">Protigya</span><span class="rank-bn">প্রতিজ্ঞা</span></div>
          <span class="rank-desc">Committed guardian. Actively verifying reports.</span>
          <span class="rank-pts-badge">150 pts</span>
        </div>
        <div class="rank-row reveal-rank">
          <span class="rn">04</span>
          <div class="rank-info"><span class="rank-en">Rokkhok</span><span class="rank-bn">রক্ষক</span></div>
          <span class="rank-desc">Area protector. Controls their thana zone.</span>
          <span class="rank-pts-badge">400 pts</span>
        </div>
        <div class="rank-row legend-rank reveal-rank">
          <span class="rn">05</span>
          <div class="rank-info"><span class="rank-en">Bidrohi</span><span class="rank-bn">বিদ্রোহী</span></div>
          <span class="rank-desc">District legend. Named after Nazrul's immortal poem.</span>
          <span class="rank-pts-badge" style="border-color:var(--gold);color:var(--gold);">1000 pts</span>
        </div>
      </div>
      <div class="district-board reveal-rank">
        <div class="db-title">District Leaderboard — This Month</div>
        <div class="db-row"><span class="db-pos">01</span><span class="db-name">Dhaka</span><div class="db-bar-wrap"><div class="db-bar" style="width:95%"></div></div><span class="db-score">2,847</span></div>
        <div class="db-row"><span class="db-pos">02</span><span class="db-name">Chittagong</span><div class="db-bar-wrap"><div class="db-bar" style="width:74%"></div></div><span class="db-score">2,104</span></div>
        <div class="db-row"><span class="db-pos">03</span><span class="db-name">Sylhet</span><div class="db-bar-wrap"><div class="db-bar" style="width:52%"></div></div><span class="db-score">1,492</span></div>
        <div class="db-row"><span class="db-pos">04</span><span class="db-name">Rajshahi</span><div class="db-bar-wrap"><div class="db-bar" style="width:41%"></div></div><span class="db-score">1,163</span></div>
        <div class="db-row"><span class="db-pos">05</span><span class="db-name">Khulna</span><div class="db-bar-wrap"><div class="db-bar" style="width:33%"></div></div><span class="db-score">940</span></div>
      </div>
    </div>

    <div class="points-right">
      <div>
        <div class="points-title">How You Earn Points</div>
        <p class="points-desc">Every action builds your rank. The more you contribute, the higher your position in the district leaderboard — and the more your community is protected.</p>
      </div>
      <div class="points-grid">
        <div class="pt-card reveal-pt">
          <div class="pt-action">Submit Report</div>
          <div class="pt-val">+10</div>
          <div class="pt-sub">Every anonymous report submitted earns points</div>
        </div>
        <div class="pt-card reveal-pt">
          <div class="pt-action">Verify Report</div>
          <div class="pt-val">+15</div>
          <div class="pt-sub">Confirming a nearby report as credible</div>
        </div>
        <div class="pt-card reveal-pt">
          <div class="pt-action">Report Confirmed</div>
          <div class="pt-val">+25</div>
          <div class="pt-sub">When 3 Rangers verify your report</div>
        </div>
        <div class="pt-card reveal-pt">
          <div class="pt-action">NGO Takes Action</div>
          <div class="pt-val red">+100</div>
          <div class="pt-sub">Your report leads to real-world NGO response</div>
        </div>
      </div>
    </div>
  </div>
</section>




@endsection