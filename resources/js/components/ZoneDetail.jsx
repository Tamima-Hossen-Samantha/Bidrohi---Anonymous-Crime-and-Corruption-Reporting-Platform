export default function ZoneDetail({ zone, onClose }) {
    if (!zone) return null;

    const COLOR = { red: 'var(--red)', yellow: 'var(--yellow)', green: 'var(--green)' };
    const color = COLOR[zone.status] || COLOR.green;

    return (
        <div style={{
            position:      'fixed',
            bottom:        '24px',
            right:         '24px',
            width:         '240px',
            background:    'rgba(8,8,8,0.96)',
            border:        '1px solid rgba(255,255,255,0.08)',
            borderLeft:    `2px solid ${color}`,
            backdropFilter:'blur(12px)',
            zIndex:        997,
            borderRadius:  '2px',
            animation:     'slideIn 0.2s ease',
        }}>
            {/* Header */}
            <div style={{
                padding:      '10px 12px',
                borderBottom: '1px solid rgba(255,255,255,0.06)',
                display:      'flex',
                justifyContent:'space-between',
                alignItems:   'center',
            }}>
                <div>
                    <div style={{ fontFamily: 'var(--bn)', fontSize: '14px', fontWeight: 600, color }}>
                        {zone.name_bn}
                    </div>
                    <div style={{ fontSize: '9px', color: 'rgba(255,255,255,0.25)', letterSpacing: '1px', marginTop: '2px' }}>
                        {zone.name.toUpperCase()} · {zone.division}
                    </div>
                </div>
                <button onClick={onClose} style={{
                    background: 'none', border: 'none',
                    color: 'rgba(255,255,255,0.2)',
                    cursor: 'pointer', fontSize: '14px', padding: '2px 4px',
                }}>×</button>
            </div>

            {/* Stats grid */}
            <div style={{
                display:             'grid',
                gridTemplateColumns: '1fr 1fr',
                gap:                 '1px',
                background:          'rgba(255,255,255,0.05)',
                borderBottom:        '1px solid rgba(255,255,255,0.06)',
            }}>
                {[
                    { label: 'DANGER SCORE', value: zone.score,          color },
                    { label: 'REPORTS',      value: zone.report_count,   color: 'rgba(255,255,255,0.7)' },
                    { label: 'VERIFIED',     value: zone.verified_count ?? 0, color: 'var(--green)' },
                    { label: 'RANGERS',      value: zone.ranger_count,   color: 'var(--gold)' },
                ].map((s, i) => (
                    <div key={i} style={{
                        padding:    '8px 12px',
                        background: 'rgba(8,8,8,0.8)',
                    }}>
                        <div style={{ fontSize: '9px', color: 'rgba(255,255,255,0.2)', letterSpacing: '1px', marginBottom: '3px' }}>
                            {s.label}
                        </div>
                        <div style={{ fontSize: '16px', fontWeight: 600, color: s.color, lineHeight: 1 }}>
                            {s.value}
                        </div>
                    </div>
                ))}
            </div>

            {/* Score bar */}
            <div style={{ padding: '10px 12px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '5px' }}>
                    <span style={{ fontSize: '9px', color: 'rgba(255,255,255,0.2)', letterSpacing: '1px' }}>THREAT LEVEL</span>
                    <span style={{ fontSize: '9px', color }}>{zone.score}/100</span>
                </div>
                <div style={{ height: '3px', background: 'rgba(255,255,255,0.06)', borderRadius: '1px' }}>
                    <div style={{
                        width:        `${zone.score}%`,
                        height:       '100%',
                        background:   color,
                        borderRadius: '1px',
                        transition:   'width 0.5s ease',
                    }} />
                </div>
            </div>
        </div>
    );
}