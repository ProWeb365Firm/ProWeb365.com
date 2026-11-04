<?php
/**
 * Contact form handler for the static ProWeb365 site.
 * Handles both form types on the site:
 *   - Contact Form 7 forms (your-name, your-email, your-phone, audit-goal, your-message)
 *   - Elementor forms (form_fields[...], with field labels sent by the page as _labels)
 * Replaces WordPress form processing. Uses PHP mail().
 */

$to       = 'info@proweb365.com';
$fromAddr = 'no-reply@proweb365.com';

// Pages a form may send the visitor back to (relative to this file).
$allowedReturns = ['./', 'about-us/', 'digital-marketing/', 'website-design/', 'clients-testimonials/', 'services/', 'marketing/minneapolis-seo-services/', 'faribault-web-design/', 'roseville-web-design/', 'eagan-web-design/', 'st-paul-web-design/', 'rochester-web-design/', 'website-design/rochester-minnesota/small-businesses/', 'e-commerce-web-design/', 'website-design/minnesota/small-businesses/', 'wordpress-website-design/', 'website-maintenance/', 'custom-web-design/', 'contact-us/', 'case-studies/', 'logo-design/', 'hvac-marketing/website-design/roseville-minnesota/', 'senior-living-marketing/website-development/faribault-minnesota/', 'hvac-marketing/website-design/rochester-minnesota/', 'hvac-marketing/website-design/minneapolis-minnesota/', 'senior-living-marketing/website-design/rochester-minnesota/', 'hvac-marketing/website-design/faribault-minnesota/', 'hvac-marketing/website-design/st-paul-minnesota/', 'senior-living-marketing/website-development/roseville-minnesota/', 'senior-living-marketing/website-design/roseville-minnesota/', 'senior-living-marketing/website-design/st-paul-minnesota/', 'web-developer-minneapolis-mn/', 'st-paul-web-developers-mn/', 'senior-living-web-design-faribault-mn/'];
$return = in_array($_POST['_return'] ?? '', $allowedReturns, true) ? $_POST['_return'] : './';
function back($return, $ok) { header('Location: ' . $return . '?sent=' . ($ok ? '1' : '0')); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ./'); exit; }

// Honeypot: real visitors never fill this hidden field.
if (!empty($_POST['website_url'])) back($return, true);

function clean_line($v) { return trim(str_replace(["\r", "\n", "%0a", "%0d"], ' ', (string)$v)); }

$lines = [];
$name = ''; $email = '';

if (isset($_POST['form_fields']) && is_array($_POST['form_fields'])) {
    // Elementor form
    $labels = json_decode((string)($_POST['_labels'] ?? '{}'), true) ?: [];
    foreach ($_POST['form_fields'] as $key => $value) {
        if (is_array($value)) $value = implode(', ', $value);
        $value = trim((string)$value);
        $label = $labels['form_fields[' . $key . ']'] ?? ucfirst(str_replace('_', ' ', $key));
        if ($key === 'name')  $name  = clean_line($value);
        if ($key === 'email') $email = clean_line($value);
        if ($value !== '') $lines[] = $label . ': ' . $value;
    }
} else {
    // Contact Form 7 form
    $map = ['your-name' => 'Name', 'your-email' => 'Email', 'your-phone' => 'Phone',
            'audit-goal' => 'Goal', 'your-subject' => 'Subject', 'your-message' => 'Message'];
    foreach ($map as $key => $label) {
        $value = trim((string)($_POST[$key] ?? ''));
        if ($value !== '') $lines[] = $label . ': ' . $value;
    }
    $name  = clean_line($_POST['your-name'] ?? '');
    $email = clean_line($_POST['your-email'] ?? '');
}

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) back($return, false);

$page = $return === './' ? 'Home' : rtrim($return, '/');
$body  = "New message from the website ({$page} page)\n\n" . implode("\n", $lines) . "\n";
$body .= "\n--\nSent from proweb365.com";

$headers  = "From: ProWeb365 Website <{$fromAddr}>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$ok = mail($to, 'Website message from ' . $name, $body, $headers, '-f' . $fromAddr);
back($return, $ok);
