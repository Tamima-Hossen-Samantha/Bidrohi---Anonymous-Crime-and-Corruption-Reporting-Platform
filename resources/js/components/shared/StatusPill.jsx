export default function StatusPill({ status }) {
    const config = {
        red:    { label: 'বিপজ্জনক', color: '#e63946' },
        yellow: { label: 'সতর্কতা',   color: '#d29922' },
        green:  { label: 'নিরাপদ',   color: '#3fb950' },
    };

    const { label, color } = config[status] || config.green;

    return (
        <span style={{
            display:         'inline-flex',
            alignItems:      'center',
            gap:             '5px',
            padding:         '2px 8px',
            borderRadius:    '3px',
            border:          `1px solid ${color}40`,
            background:      `${color}15`,
            color:           color,
            fontSize:        '11px',
            fontFamily:      'var(--font-bn)',
            fontWeight:      600,
            letterSpacing:   '0.5px',
        }}>
            <span style={{
                width: 6, height: 6,
                borderRadius: '50%',
                background: color,
                display: 'inline-block',
                animation: status === 'red' ? 'pulse-red 1.5s infinite' : 'none',
            }} />
            {label}
        </span>
    );
}