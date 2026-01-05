<?php
// payment.php

require_once("../includes/session.inc.php");
require_once("../includes/dbh.inc.php");

// Redirect if not logged in as a donor
if (!isset($_SESSION["donor"])) {
    header("Location: login.php");
    die();
}

// Ensure the request is a POST request and comes from the donation form
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["initiate_khalti_payment"])) {
    $_SESSION["money_error_message"] = "Invalid request.";
    header("Location: donatemoney.php");
    die();
}

$amount_str = trim($_POST["amount"] ?? '');
$errors = [];

// --- Server-side validation of the amount ---
if (empty($amount_str)) {
    $errors[] = "Please enter a donation amount.";
} else {
    $amount = filter_var($amount_str, FILTER_VALIDATE_FLOAT); 
    
    if ($amount === false) { 
        $errors[] = "Please enter a valid numeric amount.";
    } else {
        if (fmod($amount, 1) != 0) { // Check if it's a whole number
            $errors[] = "Please enter a whole number amount (no decimal places).";
        } else {
            if ($amount < 10 || $amount > 1000) {
                $errors[] = "Donation amount must be between 10 and 1000.";
            }
        }
    }
}

// If validation fails, redirect back to donatemoney.php with errors
if (!empty($errors)) {
    $_SESSION["money_error_message"] = implode("<br>", $errors); // Join errors for display
    header("Location: donatemoney.php");
    die();
}

// Amount is valid, proceed with Khalti API call
$donation_amount_npr = (int)$amount; // The amount in Nepali Rupees (whole number)
$donation_amount_paise = $donation_amount_npr * 100; // Khalti requires amount in paise

// Fetch donor details for customer_info
try {
    $query_donor = "SELECT id, name, email, phone FROM donor WHERE username = :username;";
    $stmt_donor = $pdo->prepare($query_donor);
    $stmt_donor->bindParam(":username", $_SESSION["donor"]);
    $stmt_donor->execute();
    $donor_info = $stmt_donor->fetch(PDO::FETCH_ASSOC);

    if (!$donor_info) {
        $_SESSION["money_error_message"] = "Donor information not found. Please log in again.";
        header("Location: donatemoney.php");
        die();
    }
} catch (PDOException $e) {
    error_log("Payment preparation PDO Error: " . $e->getMessage()); 
    $_SESSION["money_error_message"] = "A database error occurred while fetching donor info: " . $e->getMessage();
    header("Location: donatemoney.php");
    die();
}


// --- Khalti API Integration ---
$KHALTI_SECRET_KEY = '344cc08cef3649b2b5d23ff74fbb20d8'; // <--- UPDATED SECRET KEY HERE
$KHALTI_API_URL = 'https://dev.khalti.com/api/v2/epayment/initiate/';

// Generate a unique purchase order ID for this transaction
$purchase_order_id = 'DONOR-' . $donor_info['id'] . '-' . uniqid(); 
$purchase_order_name = "Blood Bank Donation"; 

$payload = json_encode([
    "return_url" => "http://localhost/ragatchaiyo/donor/khalti_callback.php", // <<< IMPORTANT: Change this to your actual callback URL
    "website_url" => "http://localhost/ragatchaiyo", // <<< IMPORTANT: Change this to your actual website URL
    "amount" => $donation_amount_paise, // Amount in paise
    "purchase_order_id" => $purchase_order_id,
    "purchase_order_name" => $purchase_order_name,
    "customer_info" => [
        "name" => $donor_info['name'], 
        "email" => $donor_info['email'],
        "phone" => $donor_info['phone']
    ]
]);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $KHALTI_API_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30, 
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $payload,
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

if ($curl_error) {
    error_log("Khalti cURL Error: " . $curl_error);
    $_SESSION["money_error_message"] = "Payment initiation failed: Could not connect to payment gateway. cURL Error: " . $curl_error; 
    header("Location: donatemoney.php");
    die();
}

$response_data = json_decode($response, true);

if ($http_code == 200 && isset($response_data['payment_url'])) {
    header("Location: " . $response_data['payment_url']);
    die();
} else {
    $error_message = "Payment initiation failed.";
    if (isset($response_data['detail'])) {
        $error_message .= " Khalti Error: " . $response_data['detail'];
    } elseif (isset($response_data['message'])) {
        $error_message .= " Khalti Error: " . $response_data['message'];
    } elseif (isset($response_data['non_field_errors'])) {
        $error_message .= " Khalti Error: " . implode(", ", $response_data['non_field_errors']);
    } else {
        $error_message .= " Unknown error occurred. Please try again.";
        error_log("Khalti API Unknown Error: HTTP Code: " . $http_code . " | Response: " . $response);
    }
    
    $_SESSION["money_error_message"] = $error_message;
    header("Location: donatemoney.php");
    die();
}
?>