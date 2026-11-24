{{-- ═══════════════════════════════════════════════════════════════════════════
     FILE 1: resources/views/status.blade.php
     The report status checker page.
     Users paste their tracking token and see what happened to their report.
    ═══════════════════════════════════════════════════════════════════════════ --}}

@extends('layouts.app')
@section('title', 'Check Report Status — BIDROHI')
@section('content')

<style>
.status-page{min-height:100vh;background:#060606;display:flex;align-items:flex-start;justify-content:center;padding:60px 16px 80px}
.status-card{width:100%;max-width:560px}
.s-eyebrow{font-family:'IBM Plex Mono',monospace;font-size:10px;letter-spacing:3px;color:#DC2626;text-transform:uppercase;margin-bottom:10px}
.s-title{font-family:'IBM Plex Mono',monospace;font-size:24px;color:#e8e8e8;font-weight:700;margin-bottom:8px}
.s-sub{font-family:'IBM Plex Mono',monospace;font-size:9px;color:#444;letter-spacing:1px;line-height:1.8;margin-bottom:32px}
.field-label{font-family:'IBM Plex Mono',monospace;font-size:9px;color:#555;letter-spacing:2px;text-transform:uppercase;display:block;margin-bottom:8px}
.field-input{width:100%;background:#0a0a0a;border:1px solid #1a1a1a;color:#e0e0e0;font-family:'IBM Plex Mono',monospace;font-size:11px;padding:12px 14px;outline:none;transition:border-color .2s;letter-spacing:1px}
.field-input:focus{border-color:#333}
.check-btn{width:100%;padding:14px;background:#DC2626;color:#fff;border:none;font-family:'IBM Plex Mono',monospace;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;cursor:pointer;margin-top:12px;transition:background .2s}
.check-btn:hover{background:#b91c1c}
.check-btn:disabled{background:#1a1a1a;color:#444;cursor:not-allowed}
.result-box{display:none;margin-top:28px;border:1px solid #111;padding:24px}
.r-status{font-family:'IBM Plex Mono',monospace;font-size:11px;letter-spacing:2px;font-weight:700;margin-bottom:6px}
.r-cat{font-family:'IBM Plex Mono',monospace;font-size:9px;color:#DC2626;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px}
.r-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #0d0d0d}
.r-label{font-family:'IBM Plex Mono',monospace;font-size:8px;color:#444;letter-spacing:1.5px}
.r-val{font-family:'IBM Plex Mono',monospace;font-size:8px;color:#aaa;letter-spacing:0.5px;text-align:right;max-width:60%}
.error-bar{display:none;padding:11px 14px;background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);font-family:'IBM Plex Mono',monospace;font-size:9px;color:#DC2626;letter-spacing:1px;margin-top:12px}
</style>

<div class="status-page"><div class="status-card">
  <div class="s-eyebrow">◈ Report Tracker</div>
  <h1 class="s-title">Check Your Report</h1>
  <p class="s-sub">Paste the tracking token you received when you submitted your report.<br>Your identity is never revealed — only the report status is shown.</p>

  <label class="field-label">Tracking Token</label>
  <input class="field-input" id="tokenInput" type="text" placeholder="Paste your tracking token here..." spellcheck="false">
  <button class="check-btn" id="checkBtn" onclick="checkStatus()">CHECK STATUS</button>
  <div class="error-bar" id="errorBar"></div>

  <div class="result-box" id="resultBox">
    <div class="r-status" id="rStatus"></div>
    <div class="r-cat"    id="rCat"></div>
    <div id="rRows"></div>
    <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
      <a href="/report" style="font-family:'IBM Plex Mono',monospace;font-size:8px;color:#555;border:1px solid #1a1a1a;padding:7px 14px;text-decoration:none;letter-spacing:1px;">SUBMIT ANOTHER</a>
      <a href="/dashboard" style="font-family:'IBM Plex Mono',monospace;font-size:8px;color:#DC2626;border:1px solid #DC2626;padding:7px 14px;text-decoration:none;letter-spacing:1px;">VIEW CRIME MAP</a>
    </div>
  </div>
</div></div>

<script>
const STATUS_COLORS = { pending:'#F4C430', investigating:'#0891B2', verified:'#00FF88', resolved:'#8B5CF6', rejected:'#DC2626' };

async function checkStatus() {
  const token = document.getElementById('tokenInput').value.trim();
  if (!token) { showErr('Please paste your tracking token.'); return; }

  const btn = document.getElementById('checkBtn');
  btn.disabled = true; btn.textContent = 'CHECKING...';
  document.getElementById('errorBar').style.display = 'none';
  document.getElementById('resultBox').style.display = 'none';

  try {
    const witnessToken = localStorage.getItem('bidrohi_witness_token') || '';
    const res  = await fetch('/api/witness/status', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({ tracking_token: token, witness_token: witnessToken }),
    });
    const json = await res.json();

    if (json.success && json.report) {
      const r = json.report;
      const statusColor = STATUS_COLORS[r.status] || '#555';

      document.getElementById('rStatus').innerHTML =
        `<span style="color:${statusColor}">${r.status?.toUpperCase() || 'UNKNOWN'}</span>`;
      document.getElementById('rCat').textContent =
        r.category ? r.category.replace(/_/g,' ').toUpperCase() : '—';

      const rows = [
        ['District',        r.district || '—'],
        ['Submitted',       r.created_at ? new Date(r.created_at).toLocaleDateString('en-BD',{day:'numeric',month:'short',year:'numeric'}) : '—'],
        ['AI Category',     r.ai_category ? r.ai_category.replace(/_/g,' ') : 'Analyzing...'],
        ['AI Severity',     r.ai_severity ? (r.ai_severity >= 0.8 ? 'CRITICAL' : r.ai_severity >= 0.6 ? 'HIGH' : r.ai_severity >= 0.4 ? 'MEDIUM' : 'LOW') : 'Analyzing...'],
        ['Evidence Files',  r.evidence_count ? `${r.evidence_count} file(s) encrypted` : 'None'],
        ['Nearest Thana',   r.nearest_thana || 'Calculating...'],
      ];

      document.getElementById('rRows').innerHTML = rows.map(([l, v]) =>
        `<div class="r-row"><span class="r-label">${l}</span><span class="r-val">${v}</span></div>`
      ).join('');

      document.getElementById('resultBox').style.display = 'block';
    } else {
      showErr(json.message || 'Token not found. Please check and try again.');
    }
  } catch(e) {
    showErr('Network error. Please try again.');
  } finally {
    btn.disabled = false; btn.textContent = 'CHECK STATUS';
  }
}

function showErr(msg) {
  const el = document.getElementById('errorBar');
  el.textContent = '✗ ' + msg;
  el.style.display = 'block';
}

// Pre-fill if token in URL: /status?token=abc123
window.addEventListener('load', () => {
  const params = new URLSearchParams(window.location.search);
  if (params.get('token')) {
    document.getElementById('tokenInput').value = params.get('token');
    checkStatus();
  }
});
</script>
@endsection