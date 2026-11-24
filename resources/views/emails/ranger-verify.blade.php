<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8"/>
    <style>
        body {
            background: #080c10;
            color: #e6edf3;
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 540px;
            margin: 40px auto;
            background: #0d1117;
            border: 1px solid #21262d;
            border-top: 2px solid #8B0000;
            border-radius: 4px;
            overflow: hidden;
        }
        .header {
            padding: 28px 32px;
            border-bottom: 1px solid #21262d;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #8B0000;
            letter-spacing: 2px;
        }
        .sub {
            font-size: 10px;
            color: #7d8590;
            letter-spacing: 2px;
            margin-top: 2px;
        }
        .body {
            padding: 32px;
        }
        .greeting {
            font-size: 13px;
            color: #7d8590;
            margin-bottom: 16px;
            letter-spacing: 1px;
        }
        .username {
            color: #e6edf3;
            font-weight: 600;
        }
        .message {
            font-size: 13px;
            color: #7d8590;
            line-height: 1.8;
            margin-bottom: 28px;
        }
        .bn-message {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #e6edf3;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 12px 28px;
            background: rgba(139,0,0,0.15);
            border: 1px solid rgba(139,0,0,0.5);
            color: #cc2222;
            text-decoration: none;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            letter-spacing: 2px;
            border-radius: 2px;
        }
        .url-box {
            margin-top: 20px;
            padding: 10px 14px;
            background: #080c10;
            border: 1px solid #21262d;
            border-radius: 2px;
            font-size: 11px;
            color: #7d8590;
            word-break: break-all;
        }
        .warning {
            margin-top: 20px;
            padding: 10px 14px;
            background: rgba(139,0,0,0.05);
            border: 1px solid rgba(139,0,0,0.15);
            border-radius: 2px;
            font-size: 11px;
            color: #7d8590;
        }
        .footer {
            padding: 16px 32px;
            border-top: 1px solid #21262d;
            font-size: 10px;
            color: #484f58;
            letter-spacing: 1px;
        }
        .tagline {
            font-style: italic;
            color: #8B0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="logo-text">বিদ্রোহী</div>
                <div class="sub">BIDROHI INTELLIGENCE PLATFORM</div>
            </div>
        </div>

        <!-- Body -->
        <div class="body">
            <div class="greeting">
                RANGER REGISTRATION — <span class="username">{{ $user->username }}</span>
            </div>

            <div class="bn-message">আপনার ইমেইল যাচাই করুন</div>

            <div class="message">
                You have registered as a Ranger on BIDROHI — Bangladesh's anonymous crime witness and corruption intelligence platform.<br/><br/>
                Click the button below to verify your email and activate your account. This link expires in <strong style="color:#e6edf3">24 hours</strong>.
            </div>

            <a href="{{ $verificationUrl }}" class="btn">
                VERIFY EMAIL →
            </a>

            <div class="url-box">
                If the button doesn't work, copy this link:<br/>
                {{ $verificationUrl }}
            </div>

            <div class="warning">
                If you did not register on BIDROHI, ignore this email. Your address will not be used.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            BIDROHI · Samantha · SPRING 2026<br/>
            <span class="tagline">"Not violence. Not protest. Evidence."</span>
        </div>
    </div>
</body>
</html>