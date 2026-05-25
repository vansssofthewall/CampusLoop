<?php
// Payfast Sandbox Configuration

define('PF_MERCHANT_ID', '10049349'); // Sandbox test merchant id
define('PF_MERCHANT_KEY', '5ivwyd0h6j0em'); // Sandbox test key
define('PF_PASSPHRASE', 'vanshikagandhi');
define('PF_SANDBOX', true); // true for testing 

// payfast urls
if (PF_SANDBOX) {
    define('PF_ACTION_URL', 'https://sandbox.payfast.co.za/eng/process');
} else {
    define('PF_ACTION_URL', 'https://www.payfast.co.za/eng/process');
}
// Return urls
define('PF_RETURN_URL', 'http://localhost/CampusLoop/return.php');
define('PF_CANCEL_URL', 'http://localhost/CampusLoop/cancel.php');
define('PF_NOTIFY_URL', 'http://localhost/CampusLoop/notify.php');
?>