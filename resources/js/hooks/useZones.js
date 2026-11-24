import { useState, useEffect, useCallback } from 'react';
import { fetchHeatmap } from '../services/api';

export function useZones() {
    const [zones,     setZones]     = useState([]);
    const [hotspots,  setHotspots]  = useState([]);
    const [loading,   setLoading]   = useState(true);
    const [error,     setError]     = useState(null);

    const load = useCallback(async () => {
        try {
            setLoading(true);
            const res = await fetchHeatmap();
            setZones(res.data.data.zones);
            setHotspots(res.data.data.hotspots);
        } catch (e) {
            setError(e.message);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
        // Refresh every 60 seconds
        const interval = setInterval(load, 60000);
        return () => clearInterval(interval);
    }, [load]);

    return { zones, hotspots, loading, error, refresh: load };
}