import { useState, useEffect } from 'react';
import './styles/admin.css';
import { checkSession, adminLogout } from './services/adminApi';
import Dashboard  from './pages/Dashboard';
import Reports    from './pages/Reports';
import Zones      from './pages/Zones';
import Evidence   from './pages/Evidence';
import NgoAlerts  from './pages/NgoAlerts';
import Rangers    from './pages/Rangers';
import AdminNav   from './components/AdminNav';

export default function AdminApp() {
    const [auth, setAuth] = useState(null);
    const [page, setPage] = useState('dashboard');

    useEffect(() => {
        checkSession()
            .then(() => setAuth(true))
            .catch(() => { window.location.href = '/'; });
    }, []);

    const logout = async () => {
        await adminLogout().catch(() => {});
        window.location.href = '/';
    };

    if (auth === null) return (
        <div style={{
            height: '100vh', display: 'flex',
            alignItems: 'center', justifyContent: 'center',
            background: 'var(--bg)', flexDirection: 'column', gap: '12px',
        }}>
            <img src="/images/bidrohi-logo.png" alt="" style={{ width: '36px', opacity: 0.5 }} />
            <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '3px' }}>
                VERIFYING SESSION...
            </div>
        </div>
    );

    const PAGES = {
        dashboard:  <Dashboard />,
        reports:    <Reports />,
        zones:      <Zones />,
        evidence:   <Evidence />,
        'ngo-alerts': <NgoAlerts />,
        rangers:    <Rangers />,
    };

    return (
        <div style={{ display: 'flex', height: '100vh', overflow: 'hidden' }}>
            <AdminNav page={page} setPage={setPage} onLogout={logout} />
            <main style={{ flex: 1, overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
                <div style={{
                    height: '38px', borderBottom: '1px solid var(--border)',
                    display: 'flex', alignItems: 'center',
                    padding: '0 20px', background: 'var(--bg2)',
                    flexShrink: 0, gap: '8px',
                }}>
                    <span style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '1.5px' }}>
                        BIDROHI ADMIN
                    </span>
                    <span style={{ color: 'var(--border)' }}>·</span>
                    <span style={{ fontSize: '9px', color: 'var(--maroon)', letterSpacing: '1.5px' }}>
                        {page.toUpperCase()}
                    </span>
                    <div style={{ flex: 1 }} />
                    <span style={{ fontSize: '9px', color: 'rgba(255,255,255,0.15)' }}>
                        SECURE SESSION ACTIVE
                    </span>
                    <span style={{ width: 5, height: 5, borderRadius: '50%', background: '#00e676', display: 'block' }} />
                </div>
                <div style={{ flex: 1, overflow: 'hidden' }}>
                    {PAGES[page]}
                </div>
            </main>
        </div>
    );
}