import { useLeaderboard } from '../../hooks/useLeaderboard';
import RankBadge from '../shared/RankBadge';

export default function LeaderboardPanel() {
    const { rangers, loading } = useLeaderboard();

    return (
        <div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
            <div style={{
                padding:      '14px 16px',
                borderBottom: '1px solid var(--border)',
                display:      'flex',
                alignItems:   'center',
                justifyContent:'space-between',
            }}>
                <span style={{
                    fontFamily:  'var(--font-bn)',
                    color:       'var(--gold)',
                    fontWeight:  700,
                    fontSize:    '14px',
                }}>শীর্ষ রেঞ্জার</span>
                <span style={{
                    fontFamily: 'var(--font-mono)',
                    color:      'var(--text-muted)',
                    fontSize:   '10px',
                }}>GLOBAL TOP 10</span>
            </div>

            <div style={{ overflowY: 'auto', flex: 1 }}>
                {loading ? (
                    <div style={{ padding: '20px', textAlign: 'center', color: 'var(--text-muted)', fontFamily: 'var(--font-mono)', fontSize: '12px' }}>
                        LOADING...
                    </div>
                ) : rangers.slice(0, 10).map((ranger, i) => (
                    <div key={i} style={{
                        padding:      '10px 16px',
                        borderBottom: '1px solid var(--border)',
                        display:      'flex',
                        alignItems:   'center',
                        gap:          '10px',
                        transition:   'background 0.2s',
                        cursor:       'default',
                    }}
                    onMouseEnter={e => e.currentTarget.style.background = 'var(--dark-3)'}
                    onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
                    >
                        {/* Position */}
                        <span style={{
                            fontFamily: 'var(--font-mono)',
                            fontSize:   '13px',
                            color:      i < 3 ? 'var(--gold)' : 'var(--text-muted)',
                            width:      '20px',
                            flexShrink: 0,
                        }}>#{i + 1}</span>

                        {/* Info */}
                        <div style={{ flex: 1, minWidth: 0 }}>
                            <div style={{
                                fontFamily:   'var(--font-main)',
                                fontSize:     '14px',
                                fontWeight:   600,
                                color:        'var(--text)',
                                whiteSpace:   'nowrap',
                                overflow:     'hidden',
                                textOverflow: 'ellipsis',
                            }}>{ranger.username}</div>
                            <div style={{
                                fontFamily: 'var(--font-bn)',
                                fontSize:   '11px',
                                color:      'var(--text-muted)',
                            }}>{ranger.district}</div>
                        </div>

                        {/* Rank badge */}
                        <RankBadge rank={ranger.rank} points={ranger.points} />
                    </div>
                ))}
            </div>
        </div>
    );
}