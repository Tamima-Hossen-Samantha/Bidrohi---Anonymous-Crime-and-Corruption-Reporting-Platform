import { useState, useEffect } from 'react';
import { fetchLeaderboard, fetchRankDist } from '../services/api';

export function useLeaderboard() {
    const [rangers,      setRangers]      = useState([]);
    const [rankDist,     setRankDist]     = useState([]);
    const [loading,      setLoading]      = useState(true);

    useEffect(() => {
        Promise.all([fetchLeaderboard(), fetchRankDist()])
            .then(([lb, rd]) => {
                setRangers(lb.data.data);
                setRankDist(rd.data.data);
            })
            .finally(() => setLoading(false));
    }, []);

    return { rangers, rankDist, loading };
}