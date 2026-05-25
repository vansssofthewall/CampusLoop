<?php
session_start();
include("config.php");
include("payfast_config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($_POST['amount']);
$item_name = $_POST['item_name'];
$item_description = $_POST['item_description'];
$cart_data = json_decode($_POST['cart_data'], true);

// Generate unique order number
$order_number = 'ORD-' . strtoupper(uniqid());

// Save order to database with pending status
$stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'pending')");
$stmt->bind_param("isd", $user_id, $order_number, $amount);
$stmt->execute();
$order_id = $conn->insert_id;

// Save order items
foreach ($cart_data as $item) {
    $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, listing_id, title, price, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("iisdi", $order_id, $item['id'], $item['title'], $item['price'], $item['quantity']);
    $stmt2->execute();
}

// Prepare PayFast data
$pfData = array(
    'merchant_id' => PF_MERCHANT_ID,
    'merchant_key' => PF_MERCHANT_KEY,
    'return_url' => PF_RETURN_URL,
    'cancel_url' => PF_CANCEL_URL,
    'notify_url' => PF_NOTIFY_URL,
    'name_first' => $_SESSION['username'],
    'email_address' => $_SESSION['username'] . '@student.com',
    'm_payment_id' => $order_id,
    'amount' => number_format($amount, 2, '.', ''),
    'item_name' => $item_name,
    'item_description' => substr($item_description, 0, 255)
);

// Generate signature with passphrase
$pfSignatureString = '';
foreach ($pfData as $key => $val) {
    $pfSignatureString .= $key . '=' . urlencode(trim($val)) . '&';
}
$pfSignatureString = rtrim($pfSignatureString, '&');
if (!empty(PF_PASSPHRASE)) {
    $pfSignatureString .= '&passphrase=' . urlencode(trim(PF_PASSPHRASE));
}
$pfData['signature'] = md5($pfSignatureString);

// Build HTML form with auto-submit
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to PayFast...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f5f5f5;
        }
        .loader {
            text-align: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #c6a43f;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="spinner"></div>
        <h2>Redirecting to PayFast Secure Payment...</h2>
        <p>Please do not close this page.</p>
        <form action="<?php echo PF_ACTION_URL; ?>" method="post" id="payfastForm">
            <?php foreach ($pfData as $name => $value): ?>
                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endforeach; ?>
        </form>
    </div>
    <script>
        document.getElementById('payfastForm').submit();
    </script>
</body>
</html>