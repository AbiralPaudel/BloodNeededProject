<?php
    
    
    declare(strict_types= 1);
    require_once("../includes/session.inc.php");
    require_once("../includes/dbh.inc.php");
    
    if($_SERVER["REQUEST_METHOD"]==="POST")
    {
        try 
        {
            //code...
            $reason = $_POST["reason"];
            $unit = $_POST["unit"];
            $blood_type = $_POST["blood_type"] ?? '';
            
            $errors = [];

            if(empty($reason) || $unit==null || empty($blood_type))
            {
                $errors["request_empty"] = "Fill all fields!";
            }
            if($unit !== null && $unit < 1) 
            {
                $errors["request_negative"] = "Blood units must be at least 1!";
            }
            // optional: validate blood-type against allowed values
            $validGroups = ["A+","A-","B+","B-","AB+","AB-","O+","O-"];
            if ($blood_type && !in_array($blood_type, $validGroups, true)) {
                $errors["request_bloodgroup"] = "Invalid blood type selected.";
            }


            if($errors)
            {
                $_SESSION["patient_error_request"] = $errors;
                header("Location:dashboard.php?request_blood=1");
                die();
            }

            // use the blood type chosen by the patient rather than their own group
            $blood = $blood_type;

            // verify that the requested blood type is actually present in the storage table
            // mapping matches admin/request.php logic
            $col = $blood;
            if ($col === 'A+') $col = 'AP';
            if ($col === 'A-') $col = 'AN';
            if ($col === 'B+') $col = 'BP';
            if ($col === 'B-') $col = 'BN';
            if ($col === 'AB+') $col = 'ABP';
            if ($col === 'AB-') $col = 'ABN';
            if ($col === 'O+') $col = 'OP';
            if ($col === 'O-') $col = 'ON';

            $query = "SELECT {$col} FROM blood WHERE id = 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $stockRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$stockRow || !isset($stockRow[$col]) || $stockRow[$col] <= 0) {
                $_SESSION["patient_error_request"] = ["Selected blood type ({$blood}) is not available at the moment."];
                header("Location:dashboard.php?request_blood=1");
                die();
            }

            $query = "SELECT id from patient where username=:current_username;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":current_username", $_SESSION['patient']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $patient_id = $result["id"];

            $query = "INSERT into request(username,patient_id,reason,blood,unit) values(:current_username,:id,:reason,:blood,:unit);";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":current_username", $_SESSION["patient"]);
            $stmt->bindParam(":reason", $reason);
            $stmt->bindParam(":blood", $blood);
            $stmt->bindParam(":id", $patient_id);
            $stmt->bindParam(":unit", $unit);
            $stmt->execute();

            // ---- email notification to patient ----
            // fetch patient email address
            $query = "SELECT email FROM patient WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $patient_id);
            $stmt->execute();
            $emailResult = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($emailResult && !empty($emailResult['email'])) {
                require_once __DIR__ . "/../includes/mail.inc.php";
                $patientEmail = $emailResult['email'];
                $subject = 'Blood request received';
                $body    = "<p>You have requested <strong>{$unit}</strong> unit(s) of <strong>{$blood}</strong> blood from us.</p>" .
                           "<p>We will notify you when the blood availability is confirmed or if there is any issue.</p>";
                $sent = sendMail($patientEmail, $subject, $body);
                if ($sent) {
                    $_SESSION['patient_mail_sent'] = "An email has been sent to: {$patientEmail}";
                } else {
                    // sendMail may have stored error in session
                    $msg = $_SESSION['mail_error'] ?? 'unknown error';
                    $_SESSION['patient_mail_sent'] = "Failed to send email to {$patientEmail}: {$msg}";
                }
            }
            // ---------------------------------------

            header("Location:dashboard.php?requests_history=1");

            $pdo = null;
            $stmt = null;

            die();


        } 
        catch (PDOException $e) 
        {
            //throw $th;
            echo $e;
        }
    }
    else 
    {
        header("Location:dashboard.php");
        die();
    }

?>