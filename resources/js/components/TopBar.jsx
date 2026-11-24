import { useState, useEffect } from 'react';

export default function TopBar({ stats }) {
    const [time, setTime] = useState(new Date());

    useEffect(() => {
        const t = setInterval(() => setTime(new Date()), 1000);
        return () => clearInterval(t);
    }, []);

    // Bangladesh Standard Time = UTC+6
    const bdTime = new Date(time.getTime() + (6 * 60 * 60 * 1000));
    const hh = String(bdTime.getUTCHours()).padStart(2, '0');
    const mm = String(bdTime.getUTCMinutes()).padStart(2, '0');
    const ss = String(bdTime.getUTCSeconds()).padStart(2, '0');

    const fmt = (n) => String(n).padStart(4, '0');

    return (
        <div style={{
            position:       'fixed',
            top:            0, left: 0, right: 0,
            height:         '38px',
            background:     'rgba(8,8,8,0.96)',
            borderBottom:   '1px solid rgba(255,255,255,0.07)',
            display:        'flex',
            alignItems:     'center',
            zIndex:         1000,
            backdropFilter: 'blur(12px)',
            userSelect:     'none',
        }}>
            {/* Logo + Name */}
            <div style={{
                padding:     '0 14px',
                borderRight: '1px solid rgba(255,255,255,0.07)',
                height:      '100%',
                display:     'flex',
                alignItems:  'center',
                gap:         '8px',
                flexShrink:  0,
            }}>
                <img
                    src="/images/bidrohi-logo.png"
                    alt="BIDROHI"
                    style={{ width: '40px', height: '40px', objectFit: 'contain' }}
                />
                <span style={{
                    fontFamily:    'var(--bn)',
                    color:         'var(--red)',
                    fontSize:      '22px',
                    fontWeight:    700,
                    letterSpacing: '1px',
                }}>বিদ্রোহী</span>
            </div>

            {/* Stats */}
            {[
                { label: 'REPORTS',  value: fmt(stats.reports),  color: 'rgba(255,255,255,0.7)' },
                { label: 'VERIFIED', value: fmt(stats.verified), color: 'var(--green)' },
                { label: 'NGO',      value: fmt(stats.ngo),      color: 'var(--maroon)' },
                { label: 'RANGERS',  value: fmt(stats.rangers),  color: 'var(--gold)' },
            ].map((s, i) => (
                <div key={i} style={{
                    padding:     '0 16px',
                    borderRight: '1px solid rgba(255,255,255,0.07)',
                    height:      '100%',
                    display:     'flex',
                    alignItems:  'center',
                    gap:         '8px',
                }}>
                    <span style={{ color: 'rgba(255,255,255,0.25)', fontSize: '9px', letterSpacing: '1.5px' }}>
                        {s.label}
                    </span>
                    <span style={{ color: s.color, fontSize: '13px', fontWeight: 500 }}>
                        {s.value}
                    </span>
                </div>
            ))}

            <div style={{ flex: 1 }} />

            {/* Zone legend */}
            {[
                { color: 'var(--maroon)',  label: 'বিপজ্জনক' },
                { color: 'var(--yellow)',  label: 'সতর্কতা'   },
                { color: 'var(--green)',   label: 'নিরাপদ'    },
            ].map((l, i) => (
                <div key={i} style={{
                    padding:    '0 12px',
                    display:    'flex',
                    alignItems: 'center',
                    gap:        '5px',
                }}>
                    <span style={{
                        width: 6, height: 6, borderRadius: '50%',
                        background: l.color, display: 'block', flexShrink: 0,
                    }} />
                    <span style={{
                        fontFamily: 'var(--bn)', fontSize: '10px',
                        color: 'rgba(255,255,255,0.4)',
                    }}>{l.label}</span>
                </div>
            ))}

            {/* Bangladesh Time */}
            <div style={{
                padding:    '0 14px',
                borderLeft: '1px solid rgba(255,255,255,0.07)',
                height:     '100%',
                display:    'flex',
                alignItems: 'center',
                gap:        '8px',
                flexShrink: 0,
            }}>
                <span style={{
                    width: 5, height: 5, borderRadius: '50%',
                    background: 'var(--maroon)',
                    animation: 'blink 1.2s infinite',
                    display: 'block',
                }} />
                <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.5)', letterSpacing: '1px' }}>
                    {hh}:{mm}:{ss} BST
                </span>
            </div>
        </div>
    );
}