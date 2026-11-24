import { useEffect, useState } from 'react';
import { getReports, updateStatus } from '../services/adminApi';

const SEVERITY_COLOR = { critical: '#cc2222', high: '#ff6b35', medium: '#ffb800', low: 'rgba(255,255,255,0.3)' };
const STATUS_COLOR   = { pending: '#ffb800', verified: '#00e676', rejected: 'rgba(255,255,255,0.3)', escalated: '#cc2222' };

export default function Reports() {
    const [reports,  setReports]  = useState([]);
    const [total,    setTotal]    = useState(0);
    const [page,     setPage]     = useState(1);
    const [filter,   setFilter]   = useState({ status: '', category: '' });
    const [selected, setSelected] = useState(null);
    const [loading,  setLoading]  = useState(false);

    const load = async () => {
        setLoading(true);
        try {
            const res = await getReports({ page, ...filter, per_page: 15 });
            setReports(res.data.data.data || []);
            setTotal(res.data.data.total  || 0);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { load(); }, [page, filter]);

    const handleStatus = async (id, status, severity) => {
        await updateStatus(id, { status, severity });
        load();
        setSelected(null);
    };

    return (
        <div style={{ display: 'flex', height: '100%' }}>
            {/* Main table */}
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', overflow: 'hidden' }}>

                {/* Toolbar */}
                <div style={{
                    padding:      '12px 20px',
                    borderBottom: '1px solid var(--border)',
                    display:      'flex',
                    gap:          '10px',
                    alignItems:   'center',
                    background:   'var(--bg2)',
                    flexShrink:   0,
                }}>
                    <span style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1.5px', marginRight: '8px' }}>
                        FILTER
                    </span>

                    {/* Status filter */}
                    <select
                        className="admin-input"
                        style={{ width: '140px' }}
                        value={filter.status}
                        onChange={e => setFilter(f => ({ ...f, status: e.target.value }))}
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                        <option value="escalated">Escalated</option>
                    </select>

                    {/* Category filter */}
                    <select
                        className="admin-input"
                        style={{ width: '160px' }}
                        value={filter.category}
                        onChange={e => setFilter(f => ({ ...f, category: e.target.value }))}
                    >
                        <option value="">All Categories</option>
                        <option value="corruption">Corruption</option>
                        <option value="child_abuse">Child Abuse</option>
                        <option value="other">Other</option>
                    </select>

                    <span style={{ marginLeft: 'auto', fontSize: '10px', color: 'var(--muted)' }}>
                        {total} TOTAL
                    </span>
                </div>

                {/* Table */}
                <div style={{ flex: 1, overflowY: 'auto' }}>
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TITLE</th>
                                <th>CATEGORY</th>
                                <th>DISTRICT</th>
                                <th>SEVERITY</th>
                                <th>STATUS</th>
                                <th>AI</th>
                                <th>DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            {reports.map(r => (
                                <tr
                                    key={r.id}
                                    onClick={() => setSelected(r)}
                                    style={{ cursor: 'pointer', background: selected?.id === r.id ? 'rgba(139,0,0,0.07)' : '' }}
                                >
                                    <td style={{ color: 'var(--muted)' }}>#{r.id}</td>
                                    <td style={{ maxWidth: '200px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                        {r.title}
                                    </td>
                                    <td><span className={`badge ${r.category === 'child_abuse' ? 'badge-red' : r.category === 'corruption' ? 'badge-yellow' : 'badge-muted'}`}>{r.category}</span></td>
                                    <td style={{ fontFamily: 'var(--bn)', fontSize: '12px' }}>{r.district}</td>
                                    <td style={{ color: SEVERITY_COLOR[r.severity], fontWeight: 600, fontSize: '11px' }}>
                                        {r.severity?.toUpperCase()}
                                    </td>
                                    <td style={{ color: STATUS_COLOR[r.status], fontSize: '11px' }}>
                                        {r.status?.toUpperCase()}
                                    </td>
                                    <td>
                                        <span className={`badge ${r.ai_processed ? 'badge-green' : 'badge-muted'}`}>
                                            {r.ai_processed ? 'DONE' : 'PENDING'}
                                        </span>
                                    </td>
                                    <td style={{ color: 'var(--muted)', fontSize: '11px' }}>
                                        {new Date(r.created_at).toLocaleDateString('bn-BD')}
                                    </td>
                                </tr>
                            ))}
                            {!loading && reports.length === 0 && (
                                <tr><td colSpan={8} style={{ textAlign: 'center', color: 'var(--muted)', padding: '32px' }}>কোনো রিপোর্ট নেই</td></tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                <div style={{
                    padding:      '10px 20px',
                    borderTop:    '1px solid var(--border)',
                    display:      'flex',
                    gap:          '8px',
                    alignItems:   'center',
                    background:   'var(--bg2)',
                    flexShrink:   0,
                }}>
                    <button className="btn btn-ghost" onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>
                        ← PREV
                    </button>
                    <span style={{ fontSize: '10px', color: 'var(--muted)' }}>PAGE {page}</span>
                    <button className="btn btn-ghost" onClick={() => setPage(p => p + 1)} disabled={reports.length < 15}>
                        NEXT →
                    </button>
                </div>
            </div>

            {/* Detail panel */}
            {selected && (
                <div style={{
                    width:         '320px',
                    borderLeft:    '1px solid var(--border)',
                    background:    'var(--bg2)',
                    display:       'flex',
                    flexDirection: 'column',
                    flexShrink:    0,
                }}>
                    <div style={{
                        padding:      '12px 16px',
                        borderBottom: '1px solid var(--border)',
                        display:      'flex',
                        justifyContent:'space-between',
                        alignItems:   'center',
                    }}>
                        <span style={{ fontSize: '10px', letterSpacing: '1.5px', color: 'var(--muted)' }}>
                            REPORT #{selected.id}
                        </span>
                        <button onClick={() => setSelected(null)} style={{
                            background: 'none', border: 'none',
                            color: 'var(--muted)', cursor: 'pointer', fontSize: '16px',
                        }}>×</button>
                    </div>

                    <div style={{ flex: 1, overflowY: 'auto', padding: '16px' }}>
                        {/* Title */}
                        <div style={{ marginBottom: '16px' }}>
                            <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1px', marginBottom: '4px' }}>TITLE</div>
                            <div style={{ fontSize: '13px', lineHeight: 1.4 }}>{selected.title}</div>
                        </div>

                        {/* Description */}
                        <div style={{ marginBottom: '16px' }}>
                            <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1px', marginBottom: '4px' }}>DESCRIPTION</div>
                            <div style={{ fontSize: '11px', color: 'rgba(255,255,255,0.6)', lineHeight: 1.6 }}>
                                {selected.description}
                            </div>
                        </div>

                        {/* Meta */}
                        {[
                            { label: 'DISTRICT',  value: selected.district,  bn: true },
                            { label: 'CATEGORY',  value: selected.category },
                            { label: 'SEVERITY',  value: selected.severity?.toUpperCase(), color: SEVERITY_COLOR[selected.severity] },
                            { label: 'ANONYMOUS', value: selected.is_anonymous ? 'YES' : 'NO' },
                            { label: 'AI TAGS',   value: selected.ai_tags?.join(', ') || '—' },
                        ].map((m, i) => (
                            <div key={i} style={{
                                display:      'flex',
                                justifyContent:'space-between',
                                padding:      '7px 0',
                                borderBottom: '1px solid rgba(255,255,255,0.04)',
                                alignItems:   'center',
                            }}>
                                <span style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1px' }}>{m.label}</span>
                                <span style={{
                                    fontSize:   '11px',
                                    fontFamily: m.bn ? 'var(--bn)' : 'var(--mono)',
                                    color:      m.color || 'rgba(255,255,255,0.7)',
                                    fontWeight: m.color ? 600 : 400,
                                }}>{m.value || '—'}</span>
                            </div>
                        ))}
                    </div>

                    {/* Actions */}
                    <div style={{
                        padding:   '12px 16px',
                        borderTop: '1px solid var(--border)',
                        display:   'flex',
                        flexDirection: 'column',
                        gap:       '8px',
                    }}>
                        <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1px', marginBottom: '4px' }}>
                            UPDATE STATUS
                        </div>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '6px' }}>
                            <button className="btn btn-ghost" style={{ color: '#00e676', borderColor: 'rgba(0,230,118,0.3)', fontSize: '10px' }}
                                onClick={() => handleStatus(selected.id, 'verified', 'medium')}>
                                ✓ VERIFY
                            </button>
                            <button className="btn btn-ghost" style={{ color: '#cc2222', borderColor: 'rgba(139,0,0,0.3)', fontSize: '10px' }}
                                onClick={() => handleStatus(selected.id, 'escalated', 'high')}>
                                ↑ ESCALATE
                            </button>
                            <button className="btn btn-ghost" style={{ color: '#ffb800', borderColor: 'rgba(255,184,0,0.3)', fontSize: '10px' }}
                                onClick={() => handleStatus(selected.id, 'pending', selected.severity)}>
                                ◌ HOLD
                            </button>
                            <button className="btn btn-ghost" style={{ fontSize: '10px' }}
                                onClick={() => handleStatus(selected.id, 'rejected', selected.severity)}>
                                ✕ REJECT
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}