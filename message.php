<?php
if (!isset($_POST['message'])) {
    echo "Hello! Ask me about blood donation.";
    exit;
}

$message = strtolower(trim($_POST['message']));

if (strpos($message, 'hi') !== false || strpos($message, 'hello') !== false) {
    echo "Namaste! How can I help you with blood donation?";
}
elseif (strpos($message, 'tfid') !== false) {
    echo "TFID means Transfusion ID. It is used to track blood transfusion requests.";
}
elseif (strpos($message, 'donor') !== false) {
    echo "A donor must be healthy and above 18 years old.";
}
elseif (strpos($message, 'patient') !== false) {
    echo "Patients can request blood after logging in.";
}
elseif (strpos($message, 'blood group') !== false) {
    echo "Available blood groups are A+, A-, B+, B-, O+, O-, AB+, AB-.";
}
else {
    echo "I'm still learning 😊 Please ask about donor, patient, TFID, or blood group.";
}
?>
