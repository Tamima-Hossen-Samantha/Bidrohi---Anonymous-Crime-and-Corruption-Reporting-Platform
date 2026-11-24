export default function Evidence() {
    return (
        <div style={{ padding: '24px', height: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', flexDirection: 'column', gap: '12px' }} className="fade-in">
            <div style={{ fontSize: '9px', color: 'var(--muted)', letterSpacing: '2px' }}>EVIDENCE VAULT</div>
            <div style={{ fontFamily: 'var(--bn)', fontSize: '18px', color: 'var(--maroon)', fontWeight: 700 }}>প্রমাণ ভান্ডার</div>
            <div style={{ fontSize: '11px', color: 'var(--muted)', textAlign: 'center', maxWidth: '300px', lineHeight: 1.6 }}>
                Evidence files are AES-256 encrypted. Access requires selecting a report from the Reports panel and requesting decryption with admin credentials.
            </div>
            <div style={{
                marginTop:    '16px',
                padding:      '10px 20px',
                background:   'var(--maroon2)',
                border:       '1px solid rgba(139,0,0,0.3)',
                borderRadius: '2px',
                fontSize:     '10px',
                color:        '#cc2222',
                letterSpacing:'1px',
            }}>
                🔒 VAULT LOCKED — SELECT A REPORT TO ACCESS
            </div>
        </div>
    );
}