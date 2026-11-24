import { useState } from 'react';

const NAV = [
    { id: 'dashboard', label: 'OVERVIEW',  bn: 'সারসংক্ষেপ' },
    { id: 'reports',   label: 'REPORTS',   bn: 'রিপোর্ট'     },
    { id: 'zones',     label: 'ZONES',     bn: 'জেলা'        },
    { id: 'evidence',  label: 'EVIDENCE',  bn: 'প্রমাণ'      },
     { id: 'ngo-alerts', label: 'NGO ALERTS', bn: 'এনজিও'       },
    { id: 'rangers',    label: 'RANGERS',    bn: 'রেঞ্জার'     },
];

export default function AdminNav({ page, setPage, onLogout }) {
    return (
        <div style={{
            width:         '200px',
            minWidth:      '200px',
            height:        '100vh',
            background:    'var(--bg2)',
            borderRight:   '1px solid var(--border)',
            display:       'flex',
            flexDirection: 'column',
        }}>
            {/* Logo */}
            <div style={{
                padding:      '16px 16px',
                borderBottom: '1px solid var(--border)',
                display:      'flex',
                alignItems:   'center',
                gap:          '8px',
            }}>
                <img src="/images/bidrohi-logo.png" alt="" style={{ width: '42px', height: '42px', objectFit: 'contain' }} />
                <div>
                    <div style={{ fontFamily: 'var(--bn)', color: 'var(--maroon)', fontSize: '14px', fontWeight: 700 }}>
                        বিদ্রোহী
                    </div>
                    
                </div>
            </div>

            {/* Nav items */}
            <nav style={{ flex: 1, padding: '8px 0' }}>
                {NAV.map(item => (
                    <button key={item.id} onClick={() => setPage(item.id)} style={{
                        width:       '100%',
                        padding:     '10px 16px',
                        background:  page === item.id ? 'rgba(139,0,0,0.1)' : 'transparent',
                        border:      'none',
                        borderLeft:  page === item.id ? '2px solid var(--maroon)' : '2px solid transparent',
                        cursor:      'pointer',
                        textAlign:   'left',
                        transition:  'all 0.15s',
                        display:     'flex',
                        flexDirection:'column',
                        gap:         '2px',
                    }}
                    onMouseEnter={e => { if (page !== item.id) e.currentTarget.style.background = 'rgba(255,255,255,0.03)'; }}
                    onMouseLeave={e => { if (page !== item.id) e.currentTarget.style.background = 'transparent'; }}
                    >
                        <span style={{
                            fontSize:     '9px',
                            letterSpacing:'1.5px',
                            color:        page === item.id ? 'rgba(255,255,255,0.9)' : 'var(--muted)',
                            fontWeight:   page === item.id ? 600 : 400,
                        }}>{item.label}</span>
                        <span style={{
                            fontFamily: 'var(--bn)',
                            fontSize:   '11px',
                            color:      page === item.id ? '#cc2222' : 'rgba(255,255,255,0.2)',
                        }}>{item.bn}</span>
                    </button>
                ))}
            </nav>

            {/* Footer */}
            <div style={{ padding: '12px 16px', borderTop: '1px solid var(--border)' }}>
                <button onClick={onLogout} className="btn btn-ghost" style={{ width: '100%', fontSize: '10px', letterSpacing: '1px' }}>
                    LOGOUT
                </button>
            </div>
        </div>
    );
}