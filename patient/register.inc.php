<?php 

declare(strict_types= 1);
require_once("../includes/dbh.inc.php");
require_once("../includes/session.inc.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $pwd = $_POST["pwd"];
    $username = trim($_POST["username"]);
    $blood = trim($_POST["blood"]);
    $isAjax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

    try {
        // Error tracking
        $errors = [];

        // Check empty fields
        if (checkInput($name, $email, $pwd, $username, $blood)) {
            $errors["check_input"] = "Fill all fields!";
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email_invalid"] = "Invalid email format!";
        }

        // Restrict email to only gmail.com or hotmail.com
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@(gmail\.com|hotmail\.com)$/', $email)) {
            $errors["email_domain"] = "Only Gmail or Hotmail addresses are allowed!";
        }

        // Check if username already exists
        if (username_exists($pdo, $username)) {
            $errors["user_exists"] = "Username already exists!";
        }

        // Check if email already exists
        if (email_exists($pdo, $email)) {
            $errors["email_exists"] = "Email already exists!";
        }


        if ($errors) {
            if ($isAjax) {
                echo json_encode(['status' => 'error', 'message' => implode(' | ', $errors)]);
                exit();
            }
            $_SESSION["patient_error_register"] = $errors;
            header("Location: register.php");
            exit();
        }

        // Insert user
        insert_user($pdo, $name, $username, $pwd, $email, $blood);

        // Store session
        $_SESSION["patient"] = $username;

        if ($isAjax) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
            exit();
        }

        header("Location: register.php?register=success");
        exit();

    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: register.php");
    exit();
}

// Function to check for empty input fields
function checkInput(string $name, string $email, string $pwd, string $username, string $blood): bool {
    return empty($name) || empty($email) || empty($pwd) || empty($username) || empty($blood);
}

// Function to check if username already exists
function username_exists(object $pdo, string $username): bool {
    $query = "SELECT username FROM patient WHERE username = :username;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// Function to check if email already exists
function email_exists(object $pdo, string $email): bool {
    $query = "SELECT email FROM patient WHERE email = :email;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// Function to insert user into database
function insert_user(object $pdo, string $name, string $username, string $pwd, string $email, string $blood): void {
    $query = "INSERT INTO patient (name, username, pwd, email, blood) VALUES (:name, :username, :pwd, :email, :blood);";
    $stmt = $pdo->prepare($query);
    $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, ["cost" => 10]);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":pwd", $hashedPwd);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":blood", $blood);
    $stmt->execute();
}
?>
