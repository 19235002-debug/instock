<?php
// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
  exit;
}

/**
 * Initialize tbl_audit_log database table if it doesn't exist
 */
function initAuditLogTable($mysqli)
{
  if (!$mysqli) return;

  $query = "CREATE TABLE IF NOT EXISTS tbl_audit_log (
        id_log INT AUTO_INCREMENT PRIMARY KEY,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        id_user INT NULL,
        username VARCHAR(100) NULL,
        role VARCHAR(50) NULL,
        action VARCHAR(100) NOT NULL,
        module VARCHAR(50) NOT NULL,
        details TEXT NULL,
        ip_address VARCHAR(45) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

  try {
    @mysqli_query($mysqli, $query);
  } catch (Throwable $e) {
    // Ignore schema errors gracefully
  }
}

/**
 * Helper function to log user activity across the application
 */
function logActivity($mysqli, $action, $module, $details = '')
{
  if (!$mysqli) return;

  initAuditLogTable($mysqli);

  $id_user = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
  $username = isset($_SESSION['username']) ? mysqli_real_escape_string($mysqli, $_SESSION['username']) : 'System/Guest';
  $role = isset($_SESSION['hak_akses']) ? mysqli_real_escape_string($mysqli, $_SESSION['hak_akses']) : 'Unknown';

  $action_clean = mysqli_real_escape_string($mysqli, $action);
  $module_clean = mysqli_real_escape_string($mysqli, $module);
  $details_clean = mysqli_real_escape_string($mysqli, $details);

  $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
  }
  $ip_address = mysqli_real_escape_string($mysqli, trim($ip_address));

  $query = "INSERT INTO tbl_audit_log (timestamp, id_user, username, role, action, module, details, ip_address)
              VALUES (NOW(), '$id_user', '$username', '$role', '$action_clean', '$module_clean', '$details_clean', '$ip_address')";

  try {
    @mysqli_query($mysqli, $query);
  } catch (Throwable $e) {
    // Ignore logging errors gracefully
  }
}
