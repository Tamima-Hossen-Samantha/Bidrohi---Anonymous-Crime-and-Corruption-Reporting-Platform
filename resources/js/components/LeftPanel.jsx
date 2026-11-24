import { useState } from 'react';

const TABS = ['ZONES', 'RANGERS', 'HOTSPOTS'];

export default function LeftPanel({ zones, rangers, hotspots, onZoneClick, selectedZone }) {
    const [tab,  setTab]  = useState('ZONES');
    const [open, setOpen] = useState(true);

    const sorted = [...(zones || [])].sort((a, b) => b.score - a.score);

    return (
        <>
            {/* Toggle button */}
            <button onClick={() => setOpen(o => !o)} style={{
                position:    'fixed',
                top:         '50px',
                left:        open ? '260px' : '0px',
                width:       '18px',
                height:      '48px',
                background:  'rgba(8,8,8,0.95)',
                border:      '1px solid rgba(255,255,255,0.08)',
                borderLeft:  open ? '1px solid rgba(255,255,255,0.08)' : 'none',
                color:       'rgba(255,255,255,0.3)',
                fontSize:    '9px',
                cursor:      'pointer',
                zIndex:      999,
                display:     'flex',
                alignItems:  'center',
                justifyContent: 'center',
                transition:  'left 0.2s ease',
                borderRadius: open ? '0 3px 3px 0' : '0 3px 3px 0',
            }}>
                {open ? '◀' : '▶'}
            </button>

            {/* Panel */}
            <div style={{
                position:      'fixed',
                top:           '38px',
                left:          0,
                bottom:        0,
                width:         '260px',
                background:    'rgba(8,8,8,0.94)',
                borderRight:   '1px solid rgba(255,255,255,0.07)',
                backdropFilter:'blur(12px)',
                display:       'flex',
                flexDirection: 'column',
                zIndex:        998,
                transform:     open ? 'translateX(0)' : 'translateX(-260px)',
                transition:    'transform 0.2s ease',
            }}>

                {/* Tabs */}
                <div style={{
                    display:     'flex',
                    borderBottom:'1px solid rgba(255,255,255,0.07)',
                    flexShrink:  0,
                }}>
                    {TABS.map(t => (
                        <button key={t} onClick={() => setTab(t)} style={{
                            flex:         1,
                            padding:      '9px 0',
                            background:   'none',
                            border:       'none',
                            borderBottom: tab === t ? '1px solid var(--red)' : '1px solid transparent',
                            color:        tab === t ? 'rgba(255,255,255,0.9)' : 'rgba(255,255,255,0.25)',
                            fontSize:     '9px',
                            letterSpacing:'1.5px',
                            cursor:       'pointer',
                            transition:   'all 0.15s',
                        }}>{t}</button>
                    ))}
                </div>

                {/* Content */}
                <div style={{ flex: 1, overflowY: 'auto' }}>

                    {/* ZONES tab */}
                    {tab === 'ZONES' && sorted.map(zone => (
                        <div key={zone.id}
                            onClick={() => onZoneClick(zone)}
                            style={{
                                padding:      '8px 14px',
                                borderBottom: '1px solid rgba(255,255,255,0.04)',
                                cursor:       'pointer',
                                background:   selectedZone?.id === zone.id
                                    ? 'rgba(255,45,45,0.06)'
                                    : 'transparent',
                                borderLeft:   selectedZone?.id === zone.id
                                    ? '2px solid var(--maroon)'
                                    : '2px solid transparent',
                                transition:   'all 0.15s',
                                display:      'flex',
                                alignItems:   'center',
                                gap:          '10px',
                            }}
                            onMouseEnter={e => {
                                if (selectedZone?.id !== zone.id)
                                    e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                            }}
                            onMouseLeave={e => {
                                if (selectedZone?.id !== zone.id)
                                    e.currentTarget.style.background = 'transparent';
                            }}
                        >
                            {/* Status dot */}
                            <span style={{
                                width: 5, height: 5,
                                borderRadius: '50%',
                                background: zone.status === 'red' ? 'var(--maroon)' : zone.status === 'yellow' ? 'var(--yellow)' : 'var(--green)',
                                flexShrink: 0,
                                ...(zone.status === 'red' ? { animation: 'blink 1.5s infinite' } : {}),
                            }} />

                            {/* Name */}
                            <div style={{ flex: 1, minWidth: 0 }}>
                                <div style={{
                                    fontFamily:   'var(--bn)',
                                    fontSize:     '12px',
                                    fontWeight:   500,
                                    color:        'rgba(255,255,255,0.85)',
                                    whiteSpace:   'nowrap',
                                    overflow:     'hidden',
                                    textOverflow: 'ellipsis',
                                }}>{zone.name_bn}</div>
                                <div style={{
                                    fontSize:     '9px',
                                    color:        'rgba(255,255,255,0.2)',
                                    letterSpacing:'0.5px',
                                    marginTop:    '1px',
                                }}>{zone.report_count} RPT</div>
                            </div>

                            {/* Score */}
                            <div style={{ textAlign: 'right', flexShrink: 0 }}>
                                <div style={{
                                    fontSize:   '14px',
                                    fontWeight: 600,
                                    color:      zone.status === 'red' ? 'var(--maroon)' : zone.status === 'yellow' ? 'var(--yellow)' : 'var(--green)',
                                    lineHeight: 1,
                                }}>{zone.score}</div>
                                <div style={{
                                    width: '36px', height: '2px',
                                    background: 'rgba(255,255,255,0.08)',
                                    marginTop: '4px',
                                    borderRadius: '1px',
                                }}>
                                    <div style={{
                                        width: `${zone.score}%`,
                                        height: '100%',
                                        background: zone.status === 'red' ? 'var(--maroon)' : zone.status === 'yellow' ? 'var(--yellow)' : 'var(--green)',
                                        borderRadius: '1px',
                                    }} />
                                </div>
                            </div>
                        </div>
                    ))}

                    {/* RANGERS tab */}
                    {tab === 'RANGERS' && (rangers || []).slice(0, 20).map((r, i) => (
                        <div key={i} style={{
                            padding:      '7px 14px',
                            borderBottom: '1px solid rgba(255,255,255,0.04)',
                            display:      'flex',
                            alignItems:   'center',
                            gap:          '8px',
                        }}>
                            <span style={{
                                fontSize:   '9px',
                                color:      i < 3 ? 'var(--gold)' : 'rgba(255,255,255,0.2)',
                                width:      '16px',
                                flexShrink: 0,
                                textAlign:  'right',
                            }}>{i + 1}</span>
                            <div style={{ flex: 1, minWidth: 0 }}>
                                <div style={{
                                    fontSize:     '11px',
                                    color:        'rgba(255,255,255,0.85)',
                                    whiteSpace:   'nowrap',
                                    overflow:     'hidden',
                                    textOverflow: 'ellipsis',
                                }}>{r.username}</div>
                                <div style={{
                                    fontFamily: 'var(--bn)',
                                    fontSize:   '9px',
                                    color:      'rgba(255,255,255,0.2)',
                                }}>{r.rank} · {r.district}</div>
                            </div>
                            <span style={{
                                fontSize:   '11px',
                                color:      'var(--gold)',
                                fontWeight: 600,
                                flexShrink: 0,
                            }}>{r.points}</span>
                        </div>
                    ))}

                    {/* HOTSPOTS tab */}
                    {tab === 'HOTSPOTS' && (hotspots || []).map((h, i) => (
                        <div key={i} style={{
                            padding:      '8px 14px',
                            borderBottom: '1px solid rgba(255,255,255,0.04)',
                            borderLeft:   h.intensity >= 70 ? '2px solid var(--red)' : '2px solid transparent',
                        }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                <span style={{
                                    fontFamily: 'var(--bn)',
                                    fontSize:   '11px',
                                    color:      'rgba(255,255,255,0.7)',
                                }}>{h.category}</span>
                                <span style={{
                                    fontSize:   '11px',
                                    color:      h.intensity >= 70 ? 'var(--maroon)' : 'var(--yellow)',
                                    fontWeight: 600,
                                }}>{h.intensity}%</span>
                            </div>
                            <div style={{
                                fontSize:    '9px',
                                color:       'rgba(255,255,255,0.2)',
                                marginTop:   '2px',
                                letterSpacing: '0.5px',
                            }}>{h.report_count} REPORTS · {h.radius_meters}m RADIUS</div>
                        </div>
                    ))}

                </div>

                {/* Bottom — submit report button */}
                <div style={{
                    padding:    '10px 14px',
                    borderTop:  '1px solid rgba(255,255,255,0.07)',
                    flexShrink: 0,
                }}>
                    <a href="/" style={{
                        display:        'block',
                        textAlign:      'center',
                        padding:        '8px',
                        background:     'rgba(255,45,45,0.1)',
                        border:         '1px solid rgba(255,45,45,0.3)',
                        color:          'var(--maroon)',
                        fontFamily:     'var(--bn)',
                        fontSize:       '12px',
                        fontWeight:     600,
                        textDecoration: 'none',
                        letterSpacing:  '1px',
                        borderRadius:   '2px',
                        transition:     'all 0.15s',
                    }}
                    onMouseEnter={e => e.currentTarget.style.background = 'rgba(255,45,45,0.2)'}
                    onMouseLeave={e => e.currentTarget.style.background = 'rgba(255,45,45,0.1)'}
                    >
                        + রিপোর্ট করুন
                    </a>
                </div>
            </div>
        </>
    );
}