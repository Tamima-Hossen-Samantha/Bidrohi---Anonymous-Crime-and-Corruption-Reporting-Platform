import { useState } from 'react';
import './styles/dashboard.css';
import IntelMap    from './components/Map/IntelMap';
import TopBar      from './components/TopBar';
import LeftPanel   from './components/LeftPanel';
import ZoneDetail  from './components/ZoneDetail';
import { useZones }       from './hooks/useZones';
import { useLeaderboard } from './hooks/useLeaderboard';
import { usePusher }      from './hooks/usePusher';   // ← ADD THIS
import StatsBar from './components/StatsBar';
import NewsFeed from './components/NewsFeed';

export default function App() {
    const { zones, hotspots, loading } = useZones();
    const { rangers }                  = useLeaderboard();
    const { newReportIds }             = usePusher();  // ← ADD THIS
    const [selectedZone, setSelectedZone] = useState(null);

    const stats = {
        reports:  zones.reduce((s, z) => s + (z.report_count || 0), 0),
        verified: zones.reduce((s, z) => s + (z.verified_count || 0), 0),
        ngo:      0,
        rangers:  rangers.length,
    };

    return (
        <div style={{ width: '100%', height: '100%', position: 'relative' }}>
            <TopBar stats={stats} />
            <StatsBar />

            <div style={{ position: 'absolute', inset: 0, paddingTop: '38px' }}>
                <IntelMap
                    zones={zones}
                    hotspots={hotspots}
                    onZoneClick={setSelectedZone}
                />
            </div>

            <LeftPanel
                zones={zones}
                rangers={rangers}
                hotspots={hotspots}
                onZoneClick={setSelectedZone}
                selectedZone={selectedZone}
            />

            <ZoneDetail
                zone={selectedZone}
                onClose={() => setSelectedZone(null)}
            />

            {/* ← PASS newReportIds HERE */}
            <NewsFeed newReportIds={newReportIds} />
        </div>
    );
}