<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_mailer.php';
require_login();

function sw_invoice_money($amount, $currency = 'INR') {
    return htmlspecialchars($currency ?: 'INR') . ' ' . number_format((float)$amount, 2);
}

function sw_invoice_date($date) {
    if (empty($date)) return '-';
    $time = strtotime($date);
    return $time ? date('d M Y', $time) : htmlspecialchars($date);
}

function sw_invoice_status_class($status) {
    $status = strtolower((string)$status);
    if ($status === 'paid') return 'paid';
    if ($status === 'void' || $status === 'cancelled') return 'void';
    return 'pending';
}

$invoice_id = (int)($_GET['invoice_id'] ?? 0);
if (!$invoice_id) {
    echo 'Invoice id required';
    exit;
}

$stmt = $pdo->prepare(
    'SELECT i.*, o.user_id, o.total_amount, o.currency, o.status AS order_status, o.created_at AS order_created_at
     FROM invoices i
     JOIN orders o ON o.id = i.order_id
     WHERE i.id = :id
     LIMIT 1'
);
$stmt->execute([':id' => $invoice_id]);
$inv = $stmt->fetch();
if (!$inv) {
    echo 'Invoice not found';
    exit;
}

$current = current_user($pdo);
if ($current['id'] !== (int)$inv['user_id']) {
    $role = get_user_role($pdo, $current);
    if ($role !== 'admin' && $role !== 'manager') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

$userStmt = $pdo->prepare('SELECT name, username, email, company_name, address, city, state, pin_code, gst_number FROM login WHERE id = :id LIMIT 1');
$userStmt->execute([':id' => $inv['user_id']]);
$usr = $userStmt->fetch();

$itemsStmt = $pdo->prepare(
    'SELECT oi.*, hp.name AS plan_name, hp.category AS plan_category, sc.name AS service_name, sc.type AS service_type
     FROM order_items oi
     LEFT JOIN hosting_plans hp ON hp.id = oi.plan_id
     LEFT JOIN service_catalog sc ON sc.id = oi.service_id
     WHERE oi.order_id = :o'
);
$itemsStmt->execute([':o' => $inv['order_id']]);
$items = $itemsStmt->fetchAll();

$currency = $inv['currency'] ?: 'INR';

$displayInvoiceNo = !empty($inv['invoice_no']) ? $inv['invoice_no'] : 'INV-' . str_pad($inv['id'], 6, '0', STR_PAD_LEFT);

$subtotalExclTax    = 0;
$totalGstCalculated = 0;

foreach ($items as &$item) {
    $lineGross   = (float)$item['unit_price'] * (int)$item['quantity'] * max(1, (int)$item['period_months']);
    $itemGstRate = isset($item['gst_rate']) ? (float)$item['gst_rate'] : ((isset($inv['tax_rate']) ? (float)$inv['tax_rate'] : 18.00));
    $basePrice   = $lineGross / (1 + ($itemGstRate / 100));
    $gstAmount   = $lineGross - $basePrice;

    $item['_base_price'] = $basePrice;
    $item['_gst_amount'] = $gstAmount;
    $item['_gst_rate']   = $itemGstRate;

    $subtotalExclTax    += $basePrice;
    $totalGstCalculated += $gstAmount;
}
unset($item);

$amountDue     = (float)$inv['amount'];
$providerState = 'Rajasthan';
$isIntrastate  = (isset($usr['state']) && strtolower(trim($usr['state'])) === strtolower($providerState));
$statusClass   = sw_invoice_status_class($inv['status']);

$emailSent  = false;
$emailError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['send'])) {
    if ($usr && !empty($usr['email'])) {
        $host    = $_SERVER['HTTP_HOST'] ?? 'siteworx.in';
        $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $link    = $scheme . $host . dirname($_SERVER['REQUEST_URI']) . '/generate_invoice?invoice_id=' . $inv['id'];
        $subject = 'SiteWorx Tax Invoice #' . $displayInvoiceNo;
        $amountText  = $currency . ' ' . number_format($amountDue, 2);
        $clientName  = $usr['name'] ?: $usr['username'] ?: $usr['email'];
        $html   = sw_invoice_email_html($clientName, $inv['id'], $amountText, $inv['status'], $link);
        $text   = "Hello {$clientName},\n\nYour Tax Invoice #{$displayInvoiceNo} is available.\nTotal Amount: {$amountText}\nStatus: " . ucfirst((string)$inv['status']) . "\nView & Print Invoice: {$link}\n\nThank you,\nSiteWorx";
        $mailResult = sw_mail_send($usr['email'], $clientName, $subject, $html, $text);
        $emailSent  = $mailResult['success'];
        $emailError = $mailResult['error'];
    } else {
        $emailError = 'Client email address is missing.';
    }
}
?>
<?php include '_header.php'; ?>
<style>
/* ── Reset & Base ─────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

/* ── Page wrapper ─────────────────────────────────────────────── */
.sw-inv-page   { background: #f0f4f8; padding: 24px 0; min-height: 100vh; }
.sw-inv-shell  { max-width: 860px; margin: 0 auto; padding: 0 16px; }

/* ── Action bar ───────────────────────────────────────────────── */
.sw-inv-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.sw-inv-actions h1  { font-size: 20px; font-weight: 600; color: #1e293b; margin: 0 0 2px; }
.sw-inv-actions p   { font-size: 13px; color: #64748b; margin: 0; }
.sw-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 6px; font-size: 13px;
    font-weight: 500; cursor: pointer; border: none; text-decoration: none;
    transition: filter .15s;
}
.sw-btn:hover { filter: brightness(.93); }
.sw-btn-outline {
    background: #fff; color: #374151;
    border: 1px solid #d1d5db;
}
.sw-btn-primary { background: #0f1f3d; color: #fff; }

/* ── Paper card ───────────────────────────────────────────────── */
.sw-inv-paper {
    background: #fff;
    border: 1px solid #dde5f0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(15,31,61,.08);
}

/* ── Header band ──────────────────────────────────────────────── */
.sw-inv-header {
    background: #0f1f3d;
    padding: 28px 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}
.sw-inv-logo-img   { height: 40px; display: block; background: white;
    padding: 5px;
    border-radius: 30px; }
.sw-inv-logo-text  {
    font-size: 26px; font-weight: 700; color: #fff;
    letter-spacing: .4px; display: none;
}
.sw-inv-tagline    { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 6px; letter-spacing: .03em; }
.sw-inv-gstin      { font-size: 11px; color: rgba(255,255,255,.75); font-family: monospace; margin-top: 3px; }
.sw-inv-hdr-right  { text-align: right; flex-shrink: 0; }
.sw-inv-lbl        { font-size: 10px; text-transform: uppercase; letter-spacing: .09em; color: rgba(255,255,255,.5); }
.sw-inv-number     { font-size: 22px; font-weight: 600; color: #fff; letter-spacing: .02em; margin-top: 4px; }
.sw-inv-badge      {
    display: inline-block; margin-top: 10px;
    padding: 4px 12px; border-radius: 4px;
    font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .07em;
}
.sw-inv-badge.paid     { background: #22c55e; color: #fff; }
.sw-inv-badge.pending  { background: #f59e0b; color: #fff; }
.sw-inv-badge.void     { background: #94a3b8; color: #fff; }

/* ── Body ─────────────────────────────────────────────────────── */
.sw-inv-body { padding: 28px 32px; }

/* ── Meta grid (3 cols) ───────────────────────────────────────── */
.sw-inv-meta { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 26px; }
.sw-inv-meta-card {
    border: 1px solid #e8eef6; border-radius: 8px; padding: 16px;
}
.sw-inv-meta-card h6 {
    font-size: 10px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .08em; color: #94a3b8;
    border-bottom: 1px solid #f1f5f9; padding-bottom: 7px; margin: 0 0 10px;
}
.sw-inv-meta-card .mc-name  { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 3px; }
.sw-inv-meta-card .mc-sub   { font-size: 12px; color: #64748b; line-height: 1.6; }
.sw-inv-meta-card .mc-gstin { font-size: 11px; font-family: monospace; color: #0f1f3d; font-weight: 600; margin-top: 6px; }
.sw-detail-row  { display: flex; justify-content: space-between; padding: 4px 0; font-size: 12px; }
.sw-detail-row .dl { color: #94a3b8; }
.sw-detail-row .dv { font-weight: 500; color: #1e293b; }

/* ── Table ────────────────────────────────────────────────────── */
.sw-inv-table-wrap {
    border: 1px solid #e8eef6; border-radius: 8px;
    overflow: hidden; margin-bottom: 22px;
}
.sw-inv-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
.sw-inv-table thead tr { background: #f8fafc; }
.sw-inv-table th {
    padding: 10px 12px; font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .06em; color: #94a3b8;
    border-bottom: 1px solid #e8eef6; text-align: left;
}
.sw-inv-table th.tc { text-align: center; }
.sw-inv-table th.tr { text-align: right; }
.sw-inv-table td {
    padding: 13px 12px; font-size: 12px; color: #334155;
    border-bottom: 1px solid #f1f5f9; vertical-align: top;
}
.sw-inv-table tr:last-child td { border-bottom: none; }
.sw-inv-table td.tc { text-align: center; }
.sw-inv-table td.tr { text-align: right; }
.sw-item-name  { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
.sw-item-cat   { font-size: 11px; color: #94a3b8; }
.sw-item-host  { font-size: 11px; color: #64748b; font-family: monospace; margin-top: 2px; }
.sw-gst-rate   { font-size: 12px; font-weight: 600; color: #334155; }
.sw-gst-amt    { font-size: 11px; color: #94a3b8; }
.sw-line-gross { font-weight: 600; color: #1e293b; }

/* ── Totals ───────────────────────────────────────────────────── */
.sw-totals-outer { display: flex; justify-content: flex-end; margin-bottom: 24px; }
.sw-totals-card  {
    width: 300px; border: 1px solid #e8eef6;
    border-radius: 8px; overflow: hidden;
}
.sw-tot-line {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 16px; font-size: 13px;
    border-bottom: 1px solid #f1f5f9;
}
.sw-tot-line:last-child { border-bottom: none; }
.sw-tot-line .tl  { color: #64748b; }
.sw-tot-line .tv  { font-weight: 500; color: #1e293b; }
.sw-tot-line.sw-grand { background: #0f1f3d; }
.sw-tot-line.sw-grand .tl { color: rgba(255,255,255,.7); font-size: 13px; }
.sw-tot-line.sw-grand .tv { color: #fff; font-size: 17px; font-weight: 600; }

/* ── Footer note ──────────────────────────────────────────────── */
.sw-inv-footer {
    display: flex; align-items: flex-start;
    gap: 20px; border-top: 1px solid #f1f5f9; padding-top: 20px;
}
.sw-inv-note {
    flex: 1; background: #f8fafc; border: 1px solid #e8eef6;
    border-radius: 8px; padding: 14px 16px;
    font-size: 12px; color: #64748b; line-height: 1.7;
}
.sw-inv-note strong { color: #1e293b; }
.sw-inv-contact { text-align: right; font-size: 12px; color: #64748b; line-height: 1.8; flex-shrink: 0; }
.sw-inv-contact strong { color: #1e293b; display: block; }

/* ── Print ────────────────────────────────────────────────────── */
@page { size: A4; margin: 12mm; }
@media print {
    header, nav, .sw-inv-actions, .alert { display: none !important; }
    body, .sw-inv-page { background: #fff !important; }
    .sw-inv-shell { padding: 0 !important; max-width: none !important; }
    .sw-inv-paper { box-shadow: none !important; border: none !important; }
    .sw-inv-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .sw-inv-badge, .sw-totals-card .sw-grand { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .sw-inv-body { padding: 20px 24px !important; }
}
</style>

<?php if ($emailSent): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    Tax invoice sent successfully to <strong><?php echo htmlspecialchars($usr['email']); ?></strong>.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
<?php if (!$emailSent && $emailError): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($emailError); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="sw-inv-page">
  <div class="sw-inv-shell">

    <!-- Action Bar -->
    <div class="sw-inv-actions">
      <div>
        <h1>Tax Invoice</h1>
        <p>Reference: <?php echo htmlspecialchars($displayInvoiceNo); ?></p>
      </div>
      <div style="display:flex;gap:10px;align-items:center;">
        <button class="sw-btn sw-btn-outline" onclick="window.print()">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
          Print / Save PDF
        </button>
        <form method="post" action="generate_invoice?invoice_id=<?php echo (int)$inv['id']; ?>" style="margin:0;">
          <button type="submit" class="sw-btn sw-btn-primary">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Resend Invoice Email
          </button>
        </form>
      </div>
    </div>

    <!-- Invoice Paper -->
    <div class="sw-inv-paper">

      <!-- Header -->
      <div class="sw-inv-header">
        <div>
          <img
            src="https://siteworx.in/images/logofoot.png"
            alt="SiteWorx"
            class="sw-inv-logo-img"
            onerror="this.style.display='none';document.getElementById('sw-logo-fallback').style.display='block';"
          >
          <span id="sw-logo-fallback" class="sw-inv-logo-text">SiteWorx</span>
          <div class="sw-inv-tagline">Web Hosting, Infrastructure &amp; Cloud Provisioning</div>
          <div class="sw-inv-gstin">GSTIN: 08CHLPP6582J1ZJ</div>
        </div>
        <div class="sw-inv-hdr-right">
          <div class="sw-inv-lbl">Tax Invoice</div>
          <div class="sw-inv-number"><?php echo htmlspecialchars($displayInvoiceNo); ?></div>
          <span class="sw-inv-badge <?php echo $statusClass; ?>">
            <?php echo ucfirst(htmlspecialchars($inv['status'])); ?>
          </span>
        </div>
      </div>

      <!-- Body -->
      <div class="sw-inv-body">

        <!-- Meta grid -->
        <div class="sw-inv-meta">

          <!-- Billed To -->
          <div class="sw-inv-meta-card">
            <h6>Billed To</h6>
            <div class="mc-name"><?php echo htmlspecialchars($usr['company_name'] ?: $usr['name'] ?: $usr['username']); ?></div>
            <?php if (!empty($usr['company_name'])): ?>
              <div class="mc-sub"><?php echo htmlspecialchars($usr['name']); ?></div>
            <?php endif; ?>
            <div class="mc-sub">
              <?php echo nl2br(htmlspecialchars($usr['address'] ?? '')); ?><br>
              <?php echo htmlspecialchars(implode(', ', array_filter([
                $usr['city']     ?? '',
                $usr['state']    ?? '',
                $usr['pin_code'] ?? '',
              ]))); ?><br>
              India
            </div>
            <div class="mc-sub" style="margin-top:5px;"><?php echo htmlspecialchars($usr['email'] ?? ''); ?></div>
            <?php if (!empty($usr['gst_number'])): ?>
              <div class="mc-gstin">GSTIN: <?php echo htmlspecialchars($usr['gst_number']); ?></div>
            <?php endif; ?>
          </div>

          <!-- Supplier -->
          <div class="sw-inv-meta-card">
            <h6>Supplier</h6>
            <div class="mc-name">Site Worx Infotech</div>
            <div class="mc-sub">
              305, Maa Hinglaaj Nagar<br>
              Lalerpura Road, Vaishali Nagar<br>
              Jaipur, Rajasthan — 302021<br>
              India
            </div>
            <div class="mc-sub" style="margin-top:5px;">billing@siteworx.in</div>
          </div>

          <!-- Invoice Details -->
          <div class="sw-inv-meta-card">
            <h6>Invoice Details</h6>
            <div class="sw-detail-row">
              <span class="dl">Invoice Date</span>
              <span class="dv"><?php echo sw_invoice_date($inv['issued_at'] ?? $inv['order_created_at']); ?></span>
            </div>
            <div class="sw-detail-row">
              <span class="dl">Due Date</span>
              <span class="dv"><?php echo !empty($inv['due_date'])
                ? sw_invoice_date($inv['due_date'])
                : sw_invoice_date(date('Y-m-d', strtotime('+7 days', strtotime($inv['issued_at'] ?? $inv['order_created_at'])))); ?></span>
            </div>
            <div class="sw-detail-row">
              <span class="dl">Order ID</span>
              <span class="dv">#<?php echo (int)$inv['order_id']; ?></span>
            </div>
            <div class="sw-detail-row">
              <span class="dl">Place of Supply</span>
              <span class="dv"><?php echo htmlspecialchars($usr['state'] ?: 'Interstate'); ?></span>
            </div>
            <div class="sw-detail-row">
              <span class="dl">GST Type</span>
              <span class="dv"><?php echo $isIntrastate ? 'CGST + SGST' : 'IGST (Interstate)'; ?></span>
            </div>
            <div class="sw-detail-row">
              <span class="dl">Currency</span>
              <span class="dv"><?php echo htmlspecialchars($currency); ?></span>
            </div>
          </div>

        </div><!-- /meta grid -->

        <!-- Line Items Table -->
        <div class="sw-inv-table-wrap">
          <table class="sw-inv-table">
            <thead>
              <tr>
                <th style="width:33%;">Item Description</th>
                <th class="tc" style="width:10%;">Period</th>
                <th class="tc" style="width:7%;">Qty</th>
                <th class="tr" style="width:16%;">Taxable Value</th>
                <th class="tc" style="width:17%;">GST</th>
                <th class="tr" style="width:17%;">Gross Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it):
                $meta        = !empty($it['meta']) ? json_decode($it['meta'], true) : [];
                if (!is_array($meta)) $meta = [];
                $itemName    = $it['plan_name']    ?: ($it['service_name'] ?: ($meta['hostname'] ?? 'Service'));
                $itemCategory = $it['plan_category'] ?: ($it['service_type'] ?: (!empty($meta['hostname']) ? 'Server Infra' : 'General'));
                $lineGross   = (float)$it['unit_price'] * (int)$it['quantity'] * max(1, (int)$it['period_months']);
              ?>
              <tr>
                <td>
                  <div class="sw-item-name"><?php echo htmlspecialchars($itemName); ?></div>
                  <div class="sw-item-cat">Category: <?php echo htmlspecialchars($itemCategory); ?></div>
                  <?php if (!empty($meta['hostname'])): ?>
                    <div class="sw-item-host">Host: <?php echo htmlspecialchars($meta['hostname']); ?></div>
                  <?php endif; ?>
                </td>
                <td class="tc"><?php echo (int)$it['period_months']; ?> Mth(s)</td>
                <td class="tc"><?php echo (int)$it['quantity']; ?></td>
                <td class="tr"><?php echo sw_invoice_money($it['_base_price'], $currency); ?></td>
                <td class="tc">
                  <div class="sw-gst-rate"><?php echo number_format($it['_gst_rate'], 1); ?>%</div>
                  <div class="sw-gst-amt"><?php echo sw_invoice_money($it['_gst_amount'], $currency); ?></div>
                </td>
                <td class="tr sw-line-gross"><?php echo sw_invoice_money($lineGross, $currency); ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$items): ?>
                <tr>
                  <td colspan="6" style="text-align:center;color:#94a3b8;padding:28px;">
                    No invoice line items found.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Totals -->
        <div class="sw-totals-outer">
          <div class="sw-totals-card">
            <div class="sw-tot-line">
              <span class="tl">Subtotal (excl. tax)</span>
              <span class="tv"><?php echo sw_invoice_money($subtotalExclTax, $currency); ?></span>
            </div>

            <?php if ($currency === 'INR'): ?>
              <?php if ($isIntrastate): ?>
                <div class="sw-tot-line">
                  <span class="tl">CGST (<?php echo number_format($inv['tax_rate'] / 2, 2); ?>%)</span>
                  <span class="tv"><?php echo sw_invoice_money($totalGstCalculated / 2, $currency); ?></span>
                </div>
                <div class="sw-tot-line">
                  <span class="tl">SGST (<?php echo number_format($inv['tax_rate'] / 2, 2); ?>%)</span>
                  <span class="tv"><?php echo sw_invoice_money($totalGstCalculated / 2, $currency); ?></span>
                </div>
              <?php else: ?>
                <div class="sw-tot-line">
                  <span class="tl">IGST (<?php echo number_format($inv['tax_rate'] ?? 18, 2); ?>%)</span>
                  <span class="tv"><?php echo sw_invoice_money($totalGstCalculated, $currency); ?></span>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <div class="sw-tot-line">
                <span class="tl">Total Tax</span>
                <span class="tv"><?php echo sw_invoice_money($totalGstCalculated, $currency); ?></span>
              </div>
            <?php endif; ?>

            <div class="sw-tot-line sw-grand">
              <span class="tl">Grand Total Due</span>
              <span class="tv"><?php echo sw_invoice_money($amountDue, $currency); ?></span>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="sw-inv-footer">
          <div class="sw-inv-note">
            <strong>Declarations &amp; Terms</strong><br>
            This is a computer-generated tax invoice and does not require a physical signature.
            Services once provisioned are governed by our standard SLA. Please clear all dues
            before the mapped expiry interval to avoid service interruption.
          </div>
          <div class="sw-inv-contact">
            <strong>SiteWorx Tech Support</strong>
            support@siteworx.in<br>
            www.siteworx.in
          </div>
        </div>

      </div><!-- /body -->
    </div><!-- /paper -->
  </div><!-- /shell -->
</div><!-- /page -->

<?php include '_footer.php'; ?>