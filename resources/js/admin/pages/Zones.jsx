import { useEffect, useState } from 'react';
import { getZones } from '../services/adminApi';

export default function Zones() {
    const [zones,  setZones]  = useState([]);
    const [filter, setFilter] = useState('all');

    useEffect(() => {
        getZones().then(r => setZones(r.data.data));
    }, []);

    const STATUS_COLOR = { red: '#cc2222', yellow: '#ffb800', green: '#00e676' };

    const filtered = filter === 'all' ? zones : zones.filter(z => z.status === filter);
    const sorted   = [...filtered].sort((a, b) => b.score - a.score);

    return (
        <div style={{ padding: '24px', overflowY: 'auto', height: '100%' }} className="fade-in">
            <div style={{ marginBottom: '20px', display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }}>
                <div>
                    <div style={{ fontSize: '10px', color: 'var(--muted)', letterSpacing: '2px', marginBottom: '4px' }}>ZONE CONTROL</div>
                    <div style={{ fontFamily: 'var(--bn)', fontSize: '18px', fontWeight: 700, color: 'var(--maroon)' }}>জেলা পর্যবেক্ষণ</div>
                </div>
                <div style={{ display: 'flex', gap: '6px' }}>
                    {['all', 'red', 'yellow', 'green'].map(f => (
                        <button key={f} className="btn btn-ghost"
                            onClick={() => setFilter(f)}
                            style={{
                                fontSize:    '9px',
                                letterSpacing:'1px',
                                color:       filter === f ? 'var(--text)' : 'var(--muted)',
                                borderColor: filter === f ? 'rgba(255,255,255,0.2)' : 'var(--border)',
                            }}>
                            {f.toUpperCase()}
                        </button>
                    ))}
                </div>
            </div>

            <div style={{
                display:             'grid',
                gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))',
                gap:                 '1px',
                background:          'var(--border)',
                border:              '1px solid var(--border)',
            }}>
                {sorted.map(zone => {
                    const color = STATUS_COLOR[zone.status];
                    return (
                        <div key={zone.id} style={{
                            background: 'var(--bg2)',
                            padding:    '14px 16px',
                            borderLeft: `2px solid ${color}`,
                        }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                                <div>
                                    <div style={{ fontFamily: 'var(--bn)', fontSize: '13px', fontWeight: 600, color: '(231,76,60,0.1)' }}>
                                        {zone.name_bn}
                                    </div>
                                    <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '0.5px', marginTop: '2px' }}>
                                        {zone.division}
                                    </div>
                                </div>
                                <div style={{ fontSize: '20px', fontWeight: 700, color, lineHeight: 1 }}>
                                    {zone.score}
                                </div>
                            </div>
                            <div style={{ height: '2px', background: 'rgba(255,255,255,255)', borderRadius: '1px', marginBottom: '8px' }}>
                                <div style={{ width: `${zone.score}%`, height: '100%', background: color, borderRadius: '1px' }} />
                            </div>
                            <div style={{ display: 'flex', gap: '12px', fontSize: '10px', color: 'var(--muted)' }}>
                                <span>RPT <span style={{ color: 'rgba(255,255,255,255)' }}>{zone.report_count}</span></span>
                                <span>VRF <span style={{ color: '#00e676' }}>{zone.verified_count}</span></span>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}