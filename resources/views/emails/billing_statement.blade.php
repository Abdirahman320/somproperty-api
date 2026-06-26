<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;background:#f7f8fc;font-family:Arial,sans-serif">
<div style="max-width:580px;margin:0 auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e2e7ef">
  <div style="background:#0f1f3d;padding:28px 30px;text-align:center">
    <div style="color:#f0a500;font-size:22px;font-weight:700">{{ $bill->owner->company_name ?? 'Property Management' }}</div>
    <div style="color:rgba(255,255,255,.65);font-size:13px;margin-top:4px">Billing Statement — {{ $bill->billing_month->format('F Y') }}</div>
  </div>
  <div style="padding:26px 30px">
    <p>Dear <b>{{ $bill->tenant->full_name }}</b>,</p>
    <p>Your billing statement for <b>{{ $bill->billing_month->format('F Y') }}</b> is now ready.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
      <tr style="background:#f7f8fc"><th style="padding:9px;text-align:left;font-size:12px;color:#6b7a8d">Description</th><th style="padding:9px;text-align:right;font-size:12px;color:#6b7a8d">Amount</th></tr>
      <tr><td style="padding:10px;border-bottom:1px solid #eee">Monthly Rent</td><td style="padding:10px;text-align:right;border-bottom:1px solid #eee"><b>${{ number_format($bill->rent_amount,2) }}</b></td></tr>
      @if($bill->water_amount>0)<tr><td style="padding:10px;border-bottom:1px solid #eee">Water ({{ number_format($bill->water_consumption,2) }} m³)</td><td style="padding:10px;text-align:right;border-bottom:1px solid #eee;color:#3b82f6"><b>${{ number_format($bill->water_amount,2) }}</b></td></tr>@endif
      @if($bill->electric_amount>0)<tr><td style="padding:10px;border-bottom:1px solid #eee">Electricity ({{ number_format($bill->electric_consumption,2) }} kWh)</td><td style="padding:10px;text-align:right;border-bottom:1px solid #eee;color:#f0a500"><b>${{ number_format($bill->electric_amount,2) }}</b></td></tr>@endif
      <tr style="background:#0f1f3d"><td style="padding:12px;color:#fff;font-weight:700">TOTAL DUE</td><td style="padding:12px;text-align:right;color:#f0a500;font-weight:700;font-size:16px">${{ number_format($bill->total_amount,2) }}</td></tr>
    </table>
    <p><b>Due Date:</b> {{ $bill->due_date->format('F j, Y') }}</p>
    <div style="text-align:center;margin:24px 0">
      <a href="{{ url('/tenant/billing') }}" style="background:#0f1f3d;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700">View Statement & Pay Online →</a>
    </div>
  </div>
  <div style="background:#f7f8fc;padding:16px;text-align:center;font-size:11px;color:#8895a7">
    {{ $bill->owner->company_name }} · Powered by SOM Property Management
  </div>
</div>
</body></html>
