const RANK_COLORS = {
    'নাগরিক':  '#7d8590',
    'সাক্ষী':   '#3fb950',
    'প্রতিজ্ঞা': '#388bfd',
    'রক্ষক':   '#d29922',
    'বিদ্রোহী': '#e63946',
};

export default function RankBadge({ rank, points }) {
    const color = RANK_COLORS[rank] || '#7d8590';

    return (
        <div style={{
            display:      'flex',
            alignItems:   'center',
            gap:          '6px',
            padding:      '3px 10px',
            borderRadius: '3px',
            border:       `1px solid ${color}50`,
            background:   `${color}10`,
        }}>
            <span style={{
                fontFamily:  'var(--font-bn)',
                color:       color,
                fontWeight:  700,
                fontSize:    '13px',
            }}>{rank}</span>
            {points !== undefined && (
                <span style={{
                    fontFamily: 'var(--font-mono)',
                    color:      'var(--text-muted)',
                    fontSize:   '11px',
                }}>{points}pts</span>
            )}
        </div>
    );
}