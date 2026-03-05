<?php

    declare(strict_types= 1);
    require_once("../includes/dbh.inc.php");

    if($_SERVER["REQUEST_METHOD"]==='POST')
    {

        try 
        {
            //code...
            $id = $_POST['id'];
            $input_status = isset($_POST['approve']) ? "approved" :'rejected';
    
            $query = "SELECT * FROM request where id=:id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($input_status=="approved")
            {
                $request_unit = $result["unit"];
                $blood = $result["blood"];
        
                if($blood=="A+") $blood="AP";
                if($blood=="A-") $blood="AN";
                if($blood=="B+") $blood="BP";
                if($blood=="B-") $blood="BN";
                if($blood=="AB+") $blood="ABP";
                if($blood=="AB-") $blood="ABN";
                if($blood=="O+") $blood="OP";
                if($blood=="O-") $blood="`ON`";

                $blood_id = 1;

                $query = "SELECT * FROM blood WHERE id = :id;";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":id", $blood_id);
                $stmt->execute();
                $blood_result = $stmt->fetch(PDO::FETCH_ASSOC);

                if($blood_result[$blood]-$request_unit>=0)
                {
                    $query = "UPDATE blood SET {$blood} = {$blood} - :unit WHERE id = :id;";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":id", $blood_id, PDO::PARAM_INT);
                    $stmt->bindParam(":unit", $request_unit, PDO::PARAM_INT);
                    $stmt->execute();
                }
                else
                {
                    $input_status = "rejected due to insufficient blood stock of " . $result['blood'];
                }

            }
    
            $status = "status";
    
            $query = "UPDATE request SET {$status} = :status where id=:id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":status", $input_status);
            $stmt->execute();

            // send notification to patient about status change
            if (isset($result['patient_id'])) {
                $patientId = $result['patient_id'];
                $query = "SELECT email FROM patient WHERE id=:id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":id", $patientId);
                $stmt->execute();
                $emailRes = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($emailRes && !empty($emailRes['email'])) {
                    require_once __DIR__ . "/../includes/mail.inc.php";
                    $to = $emailRes['email'];
                    $subject = 'Update on your blood request';
                    if ($input_status === 'approved') {
                        $body = "<p>Your request for <strong>{$request_unit}</strong> unit(s) of <strong>{$blood}</strong> blood has been <strong>approved</strong>.</p>";
                    } else {
                        $body = "<p>Your request for <strong>{$request_unit}</strong> unit(s) of <strong>{$blood}</strong> blood has been <strong>rejected</strong>";
                        if (strpos($input_status, 'insufficient') !== false) {
                            $body .= " because there is not enough stock available.";
                        }
                        $body .= ".</p>";
                    }
                    $sent = sendMail($to, $subject, $body);
                    if ($sent) {
                        $_SESSION['admin_mail_sent'] = "An email has been sent to: {$to}";
                    } else {
                        $msg = $_SESSION['mail_error'] ?? 'unknown error';
                        $_SESSION['admin_mail_sent'] = "Failed to send email to {$to}: {$msg}";
                    }
                }
            }

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
        header("Location:dashboard.php?requests=1");
        die();
    }

?>