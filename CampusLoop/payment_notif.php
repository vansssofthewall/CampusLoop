<?php
//payfast instant transaction notification handler

include("config.php");

// log notif for sandbox
$data = file_get_contents('php://input');
file_put_contents('payfast_notif.txt', date('Y-m-d H:i:s') . " - " . $data . "\n", FILE_APPEND);

//parse notif
parse_str($data, $pfData);

if (isset($pfData['m_payment_id'])) {
    $order_id = intval($pfData['m_payment_id']);
    $payment_status = $pfData['payment_status'];

    if ($payment_status == 'COMPLETE') {
        $conn->query("UPDATE orders SET status = 'paid', pf_payment_id = '{$pfData['pf_payment_id']}' WHERE id = $order_id");
    }
}

echo "OK";
?>
