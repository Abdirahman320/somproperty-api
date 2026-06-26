<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
  body{font-family:Arial,sans-serif;color:#0f1f3d;font-size:13px;margin:0;padding:0}
  .header{background:#0f1f3d;color:#fff;padding:28px 32px;display:flex;justify-content:space-between;align-items:center}
  .logo{font-size:20px;font-weight:700}.logo span{color:#f0a500}
  .bill-no{text-align:right;font-size:12px;opacity:.7}
  .body{padding:28px 32px}
  .to{margin-bottom:22px}
  .to h3{font-size:12px;color:#6b7a8d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
  table{width:100%;border-collapse:collapse;margin:16px 0}
  th{background:#f7f8fc;padding:9px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7a8d;border-bottom:2px solid #e2e7ef}
  td{padding:10px 12px;border-bottom:1px solid #e2e7ef}
  .total-row td{background:#0f1f3d;color:#fff;font-weight:700;font-size:15px}
  .total-row td:last-child{color:#f0a500}
  .footer{background:#f7f8fc;padding:16px 32px;font-size:11px;color:#6b7a8d;text-align:center;margin-top:24px}
</style></head><body>
<div class="header">
  <div class="logo">{{ $bill->owner->company_name ?? $bill->owner->full_name }}<br><span style="font-size:11px;opacity:.6;font-weight:400">Property Management</span></div>
  <div class="bill-no"><b style="color:#f0a500;font-size:16px">BILLING STATEMENT</b><br>{{ $bill->billing_month->format('F Y') }}<br>Ticket: BILL-{{ str_pad($bill->id,6,'0',STR_PAD_LEFT) }}</div>
</div>
<div class="body">
  <div class="to">
    <h3>Billed To</h3>
    <b>{{ $bill->tenant->full_name }}</b><br>
    Unit {{ $bill->unit->unit_number }}, {{ $bill->unit->property->name }}<br>
    {{ $bill->tenant->email }}
  </div>
  <table>
    <thead><tr><th>Description</th><th style="text-align:right">Amount</th></tr></thead>
    <tbody>
      <tr><td>Monthly Rent — {{ $bill->billing_month->format('F Y') }}</td><td style="text-align:right">${{ number_format($bill->rent_amount,2) }}</td></tr>
      @if($bill->water_amount>0)<tr><td>Water ({{ number_format($bill->water_consumption,2) }} m³ × ${{ $bill->water_rate }})</td><td style="text-align:right">${{ number_format($bill->water_amount,2) }}</td></tr>@endif
      @if($bill->electric_amount>0)<tr><td>Electricity ({{ number_format($bill->electric_consumption,2) }} kWh × ${{ $bill->electric_rate }})</td><td style="text-align:right">${{ number_format($bill->electric_amount,2) }}</td></tr>@endif
      @if($bill->late_fee>0)<tr><td>Late Fee</td><td style="text-align:right;color:#ff4d6d">${{ number_format($bill->late_fee,2) }}</td></tr>@endif
      <tr class="total-row"><td>TOTAL DUE</td><td style="text-align:right">${{ number_format($bill->total_amount,2) }}</td></tr>
    </tbody>
  </table>
  <p><b>Due Date:</b> {{ $bill->due_date->format('F j, Y') }} &nbsp;|&nbsp; <b>Status:</b> {{ ucfirst($bill->status) }}</p>
  @if($bill->amount_paid>0)<p><b>Amount Paid:</b> ${{ number_format($bill->amount_paid,2) }} &nbsp;|&nbsp; <b>Balance Due:</b> ${{ number_format($bill->balance_due,2) }}</p>@endif
</div>
<div class="footer">{{ $bill->owner->company_name }} · Powered by SOM Property Management · Generated {{ now()->format('M j, Y H:i') }}</div>
</body></html>
