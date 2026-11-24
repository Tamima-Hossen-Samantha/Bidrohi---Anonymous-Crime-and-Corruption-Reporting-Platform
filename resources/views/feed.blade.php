
@extends('layouts.app')
@section('title', 'Intel Feed — BIDROHI')
@section('content')
<style>
.feed-page{min-height:100vh;background:#060606;padding:60px 16px 80px}
.feed-wrap{max-width:680px;margin:0 auto}
.feed-header{margin-bottom:32px}
.feed-eyebrow{font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:3px;color:#DC2626;text-transform:uppercase;margin-bottom:10px;display:flex;align-items:center;gap:10px}
.live-dot{width:7px;height:7px;border-radius:50%;background:#DC2626;animation:blink 1.4s infinite}
.feed-title{font-family:'JetBrains Mono',monospace;font-size:26px;color:#e8e8e8;font-weight:700;margin-bottom:8px}
.feed-title em{color:#DC2626;font-style:normal}
.feed-sub{font-family:'JetBrains Mono',monospace;font-size:9px;color:#fff;letter-spacing:1px;line-height:1.8}

/* Category filter */
.cat-filter{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:24px}
.cat-pill{padding:5px 12px;background:transparent;border:1px solid #1a1a1a;color:#fff;font-family:'JetBrains Mono',monospace;font-size:8px;letter-spacing:1px;cursor:pointer;transition:all .2s;text-transform:uppercase}
.cat-pill:hover{border-color:#333;color:#888}
.cat-pill.active{border-color:#DC2626;color:#DC2626;background:rgba(220,38,38,.06)}

/* Report card */
.r-card{padding:20px 0;border-bottom:1px solid #0f0f0f}
.r-card:last-child{border-bottom:none}
.r-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;gap:10px}
.r-badges{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.sev-badge{font-family:'JetBrains Mono',monospace;font-size:7px;font-weight:700;color:#000;padding:2px 6px;letter-spacing:1px;flex-shrink:0}
.r-cat-label{font-family:'JetBrains Mono',monospace;font-size:8px;color:#DC2626;letter-spacing:1px;text-transform:uppercase}
.r-time{font-family:'JetBrains Mono',monospace;font-size:8px;color:#333;white-space:nowrap;flex-shrink:0}
.r-desc{font-family:'JetBrains Mono',monospace;font-size:10px;color:#888;line-height:1.7;margin-bottom:10px}
.r-bottom{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px}
.r-meta{display:flex;gap:14px;align-items:center}
.r-district{font-family:'JetBrains Mono',monospace;font-size:8px;color:#F4C430;letter-spacing:.5px}
.r-reporter{font-family:'JetBrains Mono',monospace;font-size:8px;color:#333}
.r-status{font-family:'JetBrains Mono',monospace;font-size:7px;letter-spacing:1px;text-transform:uppercase}
.r-evidence{font-family:'JetBrains Mono',monospace;font-size:7px;color:#0891B2;letter-spacing:.5px}

/* Loading / empty */
.loading-state{text-align:center;padding:60px 20px;font-family:'JetBrains Mono',monospace;font-size:10px;color:#333;letter-spacing:2px}

/* Load more */
.load-more{width:100%;padding:12px;background:transparent;border:1px solid #1a1a1a;color:#555;font-family:'JetBrains Mono',monospace;font-size:9px;letter-spacing:2px;cursor:pointer;margin-top:24px;transition:all .2s;display:none}
.load-more:hover{border-color:#333;color:#888}

@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
</style>

<div class="feed-page"><div class="feed-wrap">

  <div class="feed-header">
    <div class="feed-eyebrow">
      <span>Crime FEED</span>
      <div class="live-dot"></div>
      <span style="color:#333">LIVE</span>
    </div>
    <h1 class="feed-title">Anonymous <em>Intelligence</em></h1>
    <p class="feed-sub">
      Real reports. Zero identity. Privacy-safe coordinates.<br>
      This is Bangladesh's public anonymous crime intelligence feed.
    </p>
  </div>

  {{-- Category filter --}}
  <div class="cat-filter">
    <button class="cat-pill active" data-cat="" onclick="filterCat(this)">ALL</button>
    <button class="cat-pill" data-cat="corruption"       onclick="filterCat(this)">CORRUPTION</button>
    <button class="cat-pill" data-cat="child_abuse"      onclick="filterCat(this)">CHILD ABUSE</button>
    <button class="cat-pill" data-cat="bribery"          onclick="filterCat(this)">BRIBERY</button>
    <button class="cat-pill" data-cat="harassment"       onclick="filterCat(this)">HARASSMENT</button>
    <button class="cat-pill" data-cat="violence"         onclick="filterCat(this)">VIOLENCE</button>
    <button class="cat-pill" data-cat="drug_trafficking" onclick="filterCat(this)">DRUGS</button>
    <button class="cat-pill" data-cat="extortion"        onclick="filterCat(this)">EXTORTION</button>
    <button class="cat-pill" data-cat="land_grabbing"    onclick="filterCat(this)">LAND GRAB</button>
  </div>

  {{-- Feed list --}}
  <div id="feedList">
    <div class="loading-state" id="loadingState">LOADING INTEL FEED...</div>
  </div>

  <button class="load-more" id="loadMoreBtn" onclick="loadMore()">LOAD MORE REPORTS</button>

</div></div>

<script>
const SEV_COLORS = {CRITICAL:'#FF0000',HIGH:'#FF6B00',MEDIUM:'#FFD700',LOW:'#00FF88'};
const STS_COLORS = {pending:'#555',investigating:'#F4C430',verified:'#00FF88',resolved:'#8B5CF6',rejected:'#DC2626'};

let currentPage = 1;
let currentCat  = '';
let hasMore     = true;
let loading     = false;

async function loadFeed(page, cat, reset) {
  if (loading) return;
  loading = true;

  let url = `/api/feed?page=${page}`;
  if (cat) url += `&category=${cat}`;

  const res  = await fetch(url);
  const json = await res.json();
  loading    = false;

  if (!json.success) return;

  const list = document.getElementById('feedList');
  document.getElementById('loadingState')?.remove();

  if (reset) list.innerHTML = '';

  (json.data || []).forEach(r => {
    const sev   = r.severity_label || 'LOW';
    const sevC  = SEV_COLORS[sev] || '#555';
    const stsC  = STS_COLORS[r.status] || '#555';
    const card  = document.createElement('div');
    card.className = 'r-card';
    card.innerHTML = `
      <div class="r-top">
        <div class="r-badges">
          ${r.severity_label ? `<span class="sev-badge" style="background:${sevC}">${sev}</span>` : ''}
          <span class="r-cat-label">${(r.ai_category||r.category||'').replace(/_/g,' ')}</span>
        </div>
        <span class="r-time">${r.time_ago||''}</span>
      </div>
      <div class="r-desc">${r.description||''}</div>
      <div class="r-bottom">
        <div class="r-meta">
          <span class="r-district">◈ ${r.district||'Unknown'}</span>
          <span class="r-reporter">${r.reporter||'Anonymous Witness'}</span>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
          ${r.has_evidence ? '<span class="r-evidence">◉ EVIDENCE</span>' : ''}
          <span class="r-status" style="color:${stsC}">${(r.status_label||'').toUpperCase()}</span>
        </div>
      </div>`;
    list.appendChild(card);
  });

  hasMore = json.has_more;
  currentPage = json.current_page;
  document.getElementById('loadMoreBtn').style.display = hasMore ? 'block' : 'none';

  if ((json.data||[]).length === 0 && reset) {
    list.innerHTML = '<div class="loading-state">NO REPORTS IN THIS CATEGORY YET</div>';
  }
}

function filterCat(btn) {
  document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  currentCat  = btn.dataset.cat;
  currentPage = 1;
  hasMore     = true;
  document.getElementById('feedList').innerHTML = '<div class="loading-state" id="loadingState">LOADING...</div>';
  loadFeed(1, currentCat, true);
}

function loadMore() {
  if (hasMore) loadFeed(currentPage + 1, currentCat, false);
}

// Initial load
loadFeed(1, '', true);
</script>
@endsection