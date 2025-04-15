<?php
// clients/kenya_tech/payment/mpesa_callback.php

// 1. Read incoming JSON
$callbackJSON = file_get_contents('php://input');

// 2. Decode JSON into PHP array
$data = json_decode($callbackJSON, true);

// 3. Optional: Log the full response for testing
file_put_contents('callback_log.json', $callbackJSON, FILE_APPEND);

// 4. Extract data (optional and safe check)
if (isset($data['Body']['stkCallback'])) {
    $stk = $data['Body']['stkCallback'];

    $merchantRequestID = $stk['MerchantRequestID'] ?? '';
    $checkoutRequestID = $stk['CheckoutRequestID'] ?? '';
    $resultCode = $stk['ResultCode'] ?? '';
    $resultDesc = $stk['ResultDesc'] ?? '';

            // Optional: Only process successful payments (ResultCode = 0)
            if ($resultCode == 0) {
                $metadata = $stk['CallbackMetadata']['Item'];
                $amount = $mpesaReceipt = $transactionDate = $phoneNumber = null;

        foreach ($metadata as $item) {
            switch ($item['Name']) {
                case 'Amount':
                    $amount = $item['Value'];
                    break;
                case 'MpesaReceiptNumber':
                    $mpesaReceipt = $item['Value'];
                    break;
                case 'TransactionDate':
                    $transactionDate = $item['Value'];
                    break;
                case 'PhoneNumber':
                    $phoneNumber = $item['Value'];
                    break;
            }
        }


        // TODO: Save to database here if needed

        // Example logging
        $log = "✅ Payment received:\nReceipt: $mpesaReceipt\nAmount: $amount\nPhone: $phoneNumber\n\n";
        file_put_contents('callback_log.txt', $log, FILE_APPEND);
    } else {
        $log = "❌ Failed Transaction:\nResultDesc: $resultDesc\n\n";
        file_put_contents('callback_log.txt', $log, FILE_APPEND);
    }
}

// 5. Respond to Safaricom (MUST DO)
header('Content-Type: application/json');
echo json_encode([
    "ResultCode" => 0,
    "ResultDesc" => "Callback received successfully"
]);
