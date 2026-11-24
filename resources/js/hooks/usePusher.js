import { useEffect, useRef, useState } from "react";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

// Attach Pusher to window so Echo can find it
window.Pusher = Pusher;

let echoInstance = null;

function getEcho() {
  if (echoInstance) return echoInstance;

  echoInstance = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "ap2",
    forceTLS: true,
  });

  return echoInstance;
}

/**
 * usePusher — subscribes to the bidrohi-feed channel and returns new reports
 * as they arrive in real-time.
 *
 * Usage:
 *   const { newReports, newReportIds } = usePusher();
 *
 * newReports    → array of the last 50 live reports (most recent first)
 * newReportIds  → Set of IDs for highlighting in the feed
 */
export function usePusher() {
  const [newReports, setNewReports]     = useState([]);
  const [newReportIds, setNewReportIds] = useState([]);
  const channelRef                       = useRef(null);

  useEffect(() => {
    try {
      const echo = getEcho();
      channelRef.current = echo.channel("bidrohi-feed");

      channelRef.current.listen(".new-report", (data) => {
        setNewReports((prev) => [data, ...prev].slice(0, 50));
        setNewReportIds([data.id]);

        // Clear highlight after 3s
        setTimeout(() => setNewReportIds([]), 3000);
      });
    } catch (err) {
      console.warn("Pusher not configured yet:", err.message);
    }

    return () => {
      channelRef.current?.stopListening(".new-report");
    };
  }, []);

  return { newReports, newReportIds };
}