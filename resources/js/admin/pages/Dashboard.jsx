import { useEffect, useState } from 'react';
import StatCard from '../components/StatCard';
import { getReports, getZones, getLeaderboard } from '../services/adminApi';

export default function Dashboard() {
    const [stats, setStats] = useState({
        total: 0, pending: 0, verified: 0, rejected: 0,
        zones_red: 0, zones_yellow: 0, rangers: 0, ngo_alerts: 0,
    });
    const [recent, setRecent] = useState([]);

    useEffect(() => {
        Promise.all([
            getReports({ per_page: 5 }),
            getZones(),
            getLeaderboard(),
        ]).then(([rp, zp, lp]) => {
            const reports = rp.data.data;
            const zones   = zp.data.data;
            const rangers = lp.data.data;

            setStats({
                total:       reports.total     || 0,
                pending:     reports.data?.filter(r => r.status === 'pending').length   || 0,
                verified:    reports.data?.filter(r => r.status === 'verified').length  || 0,
                rejected:    reports.data?.filter(r => r.status === 'rejected').length  || 0,
                zones_red:   zones.filter(z => z.status === 'red').length,
                zones_yellow:zones.filter(z => z.status === 'yellow').length,
                rangers:     rangers.length,
                ngo_alerts:  0,
            });
            setRecent(reports.data?.slice(0, 8) || []);
        });
    }, []);

    const SEVERITY_COLOR = { critical: '#cc2222', high: '#ff6b35', medium: '#ffb800', low: 'var(--muted)' };
    const STATUS_COLOR   = { pending: '#ffb800', verified: '#00e676', rejected: 'var(--muted)', escalated: '#cc2222' };

    return (
        <div className="fade-in" style={{ padding: '24px', overflowY: 'auto', height: '100%' }}>
            <div style={{ marginBottom: '20px' }}>
                
                <div style={{ fontFamily: 'var(--bn)', fontSize: '18px', fontWeight: 700, color: 'var(--maroon)' }}>
                    সিস্টেম সারসংক্ষেপ
                </div>
            </div>

            {/* Stat cards */}
            <div style={{
                display:             'grid',
                gridTemplateColumns: 'repeat(4, 1fr)',
                gap:                 '1px',
                background:          'var(--border)',
                marginBottom:        '20px',
                border:              '1px solid var(--border)',
            }}>
                <StatCard label="TOTAL REPORTS"  value={stats.total}        accent="var(--muted)"   color="var(--text)" />
                <StatCard label="PENDING"         value={stats.pending}      accent="var(--yellow)"  color="var(--yellow)" />
                <StatCard label="VERIFIED"        value={stats.verified}     accent="var(--green)"   color="var(--green)" />
                <StatCard label="RED ZONES"       value={stats.zones_red}    accent="var(--maroon)"  color="#cc2222" />
            </div>

            {/* Recent reports table */}
            <div style={{
                background:   'var(--bg2)',
                border:       '1px solid var(--border)',
                borderRadius: '2px',
            }}>
                <div style={{
                    padding:      '12px 16px',
                    borderBottom: '1px solid var(--border)',
                    display:      'flex',
                    justifyContent:'space-between',
                    alignItems:   'center',
                }}>
                    <span style={{ fontSize: '10px', letterSpacing: '1.5px', color: 'var(--muted)' }}>RECENT REPORTS</span>
                    <span style={{ fontSize: '9px', color: 'rgba(255,255,255,0.15)' }}>LAST 8</span>
                </div>
                <div style={{ overflowX: 'auto' }}>
                    <table className="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>CATEGORY</th>
                                <th>DISTRICT</th>
                                <th>SEVERITY</th>
                                <th>STATUS</th>
                                <th>DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            {recent.map(r => (
                                <tr key={r.id}>
                                    <td style={{ color: 'var(--muted)', fontFamily: 'var(--mono)' }}>#{r.id}</td>
                                    <td>
                                        <span className={`badge ${r.category === 'child_abuse' ? 'badge-red' : 'badge-yellow'}`}>
                                            {r.category}
                                        </span>
                                    </td>
                                    <td style={{ fontFamily: 'var(--bn)', fontSize: '12px' }}>{r.district}</td>
                                    <td>
                                        <span style={{ color: SEVERITY_COLOR[r.severity], fontSize: '11px', fontWeight: 600 }}>
                                            {r.severity?.toUpperCase()}
                                        </span>
                                    </td>
                                    <td>
                                        <span style={{ color: STATUS_COLOR[r.status], fontSize: '11px' }}>
                                            {r.status?.toUpperCase()}
                                        </span>
                                    </td>
                                    <td style={{ color: 'var(--muted)', fontSize: '11px' }}>
                                        {new Date(r.created_at).toLocaleDateString('bn-BD')}
                                    </td>
                                </tr>
                            ))}
                            {recent.length === 0 && (
                                <tr>
                                    <td colSpan={6} style={{ textAlign: 'center', color: 'var(--muted)', padding: '24px' }}>
                                        কোনো রিপোর্ট নেই
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}