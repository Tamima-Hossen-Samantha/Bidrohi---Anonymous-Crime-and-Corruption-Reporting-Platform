import { useEffect, useState } from 'react';

const STATUS_COLOR = {
    sent:    '#00e676',
    pending: '#ffb800',
    failed:  '#cc2222',
};

export default function NgoAlerts() {
    const [alerts, setAlerts]   = useState([]);
    const [stats, setStats]     = useState({ sent: 0, pending: 0, failed: 0, this_week: 0 });
    const [loading, setLoading] = useState(true);
    const [page, setPage]       = useState(1);
    const [hasMore, setHasMore] = useState(false);

    const fetchAlerts = async (pageNum = 1) => {
        setLoading(true);
        try {
            const res  = await fetch(`/api/admin/ngo-alerts?page=${pageNum}`, { credentials: 'include' });
            const json = await res.json();
            if (json.success) {
                setAlerts(prev => pageNum === 1 ? json.data : [...prev, ...json.data]);
                setStats(json.stats || stats);
                setHasMore(json.has_more);
                setPage(pageNum);
            }
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { fetchAlerts(1); }, []);

    return (
        <div className="fade-in" style={{ padding: '24px', overflowY: 'auto', height: '100%' }}>

            {/* Page title */}
            <div style={{ marginBottom: '20px' }}>
                <div style={{ fontFamily: 'var(--bn)', fontSize: '18px', fontWeight: 700, color: 'var(--maroon)' }}>
                    এনজিও সতর্কতা
                </div>
            </div>

            {/* Stat cards */}
            <div style={{
                display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)',
                gap: '1px', background: 'var(--border)',
                border: '1px solid var(--border)', marginBottom: '20px',
            }}>
                {[
                    { label: 'TOTAL SENT',   value: stats.sent,      color: '#00e676' },
                    { label: 'PENDING',      value: stats.pending,   color: '#ffb800' },
                    { label: 'FAILED',       value: stats.failed,    color: '#cc2222' },
                    { label: 'THIS WEEK',    value: stats.this_week, color: 'var(--muted)' },
                ].map((s, i) => (
                    <div key={i} style={{ background: 'var(--bg2)', padding: '16px' }}>
                        <div style={{ fontSize: '22px', fontWeight: 700, color: s.color, letterSpacing: '2px' }}>
                            {String(s.value).padStart(4, '0')}
                        </div>
                        <div style={{ fontSize: '8px', color: 'var(--muted)', letterSpacing: '1.5px', marginTop: '4px' }}>
                            {s.label}
                        </div>
                    </div>
                ))}
            </div>

            {/* Table */}
            <div style={{ background: 'var(--bg2)', border: '1px solid var(--border)', borderRadius: '2px' }}>
                <div style={{
                    padding: '12px 16px', borderBottom: '1px solid var(--border)',
                    display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                }}>
                    <span style={{ fontSize: '10px', letterSpacing: '1.5px', color: 'var(--muted)' }}>
                        ALL NGO ALERTS
                    </span>
                </div>

                <div style={{ overflowX: 'auto' }}>
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>LOCATION</th>
                                <th>ALERT TYPE</th>
                                <th>SENT TO</th>
                                <th>ABUSE COUNT</th>
                                <th>STATUS</th>
                                <th>DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            {alerts.map(a => (
                                <tr key={a.id}>
                                    <td style={{ color: 'var(--muted)' }}>#{a.id}</td>
                                    <td style={{ fontFamily: 'var(--bn)', fontSize: '12px' }}>
                                        {a.location_name}
                                    </td>
                                    <td>
                                        <span className="badge badge-red">{a.alert_type}</span>
                                    </td>
                                    <td style={{ fontSize: '11px', color: 'rgba(255,255,255,0.5)' }}>
                                        {a.sent_to_email}
                                    </td>
                                    <td style={{ textAlign: 'center', color: '#ffb800', fontWeight: 700 }}>
                                        {a.abuse_count}
                                    </td>
                                    <td>
                                        <span style={{
                                            fontSize: '10px', fontWeight: 600,
                                            color: STATUS_COLOR[a.status] || 'var(--muted)',
                                            letterSpacing: '1px',
                                        }}>
                                            {a.status?.toUpperCase()}
                                        </span>
                                    </td>
                                    <td style={{ color: 'var(--muted)', fontSize: '11px' }}>
                                        {new Date(a.created_at).toLocaleDateString('bn-BD')}
                                    </td>
                                </tr>
                            ))}
                            {alerts.length === 0 && !loading && (
                                <tr>
                                    <td colSpan={7} style={{ textAlign: 'center', color: 'var(--muted)', padding: '32px' }}>
                                        কোনো এনজিও সতর্কতা নেই
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Load more */}
                {hasMore && (
                    <div style={{ padding: '12px', textAlign: 'center', borderTop: '1px solid var(--border)' }}>
                        <button
                            onClick={() => fetchAlerts(page + 1)}
                            disabled={loading}
                            className="btn btn-ghost"
                            style={{ fontSize: '9px', letterSpacing: '1.5px' }}
                        >
                            {loading ? 'LOADING...' : 'LOAD MORE'}
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}