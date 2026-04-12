<!DOCTYPE html>
<html>
<head>
    <title>{{ strtoupper($serviceType) }} PIN Purchase Confirmation</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: #ffffff; padding: 35px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
        .status-badge { background-color: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; margin-top: 15px; display: inline-block; }
        .content { padding: 40px 30px; }
        .pin-container { background-color: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px 20px; text-align: center; margin-bottom: 30px; }
        .pin-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; margin-bottom: 10px; display: block; }
        .pin-value { font-family: 'Courier New', monospace; font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: 4px; display: block; margin-top: 10px; }
        .details-grid { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details-grid td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #475569; }
        .details-grid .label { font-weight: 600; color: #1e293b; width: 160px; }
        .details-grid .value { text-align: right; }
        .note { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 6px; margin-bottom: 30px; }
        .note p { margin: 0; color: #92400e; font-size: 13px; line-height: 1.5; }
        .footer { padding: 30px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 12px; }
        .footer b { color: #475569; }
        @media only screen and (max-width: 600px) {
            .content { padding: 30px 20px; }
            .pin-value { font-size: 26px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Purchase Confirmation</h1>
            <div class="status-badge">Payment Successful</div>
        </div>
        
        <div class="content">
            <p style="margin-top: 0; color: #334155; font-size: 16px;">Hello <b>{{ $customerName }}</b>,</p>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">Your educational PIN purchase was successful. Please find the details below:</p>
            
            <div class="pin-container">
                <span class="pin-label">Generated PIN / Token</span>
                <span class="pin-value">{{ $pin }}</span>
            </div>

            <table class="details-grid">
                <tr>
                    <td class="label">Service Type</td>
                    <td class="value">{{ strtoupper($serviceType) }}</td>
                </tr>
                @if($profileId)
                <tr>
                    <td class="label">Profile ID</td>
                    <td class="value">{{ $profileId }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Amount Paid</td>
                    <td class="value">₦{{ number_format($amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Reference No.</td>
                    <td class="value">{{ $reference }}</td>
                </tr>
                <tr>
                    <td class="label">Purchase Date</td>
                    <td class="value">{{ $transactionDate }}</td>
                </tr>
            </table>

            <div class="note">
                <p><b>Important Security Notice:</b> Treat this PIN like cash. Never share your PIN with anyone via SMS or unexpected phone calls. <b>Arewa Smart</b> will never ask for your PIN via support channels.</p>
            </div>
            
            <p style="text-align: center; color: #64748b; font-size: 14px;">Thank you for choosing <b>Arewa Smart</b> for your educational services.</p>
        </div>
        
        <div class="footer">
            <p><b>{{ config('app.name', 'Arewa Smart') }}</b></p>
            <p>Empowering Education through Smart Technology</p>
            <p style="margin-top: 15px;">&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>
