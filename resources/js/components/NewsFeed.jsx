/**
 * NewsFeed.jsx
 *
 * This is the right-side sliding panel on the intelligence dashboard.
 * It shows a scrollable feed of reports — like a social media timeline
 * but for crime intelligence. Fully privacy-safe (no identities shown).
 *
 * Features:
 * - Infinite scroll (loads more as you scroll down)
 * - Category filter tabs at the top
 * - Collapses to a thin strip when you click the arrow
 * - Can highlight newly arrived reports (from Pusher real-time)
 *
 * Usage in Dashboard.jsx:
 *   import NewsFeed from './NewsFeed';
 *   // Place <NewsFeed /> anywhere in your dashboard JSX
 *   // It will position itself fixed to the right side of the screen
 */

import { useState, useEffect, useRef, useCallback } from "react";

// Category filter options shown at the top of the feed
const CATEGORIES = [
  { value: "",                label: "ALL" },
  { value: "corruption",      label: "CORRUPTION" },
  { value: "child_abuse",     label: "CHILD ABUSE" },
  { value: "bribery",         label: "BRIBERY" },
  { value: "fraud",           label: "FRAUD" },
  { value: "harassment",      label: "HARASSMENT" },
  { value: "violence",        label: "VIOLENCE" },
  { value: "drug_trafficking",label: "DRUGS" },
  { value: "extortion",       label: "EXTORTION" },
  { value: "land_grabbing",   label: "LAND GRAB" },
];

// Colors for each severity level
const SEVERITY_COLORS = {
  CRITICAL: "#FF0000",
  HIGH:     "#FF6B00",
  MEDIUM:   "#FFD700",
  LOW:      "#00FF88",
};

// Colors for each report status
const STATUS_COLORS = {
  pending:      "#555",
  investigating:"#F4C430",
  verified:     "#00FF88",
  resolved:     "#8B5CF6",
  rejected:     "#DC2626",
};

/**
 * A single report card in the feed.
 * isNew=true gives it a red-tinted background briefly (for Pusher highlights).
 */
function FeedCard({ report, isNew }) {
  return (
    <div
      style={{
        borderBottom: "1px solid #0f0f0f",
        padding: "12px 14px",
        background: isNew ? "rgba(220,38,38,0.06)" : "transparent",
        transition: "background 1.5s ease",
        animation: isNew ? "slideIn 0.3s ease" : "none",
      }}
    >
      {/* ── Top Row: Severity badge + Category + Time ago ── */}
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "6px" }}>
        <div style={{ display: "flex", gap: "6px", alignItems: "center" }}>

          {/* Severity label (CRITICAL / HIGH / MEDIUM / LOW) */}
          {report.severity_label && (
            <span style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "7px",
              fontWeight: "700",
              color: "#000",
              background: SEVERITY_COLORS[report.severity_label] || "#555",
              padding: "1px 5px",
              letterSpacing: "1px",
            }}>
              {report.severity_label}
            </span>
          )}

          {/* Crime category */}
          <span style={{
            fontFamily: "'IBM Plex Mono', monospace",
            fontSize: "8px",
            color: "#DC2626",
            letterSpacing: "1px",
            textTransform: "uppercase",
          }}>
            {report.ai_category || report.category}
          </span>
        </div>

        {/* Time ago: "3 hours ago" */}
        <span style={{
          fontFamily: "'IBM Plex Mono', monospace",
          fontSize: "8px",
          color: "#333",
        }}>
          {report.time_ago}
        </span>
      </div>

      {/* ── Description preview (max 200 chars) ── */}
      <div style={{
        fontFamily: "'IBM Plex Mono', monospace",
        fontSize: "10px",
        color: "#888",
        lineHeight: "1.6",
        marginBottom: "8px",
      }}>
        {report.description}
      </div>

      {/* ── Bottom Row: District + Reporter + Evidence + Status ── */}
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>

        <div style={{ display: "flex", gap: "10px", alignItems: "center" }}>
          {/* District name in gold */}
          <span style={{
            fontFamily: "'IBM Plex Mono', monospace",
            fontSize: "8px",
            color: "#F4C430",
            letterSpacing: "0.5px",
          }}>
            ◈ {report.district || "Unknown"}
          </span>

          {/* Reporter: "Anonymous Witness" or "@username" */}
          <span style={{
            fontFamily: "'IBM Plex Mono', monospace",
            fontSize: "8px",
            color: "#444",
          }}>
            {report.reporter}
          </span>
        </div>

        <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
          {/* Blue EVIDENCE tag if files were attached */}
          {report.has_evidence && (
            <span style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "7px",
              color: "#0891B2",
              letterSpacing: "0.5px",
            }}>
              ◉ EVIDENCE
            </span>
          )}

          {/* Status label */}
          <span style={{
            fontFamily: "'IBM Plex Mono', monospace",
            fontSize: "7px",
            color: STATUS_COLORS[report.status] || "#555",
            letterSpacing: "1px",
            textTransform: "uppercase",
          }}>
            {report.status_label}
          </span>
        </div>

      </div>
    </div>
  );
}

/**
 * The main NewsFeed component.
 * Props:
 *   newReportIds — array of report IDs that just arrived via Pusher (to highlight briefly)
 */
export default function NewsFeed({ newReportIds = [] }) {
  const [reports, setReports]     = useState([]);   // all loaded reports
  const [page, setPage]           = useState(1);    // current page number
  const [hasMore, setHasMore]     = useState(true); // is there a next page?
  const [loading, setLoading]     = useState(false); // currently fetching?
  const [category, setCategory]   = useState("");   // active category filter
  const [collapsed, setCollapsed] = useState(false); // is the panel collapsed?
  const [highlightIds, setHighlightIds] = useState(new Set()); // IDs to highlight

  const sentinelRef = useRef(null); // invisible div at bottom of list for infinite scroll
  const observerRef = useRef(null); // IntersectionObserver instance

  /**
   * Fetch a page of reports from /api/feed
   *
   * pageNum — which page to fetch
   * cat     — which category to filter by ("" means all)
   * reset   — if true, clear existing reports and start fresh
   */
  const fetchFeed = useCallback(async (pageNum, cat, reset = false) => {
    if (loading) return;
    setLoading(true);
    try {
      let url = `/api/feed?page=${pageNum}`;
      if (cat) url += `&category=${cat}`;

      const res  = await fetch(url);
      const json = await res.json();

      if (json.success) {
        const items = json.data || [];
        // If reset, replace the list. Otherwise append to it.
        setReports((prev) => reset ? items : [...prev, ...items]);
        setHasMore(json.has_more);
        setPage(pageNum);
      }
    } catch (e) {
      // Network error — silently fail
    } finally {
      setLoading(false);
    }
  }, []); // Note: no loading dependency here to avoid stale closure issue

  // Load page 1 when the component first mounts
  useEffect(() => {
    fetchFeed(1, "", true);
  }, []);

  // When admin clicks a category filter tab
  const handleCategoryChange = (cat) => {
    setCategory(cat);
    setReports([]);   // clear current list
    setPage(1);
    setHasMore(true);
    fetchFeed(1, cat, true);
  };

  // Set up IntersectionObserver to detect when user scrolls to bottom
  // When the sentinel div becomes visible, load the next page
  useEffect(() => {
    if (!sentinelRef.current) return;

    observerRef.current = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting && hasMore && !loading) {
          fetchFeed(page + 1, category, false);
        }
      },
      { threshold: 0.1 }
    );

    observerRef.current.observe(sentinelRef.current);

    return () => {
      observerRef.current?.disconnect();
    };
  }, [hasMore, loading, page, category]);

  // When Pusher sends new report IDs, highlight them briefly and refresh top
  useEffect(() => {
    if (!newReportIds || newReportIds.length === 0) return;
    setHighlightIds(new Set(newReportIds));
    fetchFeed(1, category, true); // refresh feed to show new report at top
    setTimeout(() => setHighlightIds(new Set()), 3000); // remove highlight after 3s
  }, [newReportIds]);

  // ── RENDER ──────────────────────────────────────────────────────────────────

  return (
    <div
      style={{
        position: "absolute",
        top: "38px",          // below the top bar
        right: "0",
        width: collapsed ? "36px" : "300px",
        height: "calc(100vh - 38px)",
        background: "rgba(4,4,4,0.96)",
        borderLeft: "1px solid #111",
        display: "flex",
        flexDirection: "column",
        transition: "width 0.25s ease",
        zIndex: 900,
        overflow: "hidden",
      }}
    >
      {/* ── Collapse/Expand Toggle Button ── */}
      <button
        onClick={() => setCollapsed(!collapsed)}
        title={collapsed ? "Expand feed" : "Collapse feed"}
        style={{
          position: "absolute",
          left: collapsed ? "5px" : "-14px",
          top: "50%",
          transform: "translateY(-50%)",
          background: "#0d0d0d",
          border: "1px solid #1a1a1a",
          color: "#555",
          width: "28px",
          height: "28px",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          cursor: "pointer",
          fontFamily: "'IBM Plex Mono', monospace",
          fontSize: "10px",
          zIndex: 10,
          transition: "left 0.25s ease",
        }}
      >
        {collapsed ? "◁" : "▷"}
      </button>

      {/* ── Panel Content (hidden when collapsed) ── */}
      {!collapsed && (
        <>
          {/* ── Header ── */}
          <div style={{
            padding: "10px 14px",
            borderBottom: "1px solid #111",
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            flexShrink: 0,
          }}>
            <div>
              <div style={{
                fontFamily: "'IBM Plex Mono', monospace",
                fontSize: "10px",
                color: "#DC2626",
                letterSpacing: "2.5px",
                fontWeight: "700",
              }}>
                INTEL FEED
              </div>
              <div style={{
                fontFamily: "'IBM Plex Mono', monospace",
                fontSize: "7px",
                color: "#2a2a2a",
                letterSpacing: "1px",
                marginTop: "1px",
              }}>
                LIVE · ANONYMOUS · ENCRYPTED
              </div>
            </div>

            {/* Red blinking dot — indicates live */}
            <div style={{
              width: "6px",
              height: "6px",
              borderRadius: "50%",
              background: "#DC2626",
              animation: "blink 1.5s infinite",
            }} />
          </div>

          {/* ── Category Filter Tabs ── */}
          <div style={{
            display: "flex",
            overflowX: "auto",
            padding: "6px 14px",
            gap: "4px",
            borderBottom: "1px solid #111",
            flexShrink: 0,
            scrollbarWidth: "none", // hide scrollbar on the tab row
          }}>
            {CATEGORIES.map((cat) => (
              <button
                key={cat.value}
                onClick={() => handleCategoryChange(cat.value)}
                style={{
                  fontFamily: "'IBM Plex Mono', monospace",
                  fontSize: "7px",
                  letterSpacing: "1px",
                  whiteSpace: "nowrap",
                  padding: "3px 7px",
                  background: category === cat.value ? "#DC2626" : "transparent",
                  color:      category === cat.value ? "#fff"    : "#444",
                  border: `1px solid ${category === cat.value ? "#DC2626" : "#1a1a1a"}`,
                  cursor: "pointer",
                  transition: "all 0.2s",
                }}
              >
                {cat.label}
              </button>
            ))}
          </div>

          {/* ── Scrollable Report List ── */}
          <div style={{
            flex: 1,
            overflowY: "auto",
            scrollbarWidth: "thin",
            scrollbarColor: "#1a1a1a transparent",
          }}>

            {/* Empty state */}
            {reports.length === 0 && !loading && (
              <div style={{
                fontFamily: "'IBM Plex Mono', monospace",
                fontSize: "10px",
                color: "#222",
                textAlign: "center",
                padding: "50px 20px",
                letterSpacing: "1.5px",
              }}>
                NO REPORTS YET
              </div>
            )}

            {/* Report cards */}
            {reports.map((report) => (
              <FeedCard
                key={report.id}
                report={report}
                isNew={highlightIds.has(report.id)}
              />
            ))}

            {/* Invisible sentinel div — triggers next page load when visible */}
            <div ref={sentinelRef} style={{ height: "1px" }} />

            {/* Loading indicator */}
            {loading && (
              <div style={{
                fontFamily: "'IBM Plex Mono', monospace",
                fontSize: "8px",
                color: "#2a2a2a",
                textAlign: "center",
                padding: "16px",
                letterSpacing: "2px",
              }}>
                LOADING...
              </div>
            )}

            {/* End of feed indicator */}
            {!hasMore && reports.length > 0 && (
              <div style={{
                fontFamily: "'IBM Plex Mono', monospace",
                fontSize: "7px",
                color: "#1a1a1a",
                textAlign: "center",
                padding: "16px",
                letterSpacing: "2px",
              }}>
                ── END OF INTEL FEED ──
              </div>
            )}

          </div>

          {/* ── Footer ── */}
          <div style={{
            padding: "7px 14px",
            borderTop: "1px solid #111",
            flexShrink: 0,
          }}>
            <div style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "7px",
              color: "#1a1a1a",
              letterSpacing: "1px",
            }}>
              BIDROHI INTELLIGENCE NETWORK · বিদ্রোহী
            </div>
          </div>
        </>
      )}

      {/* CSS animations */}
      <style>{`
        @keyframes blink {
          0%, 100% { opacity: 1; }
          50%       { opacity: 0.15; }
        }
        @keyframes slideIn {
          from { transform: translateX(15px); opacity: 0; }
          to   { transform: translateX(0);    opacity: 1; }
        }
      `}</style>
    </div>
  );
}