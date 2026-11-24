export default function StatCard({ label, value, sub, color = 'var(--text)', accent }) {
    return (
        <div style={{
            background:   'var(--bg2)',
            border:       '1px solid var(--border)',
            borderLeft:   accent ? `2px solid ${accent}` : '2px solid var(--border)',
            padding:      '16px 20px',
            borderRadius: '2px',
        }}>
            <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1.5px', marginBottom: '8px' }}>
                {label}
            </div>
            <div style={{ fontSize: '28px', fontWeight: 700, color, lineHeight: 1, marginBottom: '4px' }}>
                {value}
            </div>
            {sub && <div style={{ fontSize: '10px', color: 'var(--muted)' }}>{sub}</div>}
        </div>
    );
}