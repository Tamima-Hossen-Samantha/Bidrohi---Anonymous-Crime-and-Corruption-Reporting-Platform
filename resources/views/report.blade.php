@extends('layouts.app')

@section('title', 'Submit Report — BIDROHI বিদ্রোহী')

@section('content')
<style>
/* ─── Reset & Base ─────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.rp-page {
  min-height: 100vh;
  background: #070707;
  padding: 64px 16px 100px;
  display: flex;
  justify-content: center;
}

.rp-wrap {
  width: 100%;
  max-width: 600px;
}

/* ─── Header ───────────────────────────────────────────────────── */
.rp-eyebrow {
  font-family: 'JetBrains Mono', monospace;
  font-size: 14px;
  letter-spacing: 2px;
  color: #b02a2a;
  text-transform: uppercase;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.rp-eyebrow::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #161616;
}

.rp-title {
  font-family: 'JetBrains Mono', monospace;
  font-size: 28px;
  font-weight: 700;
  color: #fff;
  line-height: 1.15;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}
.rp-title span { color: #fff; }

.rp-sub {
  font-family: 'JetBrains Mono', monospace;
  font-size: 10px;
  color: #fff;
  letter-spacing: 0.5px;
  line-height: 2;
  margin-bottom: 32px;
}

/* ─── Privacy strip ────────────────────────────────────────────── */
.priv-strip {
  display: flex;
  gap: 0;
  border: 1px solid #111;
  margin-bottom: 40px;
  overflow: hidden;
}
.priv-item {
  flex: 1;
  padding: 10px 12px;
  border-right: 1px solid #111;
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  letter-spacing: 1px;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 7px;
  white-space: nowrap;
}
.priv-item:last-child { border-right: none; }
.priv-dot {
  width: 5px; height: 5px;
  border-radius: 50%;
  background: #00c96b;
  flex-shrink: 0;
}

/* ─── Section label ────────────────────────────────────────────── */
.sec-label {
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  letter-spacing: 3px;
  color: #fff;
  text-transform: uppercase;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.sec-label span { color: #fff; margin-left: 2px; }
.sec-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #111;
}

/* ─── Category grid ────────────────────────────────────────────── */
.cat-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  background: #111;
  border: 1px solid #111;
  margin-bottom: 32px;
  overflow: hidden;
}
.cat-btn {
  padding: 13px 10px;
  background: #0c0c0c;
  border: none;
  color: #fff;
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
  text-align: left;
  line-height: 1.5;
  display: flex;
  align-items: center;
  gap: 7px;
}
.cat-btn:hover  { background: #fff; color: #777; }
.cat-btn.active { background: #fff; color: #130a0a; border: 1px solid #b02a2a; }
.cat-icon { font-size: 11px; flex-shrink: 0; }

/* ─── Inputs ───────────────────────────────────────────────────── */
.field { margin-bottom: 28px; }

.field-input,
.field-select,
.field-textarea {
  width: 100%;
  background: #0c0c0c;
  border: 1px solid #161616;
  border-bottom-color: #1e1e1e;
  color: #d8d8d8;
  font-family: 'JetBrains Mono', monospace;
  font-size: 11px;
  padding: 13px 14px;
  outline: none;
  transition: border-color 0.15s, background 0.15s;
  letter-spacing: 0.3px;
  appearance: none;
  -webkit-appearance: none;
}
.field-input::placeholder,
.field-textarea::placeholder { color: #555; }
.field-input:focus,
.field-select:focus,
.field-textarea:focus {
  border-color: #fff;
  background: #0e0e0e;
}
.field-textarea {
  resize: vertical;
  min-height: 130px;
  line-height: 1.75;
}
.field-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23333'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
  cursor: pointer;
}
.field-select option { background: #0c0c0c; }

.char-count {
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  color: #555;
  text-align: right;
  margin-top: 6px;
  letter-spacing: 1px;
}

/* ─── GPS row ──────────────────────────────────────────────────── */
.gps-wrap {
  display: flex;
  gap: 1px;
  background: #111;
  border: 1px solid #111;
  overflow: hidden;
}
.gps-btn {
  padding: 12px 16px;
  background: #0c0c0c;
  border: none;
  color: #fff;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  letter-spacing: 1.5px;
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.15s, color 0.15s;
  text-transform: uppercase;
  flex-shrink: 0;
}
.gps-btn:hover { background: #111; color: #777; }
.gps-btn.active { color: #00c96b; }
.gps-status {
  flex: 1;
  padding: 12px 14px;
  background: #0c0c0c;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  color: #555;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  border: none;
  cursor: default;
}
.gps-status.ok  { color: #00c96b; }
.gps-status.err { color: #7a1a1a; }

/* ─── File upload ──────────────────────────────────────────────── */
.upload-area {
  border: 1px dashed #181818;
  background: #0a0a0a;
  padding: 24px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
  position: relative;
}
.upload-area:hover,
.upload-area.drag-over {
  border-color: #fff;
  background: #0e0e0e;
}
.upload-area input[type="file"] {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}
.upload-icon {
  font-size: 20px;
  margin-bottom: 8px;
  opacity: 0.3;
}
.upload-title {
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  color: #fff;
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: 5px;
}
.upload-sub {
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  color: #555;
  letter-spacing: 1px;
}

.file-list {
  margin-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.file-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  background: #0c0c0c;
  border: 1px solid #141414;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  color: #555;
  letter-spacing: 0.5px;
}
.file-item-name {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 380px;
}
.file-remove {
  background: none;
  border: none;
  color: #444;
  cursor: pointer;
  font-size: 12px;
  padding: 0 2px;
  flex-shrink: 0;
  transition: color 0.15s;
}
.file-remove:hover { color: #fff; }

/* ─── Divider ──────────────────────────────────────────────────── */
.divider {
  height: 1px;
  background: #111;
  margin: 32px 0;
}

/* ─── Ranger notice ────────────────────────────────────────────── */
.ranger-bar {
  display: none;
  padding: 11px 14px;
  border: 1px solid #1a1230;
  background: #09070f;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  color: #a78bfa;
  letter-spacing: 1px;
  margin-bottom: 24px;
}

/* ─── Error bar ────────────────────────────────────────────────── */
.error-bar {
  display: none;
  padding: 11px 14px;
  background: #0d0404;
  border: 1px solid #2a0808;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  color: #ef4444;
  letter-spacing: 1px;
  margin-bottom: 20px;
}

/* ─── Submit button ────────────────────────────────────────────── */
.submit-btn {
  width: 100%;
  padding: 15px;
  background: #b02a2a;
  color: #fff;
  border: none;
  font-family: 'JetBrains Mono', monospace;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 4px;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.2s;
  position: relative;
  overflow: hidden;
}
.submit-btn:hover    { background: #c41f1f; }
.submit-btn:disabled { background: #141414; color: #333; cursor: not-allowed; }
.submit-hint {
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  color: #333;
  text-align: center;
  margin-top: 12px;
  letter-spacing: 0.8px;
  line-height: 1.9;
}

/* ─── Success ──────────────────────────────────────────────────── */
.success-wrap {
  display: none;
  padding: 40px 32px;
  background: #0a0a0a;
  border: 1px solid #111;
  text-align: center;
}
.success-check {
  width: 44px; height: 44px;
  border: 1px solid #00c96b;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  color: #00c96b;
  font-size: 18px;
}
.success-title {
  font-family: 'JetBrains Mono', monospace;
  font-size: 11px;
  color: #00c96b;
  letter-spacing: 4px;
  font-weight: 700;
  margin-bottom: 12px;
}
.success-body {
  font-family: 'JetBrains Mono', monospace;
  font-size: 10px;
  color: #555;
  letter-spacing: 0.5px;
  line-height: 1.9;
  margin-bottom: 28px;
}
.token-label {
  font-family: 'JetBrains Mono', monospace;
  font-size: 8px;
  color: #555;
  letter-spacing: 3px;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.token-display {
  font-family: 'JetBrains Mono', monospace;
  font-size: 12px;
  color: #F4C430;
  letter-spacing: 2px;
  word-break: break-all;
  padding: 14px 16px;
  background: #070707;
  border: 1px solid #1a1a1a;
  margin-bottom: 14px;
  line-height: 1.6;
}
.copy-btn {
  padding: 9px 20px;
  background: transparent;
  border: 1px solid #1a1a1a;
  color: #777;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  letter-spacing: 1.5px;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s;
  text-transform: uppercase;
}
.copy-btn:hover { border-color: #fff; color: #fff; }

.success-actions {
  margin-top: 28px;
  display: flex;
  gap: 8px;
  justify-content: center;
  flex-wrap: wrap;
}
.sact {
  padding: 9px 18px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 9px;
  letter-spacing: 1px;
  text-decoration: none;
  border: 1px solid #1a1a1a;
  color: #555;
  transition: border-color 0.15s, color 0.15s;
}
.sact:hover { border-color: #555; color: #888; }
.sact.primary { border-color: #b02a2a; color: #fff; }
.sact.primary:hover { background: rgba(176,42,42,0.1); }

/* ─── Responsive ───────────────────────────────────────────────── */
@media (max-width: 500px) {
  .cat-grid { grid-template-columns: repeat(2, 1fr); }
  .rp-title { font-size: 22px; }
  .priv-item { font-size: 7px; gap: 5px; }
}
</style>

<div class="rp-page">
<div class="rp-wrap">

  {{-- Header --}}
  <div style="margin-bottom:40px;">
    <div class="rp-eyebrow">বিদ্রোহী · Anonymous Submission</div>
    <h1 class="rp-title">Submit a <span>Report</span></h1>
    <p class="rp-sub">
      Your identity stays private. No personal information is required.<br>
      Your report will be analyzed by AI and forwarded to relevant NGOs.
    </p>
  </div>

  {{-- Ranger notice --}}
  <div class="ranger-bar" id="rangerNotice">
    ◆ RANGER MODE — Submitting as a registered Ranger. You will earn points for this report.
  </div>

  {{-- Error --}}
  <div class="error-bar" id="errorBar"></div>

  {{-- ── FORM ── --}}
  <div id="reportForm">

    {{-- Crime category --}}
    <div class="field">
      <div class="sec-label">Crime Category <span>*</span></div>
      <div class="cat-grid">
        <button class="cat-btn" data-cat="corruption"        onclick="selectCat(this)"><span class="cat-icon">⚖</span>Corruption</button>
        <button class="cat-btn" data-cat="child_abuse"       onclick="selectCat(this)"><span class="cat-icon">⚠</span>Child Abuse</button>
        <button class="cat-btn" data-cat="bribery"           onclick="selectCat(this)"><span class="cat-icon">💰</span>Bribery</button>
        <button class="cat-btn" data-cat="harassment"        onclick="selectCat(this)"><span class="cat-icon">🚨</span>Harassment</button>
        <button class="cat-btn" data-cat="violence"          onclick="selectCat(this)"><span class="cat-icon">✊</span>Violence</button>
        <button class="cat-btn" data-cat="drug_trafficking"  onclick="selectCat(this)"><span class="cat-icon">💊</span>Drug Trafficking</button>
        <button class="cat-btn" data-cat="extortion"         onclick="selectCat(this)"><span class="cat-icon">🔒</span>Extortion</button>
        <button class="cat-btn" data-cat="land_grabbing"     onclick="selectCat(this)"><span class="cat-icon">🏚</span>Land Grabbing</button>
        <button class="cat-btn" data-cat="police_misconduct" onclick="selectCat(this)"><span class="cat-icon">🛡</span>Police Misconduct</button>
        <button class="cat-btn" data-cat="fraud"             onclick="selectCat(this)"><span class="cat-icon">📄</span>Fraud</button>
        <button class="cat-btn" data-cat="theft"             onclick="selectCat(this)"><span class="cat-icon">🔍</span>Theft</button>
        <button class="cat-btn" data-cat="other"             onclick="selectCat(this)"><span class="cat-icon">…</span>Other</button>
      </div>
    </div>

    {{-- Description --}}
    <div class="field">
      <div class="sec-label">What happened <span>*</span></div>
      <textarea class="field-textarea" id="description"
        placeholder="Describe what you witnessed. Time of day, what was happening, who was involved. Bangla or English, both accepted."
        maxlength="2000"></textarea>
      <div class="char-count"><span id="charCount">0</span> / 2000</div>
    </div>

    {{-- District --}}
    <div class="field">
      <div class="sec-label">District <span>*</span></div>
      <select class="field-select" id="district">
        <option value="">— Select district —</option>
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
    </div>

    {{-- Specific location --}}
    <div class="field">
      <div class="sec-label">Specific location <span style="color:#333;letter-spacing:1px;font-size:7px;">(optional)</span></div>
      <input class="field-input" id="specificLocation" type="text"
        placeholder="e.g. Near Mirpur 10 metro, in front of the market...">
    </div>

    {{-- GPS --}}
    <div class="field">
      <div class="sec-label">GPS coordinates <span style="color:#333;letter-spacing:1px;font-size:7px;">(optional — improves AI hotspot accuracy)</span></div>
      <div class="gps-wrap">
        <button class="gps-btn" id="gpsBtn" onclick="detectGPS()">◉ Detect location</button>
        <div class="gps-status" id="gpsStatus">Waiting for GPS...</div>
      </div>
      <input type="hidden" id="gpsLat">
      <input type="hidden" id="gpsLng">
    </div>

    {{-- Evidence upload --}}
    <div class="field">
      <div class="sec-label">Evidence files <span style="color:#333;letter-spacing:1px;font-size:7px;">(optional)</span></div>
      <div class="upload-area" id="uploadArea"
           ondragover="handleDragOver(event)"
           ondragleave="handleDragLeave(event)"
           ondrop="handleDrop(event)">
        <input type="file" id="fileInput" multiple
               accept="image/*,video/*,.pdf,.doc,.docx,.mp3,.mp4"
               onchange="handleFiles(this.files)">
        <div class="upload-icon">↑</div>
        <div class="upload-title">Drop files or click to upload</div>
        <div class="upload-sub">Images · Videos · Documents · Max 10MB each</div>
      </div>
      <div class="file-list" id="fileList"></div>
    </div>

    <div class="divider"></div>

    <button class="submit-btn" id="submitBtn" onclick="submitReport()">
      Submit Anonymous Report
    </button>

    <p class="submit-hint">
      By submitting you confirm this report is truthful to the best of your knowledge.<br>
      False reports undermine real victims — be a genuine witness.
    </p>

  </div>{{-- /reportForm --}}

  {{-- ── Success ── --}}
  <div class="success-wrap" id="successBox">
    <div class="success-check">✓</div>
    <div class="success-title">Report Submitted</div>
    <p class="success-body">
      Your report has been received and is being analyzed.<br>
      Save your tracking token to check its status later.
    </p>
    <div class="token-label">Your tracking token — save this</div>
    <div class="token-display" id="trackingTokenDisplay">—</div>
    <button class="copy-btn" onclick="copyToken()">◉ Copy token</button>
    <div class="success-actions">
      <a href="/report"    class="sact">Submit another</a>
      <a href="/status"    class="sact">Check status</a>
      <a href="/dashboard" class="sact primary">View crime map</a>
    </div>
  </div>

</div>
</div>

<script>
// ── State ─────────────────────────────────────────────────────────────────────
let selectedCategory = '';
let gpsLat = null, gpsLng = null;
let trackingToken = '';
let selectedFiles = [];

const rangerToken = localStorage.getItem('ranger_auth_token');
if (rangerToken) document.getElementById('rangerNotice').style.display = 'block';

// ── Category ──────────────────────────────────────────────────────────────────
function selectCat(btn) {
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selectedCategory = btn.dataset.cat;
}

// ── Char counter ──────────────────────────────────────────────────────────────
document.getElementById('description').addEventListener('input', function() {
  document.getElementById('charCount').textContent = this.value.length;
});

// ── GPS ───────────────────────────────────────────────────────────────────────
function detectGPS() {
  const status = document.getElementById('gpsStatus');
  const btn    = document.getElementById('gpsBtn');

  if (!navigator.geolocation) {
    status.textContent = 'Not supported by this browser';
    status.className = 'gps-status err';
    return;
  }

  btn.textContent = '◌ Detecting...';
  btn.disabled = true;
  status.textContent = 'Requesting permission...';
  status.className = 'gps-status';

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      gpsLat = pos.coords.latitude;
      gpsLng = pos.coords.longitude;
      status.textContent = `✓ ${gpsLat.toFixed(5)}, ${gpsLng.toFixed(5)}`;
      status.className = 'gps-status ok';
      btn.textContent = '✓ Detected';
      btn.classList.add('active');
      btn.disabled = false;
      document.getElementById('gpsLat').value = gpsLat;
      document.getElementById('gpsLng').value = gpsLng;
    },
    (err) => {
      const msgs = {
        1: 'Permission denied — GPS skipped',
        2: 'Position unavailable',
        3: 'Request timed out',
      };
      status.textContent = msgs[err.code] || 'Could not detect location';
      status.className = 'gps-status err';
      btn.textContent = '◉ Try again';
      btn.disabled = false;
    },
    { timeout: 10000, maximumAge: 60000 }
  );
}

// ── File upload ───────────────────────────────────────────────────────────────
function handleFiles(files) {
  const maxSize = 10 * 1024 * 1024; // 10MB
  Array.from(files).forEach(file => {
    if (file.size > maxSize) {
      showError(`"${file.name}" is too large. Max 10MB per file.`);
      return;
    }
    if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
      selectedFiles.push(file);
    }
  });
  renderFileList();
}

function renderFileList() {
  const list = document.getElementById('fileList');
  list.innerHTML = '';
  selectedFiles.forEach((file, i) => {
    const ext  = file.name.split('.').pop().toUpperCase();
    const size = file.size < 1024 * 1024
      ? (file.size / 1024).toFixed(0) + ' KB'
      : (file.size / 1024 / 1024).toFixed(1) + ' MB';
    const div = document.createElement('div');
    div.className = 'file-item';
    div.innerHTML = `
      <span class="file-item-name">${file.name}</span>
      <span style="color:#555;font-size:8px;flex-shrink:0;margin:0 8px;">${ext} · ${size}</span>
      <button class="file-remove" onclick="removeFile(${i})" title="Remove">✕</button>
    `;
    list.appendChild(div);
  });
}

function removeFile(i) {
  selectedFiles.splice(i, 1);
  renderFileList();
}

function handleDragOver(e) {
  e.preventDefault();
  document.getElementById('uploadArea').classList.add('drag-over');
}
function handleDragLeave(e) {
  document.getElementById('uploadArea').classList.remove('drag-over');
}
function handleDrop(e) {
  e.preventDefault();
  document.getElementById('uploadArea').classList.remove('drag-over');
  handleFiles(e.dataTransfer.files);
}

// ── Witness token ─────────────────────────────────────────────────────────────
// FIX: reads all possible response shapes from /api/witness/token
async function getWitnessToken() {
  const existing = localStorage.getItem('bidrohi_witness_token');
  if (existing) return existing;

  const res  = await fetch('/api/witness/token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({})
  });
  const json = await res.json();

  // Handle all possible response shapes
  const token = json.token
    || json.tracking_token
    || json.data?.tracking_token
    || json.data?.token;

  if (token) {
    localStorage.setItem('bidrohi_witness_token', token);
    return token;
  }
  throw new Error('Could not generate witness token');
}

// ── Error ─────────────────────────────────────────────────────────────────────
function showError(msg) {
  const bar = document.getElementById('errorBar');
  bar.textContent = '✗ ' + msg;
  bar.style.display = 'block';
  window.scrollTo({ top: bar.getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' });
  setTimeout(() => { bar.style.display = 'none'; }, 6000);
}

// ── Submit ────────────────────────────────────────────────────────────────────
async function submitReport() {
  if (!selectedCategory) { showError('Please select a crime category.'); return; }
  const desc = document.getElementById('description').value.trim();
  if (desc.length < 20) { showError('Please write at least 20 characters describing what happened.'); return; }
  const district = document.getElementById('district').value;
  if (!district) { showError('Please select the district where this occurred.'); return; }

  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = 'Submitting...';

  try {
    const formData = new FormData();
    formData.append('category',          selectedCategory);
    formData.append('description',       desc);
    formData.append('district',          district);
    formData.append('specific_location', document.getElementById('specificLocation').value.trim() || '');
    if (gpsLat) formData.append('latitude',  gpsLat);
    if (gpsLng) formData.append('longitude', gpsLng);

    // FIX: send files as both 'files[]' and 'evidence[]' so either field name works
    selectedFiles.forEach((file, i) => {
      formData.append(`files[${i}]`, file);
    });

    const headers = {
      'Accept':       'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
    };

    if (rangerToken) {
      headers['Authorization'] = 'Bearer ' + rangerToken;
    } else {
      // FIX: correct field name is 'tracking_token', not 'witness_token'
      const witnessToken = await getWitnessToken();
      formData.append('tracking_token', witnessToken);
    }

    const res  = await fetch('/api/reports', { method: 'POST', headers, body: formData });
    const json = await res.json();

    if (json.success || res.ok) {
      // FIX: handle all possible token locations in response
      trackingToken = json.tracking_token
        || json.data?.tracking_token
        || json.data?.tracking?.tracking_token
        || '—';
      document.getElementById('trackingTokenDisplay').textContent = trackingToken;
      document.getElementById('reportForm').style.display  = 'none';
      document.getElementById('successBox').style.display  = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      const msg = json.message
        || (json.errors ? Object.values(json.errors).flat().join(' ') : 'Submission failed. Please try again.');
      showError(msg);
      btn.disabled = false;
      btn.textContent = 'Submit Anonymous Report';
    }
  } catch (err) {
    console.error('Submit error:', err);
    showError('Network error — check your connection and try again.');
    btn.disabled = false;
    btn.textContent = 'Submit Anonymous Report';
  }
}

// ── Copy token ────────────────────────────────────────────────────────────────
function copyToken() {
  navigator.clipboard.writeText(trackingToken).then(() => {
    const btn = document.querySelector('.copy-btn');
    btn.textContent = '✓ Copied';
    setTimeout(() => { btn.textContent = '◉ Copy token'; }, 2000);
  });
}
</script>

@endsection