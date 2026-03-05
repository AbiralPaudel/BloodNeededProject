<?php 

    declare(strict_types= 1);
    require_once("../includes/dbh.inc.php");
    require_once("../includes/session.inc.php");

    if($_SERVER["REQUEST_METHOD"]==="POST")
    {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];
        $username = $_POST["username"];
        $blood = $_POST["blood"];
        $isAjax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

        try 
        {
            // errors array
            $errors = [];
    
            if(checkInput($name, $email, $pwd, $username, $blood))
            {
                $errors["check_input"] = "Fill all fields!";
            }

            // Domain-specific email validation (only allow gmail or hotmail)
            if (!preg_match("/@(gmail\.com|hotmail\.com)$/", $email)) {
                $errors["invalid_email"] = "Please use a valid email address from Gmail or Hotmail.";
            }

            // Check if username exists
            if(username_exists($pdo, $username))
            {
                $errors["user_exists"] = "User already exists!";
            }

            // Check if email exists
            if(email_exists($pdo, $email))
            {
                $errors["email_exists"] = "Email already exists!";
            }


            // If there are errors, redirect back to the register page
            if($errors)
            {
                if($isAjax) {
                    echo json_encode(['status' => 'error', 'message' => implode(' | ', $errors)]);
                    exit;
                }
                $_SESSION["donor_error_register"] = $errors;
                header("Location: register.php");
                die();
            }

            // Insert user data into the database
            insert_user($pdo, $name, $username, $pwd, $email, $blood);

            // Store session for the donor
            $_SESSION["donor"] = $username;

            if($isAjax) {
                echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
                exit;
            }

            // Redirect after successful registration (non-AJAX)
            header("Location: register.php?register=success");

            $pdo = null;
            $stmt = null;

            die();

        } 
        catch (PDOException $e) 
        {
            die("Query failed: " . $e->getMessage());
        }

    }
    else 
    {
        header("Location: register.php");
        die();
    }

    // Function to check for empty inputs
    function checkInput(string $name, string $email, string $pwd, string $username, string $blood)
    {
        return empty($name) || empty($email) || empty($pwd) || empty($username) || empty($blood);
    }

    // Function to check if username exists
    function username_exists(object $pdo, string $username)
    {
        $query = "SELECT username from donor where username=:username;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result) return true;
        return false;
    }

    // Function to check if email exists
    function email_exists(object $pdo, string $email)
    {
        $query = "SELECT email from donor where email=:email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) return true;
        return false;
    }

    // Function to insert a new user into the database
    function insert_user(object $pdo, string $name, string $username, string $pwd, string $email, string $blood)
    {
        $query = "INSERT INTO donor(name, username, pwd, email, blood) VALUES (:name, :username, :pwd, :email, :blood);";   
        $stmt = $pdo->prepare($query);
        $options = [
            "cost" => 10
        ];
        $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, $options);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":pwd", $hashedPwd);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":blood", $blood);
        $stmt->bindParam(":name", $name);
        $stmt->execute();
    }

?>
