<?php

require_once __DIR__ . '/mail_config.php';

function sw_mail_header_text($value) {
    return trim(str_replace(["\r", "\n"], '', (string)$value));
}

function sw_mail_address($email, $name = '') {
    $email = sw_mail_header_text($email);
    $name = sw_mail_header_text($name);
    if ($name === '') {
        return '<' . $email . '>';
    }
    $name = addcslashes($name, '"\\');
    return '"' . $name . '" <' . $email . '>';
}

function sw_mail_configured() {
    return defined('SW_SMTP_HOST')
        && trim((string)SW_SMTP_HOST) !== ''
        && defined('SW_SMTP_USERNAME')
        && trim((string)SW_SMTP_USERNAME) !== ''
        && defined('SW_SMTP_PASSWORD')
        && trim((string)SW_SMTP_PASSWORD) !== '';
}

function sw_mail_smtp_read($socket) {
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }
    return $response;
}

function sw_mail_smtp_expect($socket, $codes, &$lastResponse) {
    $lastResponse = sw_mail_smtp_read($socket);
    $code = (int)substr($lastResponse, 0, 3);
    return in_array($code, (array)$codes, true);
}

function sw_mail_smtp_command($socket, $command, $codes, &$lastResponse) {
    fwrite($socket, $command . "\r\n");
    return sw_mail_smtp_expect($socket, $codes, $lastResponse);
}

function sw_mail_smtp_data($body) {
    $body = str_replace(["\r\n", "\r"], "\n", $body);
    $lines = explode("\n", $body);
    foreach ($lines as &$line) {
        if (isset($line[0]) && $line[0] === '.') {
            $line = '.' . $line;
        }
    }
    return implode("\r\n", $lines);
}

function sw_mail_send($toEmail, $toName, $subject, $htmlBody, $textBody = '') {
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid recipient email address.'];
    }

    if (!sw_mail_configured()) {
        return ['success' => false, 'error' => 'SMTP is not configured. Please update Admin_SiteWorx/mail_config.php with company mail settings.'];
    }

    $host = trim((string)SW_SMTP_HOST);
    $port = (int)SW_SMTP_PORT;
    $secure = strtolower(trim((string)SW_SMTP_SECURE));
    $timeout = defined('SW_SMTP_TIMEOUT') ? (int)SW_SMTP_TIMEOUT : 20;
    $fromEmail = SW_MAIL_FROM_EMAIL;
    $fromName = SW_MAIL_FROM_NAME;
    $replyTo = SW_MAIL_REPLY_TO;
    $username = SW_SMTP_USERNAME;
    $password = SW_SMTP_PASSWORD;
    $remote = ($secure === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $errno = 0;
    $errstr = '';
    $lastResponse = '';

    $socket = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        return ['success' => false, 'error' => 'SMTP connection failed: ' . $errstr];
    }
    stream_set_timeout($socket, $timeout);

    if (!sw_mail_smtp_expect($socket, 220, $lastResponse)) {
        fclose($socket);
        return ['success' => false, 'error' => 'SMTP greeting failed: ' . trim($lastResponse)];
    }

    $serverName = $_SERVER['SERVER_NAME'] ?? 'siteworx.in';
    if (!sw_mail_smtp_command($socket, 'EHLO ' . $serverName, 250, $lastResponse)) {
        fclose($socket);
        return ['success' => false, 'error' => 'SMTP EHLO failed: ' . trim($lastResponse)];
    }

    if ($secure === 'tls') {
        if (!sw_mail_smtp_command($socket, 'STARTTLS', 220, $lastResponse)) {
            fclose($socket);
            return ['success' => false, 'error' => 'SMTP STARTTLS failed: ' . trim($lastResponse)];
        }
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return ['success' => false, 'error' => 'SMTP TLS encryption failed.'];
        }
        if (!sw_mail_smtp_command($socket, 'EHLO ' . $serverName, 250, $lastResponse)) {
            fclose($socket);
            return ['success' => false, 'error' => 'SMTP EHLO after TLS failed: ' . trim($lastResponse)];
        }
    }

    if (!sw_mail_smtp_command($socket, 'AUTH LOGIN', 334, $lastResponse)
        || !sw_mail_smtp_command($socket, base64_encode($username), 334, $lastResponse)
        || !sw_mail_smtp_command($socket, base64_encode($password), 235, $lastResponse)) {
        fclose($socket);
        return ['success' => false, 'error' => 'SMTP authentication failed: ' . trim($lastResponse)];
    }

    if (!sw_mail_smtp_command($socket, 'MAIL FROM:<' . $fromEmail . '>', 250, $lastResponse)
        || !sw_mail_smtp_command($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251], $lastResponse)
        || !sw_mail_smtp_command($socket, 'DATA', 354, $lastResponse)) {
        fclose($socket);
        return ['success' => false, 'error' => 'SMTP envelope failed: ' . trim($lastResponse)];
    }

    $boundary = 'swx_' . bin2hex(random_bytes(12));
    if ($textBody === '') {
        $textBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody)));
    }

    $headers = [
        'Date: ' . date('r'),
        'From: ' . sw_mail_address($fromEmail, $fromName),
        'Reply-To: ' . sw_mail_address($replyTo, $fromName),
        'To: ' . sw_mail_address($toEmail, $toName),
        'Subject: ' . sw_mail_header_text($subject),
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
    ];

    $message = implode("\r\n", $headers) . "\r\n\r\n";
    $message .= '--' . $boundary . "\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $textBody . "\r\n\r\n";
    $message .= '--' . $boundary . "\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $htmlBody . "\r\n\r\n";
    $message .= '--' . $boundary . "--\r\n";

    fwrite($socket, sw_mail_smtp_data($message) . "\r\n.\r\n");
    if (!sw_mail_smtp_expect($socket, 250, $lastResponse)) {
        fclose($socket);
        return ['success' => false, 'error' => 'SMTP message rejected: ' . trim($lastResponse)];
    }

    sw_mail_smtp_command($socket, 'QUIT', 221, $lastResponse);
    fclose($socket);
    return ['success' => true, 'error' => ''];
}

function sw_invoice_email_html($clientName, $invoiceId, $amountText, $status, $invoiceLink) {
    $clientName = htmlspecialchars($clientName ?: 'Client');
    $invoiceId = (int)$invoiceId;
    $amountText = htmlspecialchars($amountText);
    $status = htmlspecialchars(ucfirst((string)$status));
    $invoiceLink = htmlspecialchars($invoiceLink);

    return '<!doctype html>
<html>
<body style="margin:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:28px 0;">
    <tr>
      <td align="center">
        <table width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #dfe7f1;">
          <tr>
            <td style="background:#0f5f73;padding:26px 30px;color:#ffffff;">
              <div style="font-size:26px;font-weight:700;">SiteWorx</div>
              <div style="font-size:13px;opacity:.85;">Web Hosting, Cloud, Email and Business Services</div>
            </td>
          </tr>
          <tr>
            <td style="padding:30px;">
              <h2 style="margin:0 0 12px;color:#0f5f73;">Invoice #' . $invoiceId . ' is ready</h2>
              <p style="margin:0 0 18px;line-height:1.6;">Hello ' . $clientName . ', your SiteWorx invoice has been generated. You can review, print, or save the invoice from your account.</p>
              <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e4eaf2;border-radius:8px;margin:20px 0;">
                <tr>
                  <td style="padding:14px 16px;color:#6b7280;">Amount Due</td>
                  <td align="right" style="padding:14px 16px;font-weight:700;">' . $amountText . '</td>
                </tr>
                <tr>
                  <td style="padding:14px 16px;color:#6b7280;border-top:1px solid #e4eaf2;">Status</td>
                  <td align="right" style="padding:14px 16px;border-top:1px solid #e4eaf2;font-weight:700;">' . $status . '</td>
                </tr>
              </table>
              <p style="margin:24px 0;">
                <a href="' . $invoiceLink . '" style="background:#148064;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:6px;font-weight:700;display:inline-block;">View Invoice</a>
              </p>
              <p style="margin:22px 0 0;color:#6b7280;font-size:13px;line-height:1.5;">If the button does not work, copy this link:<br>' . $invoiceLink . '</p>
            </td>
          </tr>
          <tr>
            <td style="background:#f8fafc;padding:18px 30px;color:#6b7280;font-size:13px;">
              SiteWorx Billing | support@siteworx.in | https://www.siteworx.in
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}
