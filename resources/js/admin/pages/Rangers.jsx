import { useEffect, useState } from 'react';

const RANK_COLOR = {
    'নাগরিক':  'var(--muted)',
    'সাক্ষী':  '#4fc3f7',
    'প্রতিজ্ঞা': '#ffb800',
    'রক্ষক':   '#00e676',
    'বিদ্রোহী': '#cc2222',
};

export default function Rangers() {
    const [rangers, setRangers]   = useState([]);
    const [loading, setLoading]   = useState(true);
    const [page, setPage]         = useState(1);
    const [hasMore, setHasMore]   = useState(false);
    const [toggling, setToggling] = useState(null); // ID of ranger being toggled

    const fetchRangers = async (pageNum = 1) => {
        setLoading(true);
        try {
            const res  = await fetch(`/api/admin/rangers?page=${pageNum}`, { credentials: 'include' });
            const json = await res.json();
            if (json.success) {
                setRangers(prev => pageNum === 1 ? json.data : [...prev, ...json.data]);
                setHasMore(json.has_more);
                setPage(pageNum);
            }
        } finally {
            setLoading(false);
        }
    };

    const toggleRanger = async (userId, username) => {
        if (!confirm(`Toggle active status for @${username}?`)) return;
        setToggling(userId);
        try {
            await fetch(`/admin/rangers/${userId}/toggle`, {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json',
                },
            });
            // Refresh current page
            fetchRangers(1);
        } finally {
            setToggling(null);
        }
    };

    useEffect(() => { fetchRangers(1); }, []);

    return (
        <div className="fade-in" style={{ padding: '24px', overflowY: 'auto', height: '100%' }}>

            {/* Page title */}
            <div style={{ marginBottom: '20px' }}>
                <div style={{ fontFamily: 'var(--bn)', fontSize: '18px', fontWeight: 700, color: 'var(--maroon)' }}>
                    রেঞ্জার ব্যবস্থাপনা
                </div>
            </div>

            {/* Table */}
            <div style={{ background: 'var(--bg2)', border: '1px solid var(--border)', borderRadius: '2px' }}>
                <div style={{
                    padding: '12px 16px', borderBottom: '1px solid var(--border)',
                    display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                }}>
                    <span style={{ fontSize: '10px', letterSpacing: '1.5px', color: 'var(--muted)' }}>
                        ALL RANGERS
                    </span>
                    <span style={{ fontSize: '9px', color: 'rgba(255,255,255,0.15)' }}>
                        {rangers.length} LOADED
                    </span>
                </div>

                <div style={{ overflowX: 'auto' }}>
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>USERNAME</th>
                                <th>EMAIL</th>
                                <th>RANK</th>
                                <th>POINTS</th>
                                <th>VERIFIED</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rangers.map(u => (
                                <tr key={u.id}>
                                    <td style={{ color: 'var(--muted)' }}>#{u.id}</td>
                                    <td style={{ fontWeight: 600, color: 'rgba(255,255,255,0.8)' }}>
                                        @{u.username}
                                    </td>
                                    <td style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)' }}>
                                        {u.email}
                                    </td>
                                    <td>
                                        <span style={{
                                            fontFamily: 'var(--bn)',
                                            fontSize: '12px',
                                            color: RANK_COLOR[u.ranger?.rank] || 'var(--muted)',
                                            fontWeight: 600,
                                        }}>
                                            {u.ranger?.rank || '—'}
                                        </span>
                                    </td>
                                    <td style={{ color: '#ffb800', fontWeight: 700 }}>
                                        {u.ranger?.points ?? 0}
                                    </td>
                                    <td>
                                        {u.email_verified_at
                                            ? <span style={{ color: '#00e676', fontSize: '10px' }}>✓ YES</span>
                                            : <span style={{ color: 'var(--muted)', fontSize: '10px' }}>✗ NO</span>
                                        }
                                    </td>
                                    <td>
                                        <span style={{
                                            fontSize: '10px', fontWeight: 600, letterSpacing: '1px',
                                            color: u.is_active ? '#00e676' : '#cc2222',
                                        }}>
                                            {u.is_active ? 'ACTIVE' : 'SUSPENDED'}
                                        </span>
                                    </td>
                                    <td>
                                        <button
                                            onClick={() => toggleRanger(u.id, u.username)}
                                            disabled={toggling === u.id}
                                            className="btn btn-ghost"
                                            style={{
                                                fontSize: '8px', letterSpacing: '1px', padding: '4px 8px',
                                                color: u.is_active ? '#cc2222' : '#00e676',
                                                borderColor: u.is_active ? '#cc2222' : '#00e676',
                                            }}
                                        >
                                            {toggling === u.id ? '...' : u.is_active ? 'SUSPEND' : 'RESTORE'}
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            {rangers.length === 0 && !loading && (
                                <tr>
                                    <td colSpan={8} style={{ textAlign: 'center', color: 'var(--muted)', padding: '32px' }}>
                                        কোনো রেঞ্জার নেই
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {hasMore && (
                    <div style={{ padding: '12px', textAlign: 'center', borderTop: '1px solid var(--border)' }}>
                        <button
                            onClick={() => fetchRangers(page + 1)}
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