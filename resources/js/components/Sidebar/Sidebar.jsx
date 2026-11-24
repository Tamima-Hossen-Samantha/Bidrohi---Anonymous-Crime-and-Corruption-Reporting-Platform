import { useState } from 'react';
import LeaderboardPanel from './LeaderboardPanel';

const TABS = [
    { id: 'leaderboard', label: 'রেঞ্জার',  labelEn: 'RANGERS'  },
    { id: 'zones',       label: 'জেলা',     labelEn: 'ZONES'    },
];

export default function Sidebar({ zones, selectedZone }) {
    const [tab, setTab] = useState('leaderboard');

    return (
        <div style={{
            position:      'fixed',
            right:         0,
            top:           0,
            bottom:        0,
            width:         '300px',
            background:    'var(--dark-2)',
            borderLeft:    '1px solid var(--border)',
            display:       'flex',
            flexDirection: 'column',
            zIndex:        50,
        }}>
            {/* Tab bar */}
            <div style={{
                display:       'flex',
                borderBottom:  '1px solid var(--border)',
                marginTop:     '56px',
            }}>
                {TABS.map(t => (
                    <button key={t.id} onClick={() => setTab(t.id)} style={{
                        flex:          1,
                        padding:       '12px',
                        background:    'none',
                        border:        'none',
                        borderBottom:  tab === t.id ? '2px solid var(--red)' : '2px solid transparent',
                        cursor:        'pointer',
                        transition:    'all 0.2s',
                    }}>
                        <div style={{
                            fontFamily: 'var(--font-bn)',
                            fontSize:   '13px',
                            fontWeight: 600,
                            color:      tab === t.id ? 'var(--text)' : 'var(--text-muted)',
                        }}>{t.label}</div>
                        <div style={{
                            fontFamily: 'var(--font-mono)',
                            fontSize:   '9px',
                            color:      tab === t.id ? 'var(--red)' : 'var(--text-muted)',
                            marginTop:  '2px',
                        }}>{t.labelEn}</div>
                    </button>
                ))}
            </div>

            {/* Panel content */}
            <div style={{ flex: 1, overflow: 'hidden' }}>
                {tab === 'leaderboard' && <LeaderboardPanel />}
                {tab === 'zones'       && <ZonesPanel zones={zones} selectedZone={selectedZone} />}
            </div>
        </div>
    );
}

function ZonesPanel({ zones, selectedZone }) {
    const sorted = [...(zones || [])].sort((a, b) => b.score - a.score);

    return (
        <div style={{ overflowY: 'auto', height: '100%' }}>
            {sorted.map(zone => (
                <div key={zone.id} style={{
                    padding:      '10px 16px',
                    borderBottom: '1px solid var(--border)',
                    background:   selectedZone?.id === zone.id ? 'var(--dark-3)' : 'transparent',
                    transition:   'background 0.2s',
                }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div>
                            <div style={{ fontFamily: 'var(--font-bn)', fontSize: '14px', fontWeight: 600 }}>
                                {zone.name_bn}
                            </div>
                            <div style={{ fontFamily: 'var(--font-mono)', fontSize: '10px', color: 'var(--text-muted)' }}>
                                {zone.report_count} reports
                            </div>
                        </div>
                        {/* Score bar */}
                        <div style={{ textAlign: 'right' }}>
                            <div style={{
                                fontFamily: 'var(--font-mono)',
                                fontSize:   '18px',
                                fontWeight: 700,
                                color:      zone.status === 'red' ? 'var(--red)' : zone.status === 'yellow' ? 'var(--yellow)' : 'var(--green)',
                            }}>{zone.score}</div>
                            <div style={{
                                width:        '60px',
                                height:       '3px',
                                background:   'var(--dark-4)',
                                borderRadius: '2px',
                                marginTop:    '4px',
                            }}>
                                <div style={{
                                    width:        `${zone.score}%`,
                                    height:       '100%',
                                    background:   zone.status === 'red' ? 'var(--red)' : zone.status === 'yellow' ? 'var(--yellow)' : 'var(--green)',
                                    borderRadius: '2px',
                                    transition:   'width 0.5s ease',
                                }} />
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}