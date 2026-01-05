<?php
// khalti_callback.php

require_once("../includes/session.inc.php");
require_once("../includes/dbh.inc.php");

// Redirect if not logged in (optional, but good for context)
if (!isset($_SESSION["donor"])) {
    header("Location: login.php");
    die();
}

$status = $_GET['status'] ?? 'failed'; 
$purchase_order_id = $_GET['purchase_order_id'] ?? '';
$transaction_idx = $_GET['pidx'] ?? $_GET['idx'] ?? $_GET['tidx'] ?? ''; // Prioritize pidx as it's the primary lookup ID

$KHALTI_SECRET_KEY = '344cc08cef3649b2b5d23ff74fbb20d8'; // <--- UPDATED SECRET KEY HERE
$KHALTI_VERIFY_URL = 'https://dev.khalti.com/api/v2/epayment/lookup/'; 

$verified_amount = 0; 
$is_verified_successful = false;

if ($status === 'Completed' && !empty($transaction_idx)) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $KHALTI_VERIFY_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $transaction_idx]), 
        CURLOPT_HTTPHEADER => array(
            'Authorization: Key ' . $KHALTI_SECRET_KEY, // Changed to 'Key ' (capital 'K') for consistency/best practice
            'Content-Type: application/json',
        ),
        // Remove these in production once testing is complete and SSL is verified
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_SSL_VERIFYHOST => false, 
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    $verification_data = json_decode($response, true);

    // --- REFINED DEBUGGING LOGGING START ---
    error_log("KHALTI_CALLBACK_DEBUG: Transaction IDX received: " . $transaction_idx);
    error_log("KHALTI_CALLBACK_DEBUG: HTTP Code from Khalti Lookup: " . $http_code);
    error_log("KHALTI_CALLBACK_DEBUG: cURL Error: " . ($curl_error ?: "None"));
    error_log("KHALTI_CALLBACK_DEBUG: Khalti Lookup Raw Response: " . ($response ?: "Empty Response"));
    error_log("KHALTI_CALLBACK_DEBUG: Khalti Lookup Decoded Data (print_r): " . print_r($verification_data, true));
    error_log("KHALTI_CALLBACK_DEBUG: Value of verification_data['amount']: " . (isset($verification_data['amount']) ? $verification_data['amount'] : 'Key "amount" not found'));
    // --- REFINED DEBUGGING LOGGING END ---


    if (!$curl_error && $http_code == 200 && isset($verification_data['status']) && $verification_data['status'] === 'Completed') {
        // --- FIX: Check if 'amount' key exists AND is numeric ---
        if (isset($verification_data['amount']) && is_numeric($verification_data['amount'])) {
            $verified_amount_paise = (int)$verification_data['amount']; 
            $verified_amount = $verified_amount_paise / 100; 
            $is_verified_successful = true;
        } else {
            // Log this specific error for investigation in server logs
            error_log("KHALTI_CALLBACK_ERROR: 'amount' key missing or invalid in verification response for pidx: " . $transaction_idx . ". Full response: " . print_r($verification_data, true));
            $_SESSION["money_error_message"] = "Payment successful, but amount verification failed. Khalti response missing valid amount.";
            header("Location: donatemoney.php");
            die();
        }

        try {
            $query_donor_id = "SELECT id FROM donor WHERE username = :username;";
            $stmt_donor_id = $pdo->prepare($query_donor_id);
            $stmt_donor_id->bindParam(":username", $_SESSION["donor"]);
            $stmt_donor_id->execute();
            $donor_row = $stmt_donor_id->fetch(PDO::FETCH_ASSOC);

            if ($donor_row) {
                $donor_id = $donor_row["id"];

                $check_query = "SELECT COUNT(*) FROM money_donations WHERE khalti_transaction_idx = :idx;";
                $check_stmt = $pdo->prepare($check_query);
                $check_stmt->bindParam(":idx", $transaction_idx);
                $check_stmt->execute();
                if ($check_stmt->fetchColumn() == 0) { 
                    $insert_query = "INSERT INTO money_donations (donor_id, amount, khalti_transaction_idx, purchase_order_id) VALUES (:donor_id, :amount, :khalti_transaction_idx, :purchase_order_id);";
                    $insert_stmt = $pdo->prepare($insert_query);
                    $insert_stmt->bindParam(":donor_id", $donor_id);
                    $insert_stmt->bindParam(":amount", $verified_amount); 
                    $insert_stmt->bindParam(":khalti_transaction_idx", $transaction_idx);
                    $insert_stmt->bindParam(":purchase_order_id", $purchase_order_id); 
                    $insert_stmt->execute();

                    $_SESSION["money_success_message"] = "Payment successful! Thank you for your donation of Rs. " . number_format($verified_amount, 0) . ".";
                } else {
                    $_SESSION["money_success_message"] = "Payment already recorded. Thank you!";
                }
            } else {
                $_SESSION["money_error_message"] = "Could not find your donor account to record donation.";
            }

        } catch (PDOException $e) {
            error_log("Khalti Callback DB Error: " . $e->getMessage());
            $_SESSION["money_error_message"] = "Payment successful, but there was an issue recording it: " . $e->getMessage();
        }
        
    } else {
        $khalti_msg = $verification_data['message'] ?? $verification_data['detail'] ?? 'No specific message from Khalti.';
        if (isset($verification_data['status'])) {
            $khalti_msg .= " (Khalti Status: " . $verification_data['status'] . ")";
        }
        $_SESSION["money_error_message"] = "Payment verification failed. Khalti said: " . $khalti_msg . ". Please check server logs for more details.";
        error_log("Khalti Verification Failed: HTTP Code: " . $http_code . " | Response: " . ($response ?: 'No response') . " | cURL Error: " . $curl_error);
    }
} else {
    $_SESSION["money_error_message"] = "Payment was cancelled or failed by Khalti. No amount was charged.";
}

header("Location: donatemoney.php");
die();