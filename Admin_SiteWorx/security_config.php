<?php
/**
 * SiteWorx Admin - Security Configuration Helper
 * 
 * This file contains security-related configurations and helper functions
 * Include this in your admin pages for enhanced security
 * 
 * Usage: require_once 'security_config.php';
 */

// ============================================================================
// SESSION SECURITY CONFIGURATION
// ============================================================================

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Set secure session options
    ini_set('session.cookie_httponly', 1);    // Prevent JavaScript access
    ini_set('session.cookie_secure', 0);      // Set to 1 if using HTTPS only
    ini_set('session.use_strict_mode', 1);    // Prevent session fixation
    ini_set('session.gc_maxlifetime', 3600);  // 1 hour session timeout
    
    session_start();
}

// ============================================================================
// SECURITY HEADERS - Add to prevent common attacks
// ============================================================================

function set_security_headers() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent MIME-type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Strict Transport Security (uncomment if using HTTPS)
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com; font-src 'self' cdnjs.cloudflare.com;");
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Call this in your main pages
set_security_headers();

// ============================================================================
// RATE LIMITING - Prevent brute force attacks
// ============================================================================

class RateLimiter {
    private $attempts_file = '/tmp/login_attempts.json';
    private $max_attempts = 5;
    private $lockout_duration = 900; // 15 minutes
    
    public function __construct($max_attempts = 5, $lockout_duration = 900) {
        $this->max_attempts = $max_attempts;
        $this->lockout_duration = $lockout_duration;
    }
    
    /**
     * Check if IP is rate limited
     */
    public function is_rate_limited($ip = null) {
        if ($ip === null) {
            $ip = $this->get_client_ip();
        }
        
        $attempts = $this->get_attempts($ip);
        
        if (isset($attempts['count']) && $attempts['count'] >= $this->max_attempts) {
            if (time() - $attempts['first_attempt'] < $this->lockout_duration) {
                return true;
            } else {
                // Lockout period expired, reset
                $this->reset_attempts($ip);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Record a failed login attempt
     */
    public function record_attempt($ip = null) {
        if ($ip === null) {
            $ip = $this->get_client_ip();
        }
        
        $attempts = $this->get_attempts($ip);
        
        if (!isset($attempts['count'])) {
            $attempts['count'] = 1;
            $attempts['first_attempt'] = time();
        } else {
            $attempts['count']++;
        }
        
        $this->save_attempts($ip, $attempts);
    }
    
    /**
     * Reset attempts for an IP
     */
    public function reset_attempts($ip = null) {
        if ($ip === null) {
            $ip = $this->get_client_ip();
        }
        
        $attempts = $this->load_attempts_data();
        unset($attempts[$ip]);
        $this->save_attempts_data($attempts);
    }
    
    /**
     * Get number of attempts for an IP
     */
    private function get_attempts($ip) {
        $attempts = $this->load_attempts_data();
        return $attempts[$ip] ?? [];
    }
    
    /**
     * Save attempts for an IP
     */
    private function save_attempts($ip, $data) {
        $attempts = $this->load_attempts_data();
        $attempts[$ip] = $data;
        $this->save_attempts_data($attempts);
    }
    
    /**
     * Load all attempts from file
     */
    private function load_attempts_data() {
        if (!file_exists($this->attempts_file)) {
            return [];
        }
        
        $data = file_get_contents($this->attempts_file);
        return json_decode($data, true) ?? [];
    }
    
    /**
     * Save all attempts to file
     */
    private function save_attempts_data($data) {
        if (!is_writable(dirname($this->attempts_file))) {
            // Fallback to session storage if file not writable
            $_SESSION['rate_limit_data'] = $data;
            return;
        }
        
        file_put_contents($this->attempts_file, json_encode($data), LOCK_EX);
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        // Check for shared internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // Check for IP passed from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        // Default to REMOTE_ADDR
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

// ============================================================================
// CSRF TOKEN GENERATION - For form protection
// ============================================================================

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================================================
// LOGGING - Track important security events
// ============================================================================

function log_security_event($event, $details = []) {
    $log_file = __DIR__ . '/security.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user = $_SESSION['username'] ?? 'ANONYMOUS';
    
    $log_entry = [
        'timestamp' => $timestamp,
        'event' => $event,
        'user' => $user,
        'ip' => $ip,
        'details' => $details
    ];
    
    $log_message = json_encode($log_entry) . PHP_EOL;
    
    // Append to log file with atomic write
    if (is_writable(dirname($log_file))) {
        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    }
}

// ============================================================================
// PASSWORD VALIDATION - Check password strength
// ============================================================================

class PasswordValidator {
    private $min_length = 6;
    private $require_uppercase = false;
    private $require_numbers = false;
    private $require_special = false;
    
    public function __construct($config = []) {
        if (isset($config['min_length'])) $this->min_length = $config['min_length'];
        if (isset($config['require_uppercase'])) $this->require_uppercase = $config['require_uppercase'];
        if (isset($config['require_numbers'])) $this->require_numbers = $config['require_numbers'];
        if (isset($config['require_special'])) $this->require_special = $config['require_special'];
    }
    
    /**
     * Validate password and return errors
     */
    public function validate($password) {
        $errors = [];
        
        if (strlen($password) < $this->min_length) {
            $errors[] = "Password must be at least {$this->min_length} characters";
        }
        
        if ($this->require_uppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if ($this->require_numbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if ($this->require_special && !preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return $errors;
    }
    
    /**
     * Check if password is valid
     */
    public function is_valid($password) {
        return empty($this->validate($password));
    }
    
    /**
     * Get password strength score (0-100)
     */
    public function get_strength($password) {
        $strength = 0;
        
        if (strlen($password) >= $this->min_length) $strength += 20;
        if (strlen($password) >= 12) $strength += 20;
        if (preg_match('/[a-z]/', $password)) $strength += 15;
        if (preg_match('/[A-Z]/', $password)) $strength += 15;
        if (preg_match('/[0-9]/', $password)) $strength += 15;
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)) $strength += 15;
        
        return min(100, $strength);
    }
}

// ============================================================================
// INPUT SANITIZATION - Clean user input
// ============================================================================

function sanitize_input($data, $type = 'string') {
    $data = trim($data);
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
            
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
            
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT);
            
        case 'string':
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

function sanitize_array($data, $types = []) {
    $sanitized = [];
    
    foreach ($data as $key => $value) {
        $type = $types[$key] ?? 'string';
        $sanitized[$key] = sanitize_input($value, $type);
    }
    
    return $sanitized;
}

// ============================================================================
// SESSION VALIDATION - Check session integrity
// ============================================================================

function validate_session_integrity() {
    // Check if session has required fields
    if (!isset($_SESSION['valid'])) {
        return false;
    }
    
    // Optional: Check user agent hasn't changed (may cause issues with some users)
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // Could be suspicious, but optional to enforce
    }
    
    // Check session hasn't expired
    if (isset($_SESSION['session_created'])) {
        $session_duration = time() - $_SESSION['session_created'];
        if ($session_duration > 3600) { // 1 hour
            return false;
        }
    }
    
    return true;
}

// Initialize session tracking
if (isset($_SESSION['valid']) && !isset($_SESSION['session_created'])) {
    $_SESSION['session_created'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

// ============================================================================
// USAGE EXAMPLES
// ============================================================================

/*

// Example 1: Rate limiting on login page
$rate_limiter = new RateLimiter(5, 900); // 5 attempts per 15 minutes

if ($rate_limiter->is_rate_limited()) {
    $error = 'Too many login attempts. Please try again later.';
    log_security_event('RATE_LIMIT_EXCEEDED', ['ip' => $_SERVER['REMOTE_ADDR']]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Attempt login
    if (!$login_successful) {
        $rate_limiter->record_attempt();
        $error = 'Invalid credentials';
        log_security_event('LOGIN_FAILED', ['username' => $username]);
    } else {
        $rate_limiter->reset_attempts();
        log_security_event('LOGIN_SUCCESS', ['username' => $username]);
    }
}

// Example 2: Password validation
$validator = new PasswordValidator([
    'min_length' => 8,
    'require_uppercase' => true,
    'require_numbers' => true,
]);

if (!$validator->is_valid($_POST['password'])) {
    $errors = $validator->validate($_POST['password']);
    foreach ($errors as $error) {
        echo "Error: $error";
    }
}

// Example 3: Input sanitization
$email = sanitize_input($_POST['email'], 'email');
$user_data = sanitize_array($_POST, [
    'username' => 'string',
    'email' => 'email',
    'age' => 'int',
]);

// Example 4: CSRF protection in forms
<input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

// And verify in POST handler:
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('CSRF token invalid');
}

*/

// ============================================================================
// END OF SECURITY CONFIGURATION
// ============================================================================
