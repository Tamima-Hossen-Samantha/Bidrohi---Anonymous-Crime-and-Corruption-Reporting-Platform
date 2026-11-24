/**
 * StatsBar.jsx
 *
 * This component sits in the top bar of the intelligence dashboard.
 * It fetches live statistics from GET /api/stats every 30 seconds
 * and displays them as glowing monospace counters.
 *
 * Usage in your Dashboard.jsx:
 *   import StatsBar from './StatsBar';
 *   // Then place <StatsBar /> inside your top bar div
 */

import { useState, useEffect } from "react";

// Format a number for display: 1000 → 1.0K, 42 → 0042
const formatNumber = (n) => {
  if (n === null || n === undefined) return "----";
  if (n >= 1000) return (n / 1000).toFixed(1) + "K";
  return String(n).padStart(4, "0");
};

export default function StatsBar() {
  // stats holds the data from /api/stats
  const [stats, setStats]   = useState(null);
  const [loading, setLoading] = useState(true);

  // fetchStats calls the API and updates state
  const fetchStats = async () => {
    try {
      const res  = await fetch("/api/stats");
      const json = await res.json();
      if (json.success) {
        setStats(json.data);
      }
    } catch (e) {
      // silently fail — keep showing last known values
    } finally {
      setLoading(false);
    }
  };

  // On mount: fetch immediately, then refresh every 30 seconds
  useEffect(() => {
    fetchStats();
    const interval = setInterval(fetchStats, 30000);
    return () => clearInterval(interval); // cleanup on unmount
  }, []);

  // These are the six counters we show in the top bar
  const counters = [
    {
      label: "TOTAL REPORTS",
      value: formatNumber(stats?.total_reports),
      color: "#DC2626", // red
    },
    {
      label: "TODAY",
      value: formatNumber(stats?.reports_today),
      color: "#F4C430", // gold
    },
    {
      label: "HOTSPOTS",
      value: formatNumber(stats?.active_hotspots),
      color: "#FF6B00", // orange
    },
    {
      label: "VERIFIED",
      value: formatNumber(stats?.verified_cases),
      color: "#00FF88", // green
    },
    {
      label: "RANGERS",
      value: formatNumber(stats?.total_rangers),
      color: "#8B5CF6", // purple
    },
    {
      label: "NGO ALERTS",
      value: formatNumber(stats?.ngo_alerts_sent),
      color: "#EF4444", // bright red
    },
  ];

  return (
    <div
      style={{
        display: "flex",
        alignItems: "center",
        height: "100%",
        padding: "0 8px",
        gap: "0",
      }}
    >
      {counters.map((counter, i) => (
        <div
          key={i}
          style={{
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            justifyContent: "center",
            padding: "0 14px",
            // Divider line between counters except after the last one
            borderRight: i < counters.length - 1 ? "1px solid #1a1a1a" : "none",
            minWidth: "80px",
          }}
        >
          {/* The big number */}
          <div
            style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "15px",
              fontWeight: "700",
              color: loading ? "#2a2a2a" : counter.color,
              letterSpacing: "2px",
              transition: "color 0.5s",
            }}
          >
            {loading ? "···" : counter.value}
          </div>

          {/* The small label underneath */}
          <div
            style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "7px",
              color: "#444",
              letterSpacing: "1.5px",
              marginTop: "2px",
              whiteSpace: "nowrap",
            }}
          >
            {counter.label}
          </div>
        </div>
      ))}

      {/* If we have a top danger zone, show it after the counters */}
      {stats?.top_danger_zone && (
        <div
          style={{
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            justifyContent: "center",
            padding: "0 14px",
            borderLeft: "1px solid #1a1a1a",
          }}
        >
          <div
            style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "9px",
              color: "#DC2626",
              letterSpacing: "1px",
              whiteSpace: "nowrap",
            }}
          >
            ⚡ {stats.top_danger_zone.name.toUpperCase()}
          </div>
          <div
            style={{
              fontFamily: "'IBM Plex Mono', monospace",
              fontSize: "7px",
              color: "#444",
              letterSpacing: "1.5px",
            }}
          >
            TOP THREAT ZONE
          </div>
        </div>
      )}
    </div>
  );
}