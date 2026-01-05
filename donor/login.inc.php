<?php 
    declare(strict_types= 1);
    require_once("../includes/dbh.inc.php");
    require_once("../includes/session.inc.php");

    if($_SERVER["REQUEST_METHOD"]==="POST")
    {
        $pwd = $_POST["pwd"];
        $username = $_POST["username"];

        try 
        {
            $errors = [];
    
            if(checkInput($pwd,$username))
            {
                $errors["check_input"] = "Fill all fields!";
            }
            if(!username_exists($pdo,$username,$pwd))
            {
                $errors["incorrect"] = "Incorrect Login Info!";
            }

            if($errors)
            {
                $_SESSION["donor_error_login"] = $errors;
                header("Location:login.php");
                die();
            }

            $_SESSION["donor"] = $username;

            header("Location:dashboard.php?home=1"); 

            $pdo = null;
            $stmt = null;

            die();
        } 
        catch (PDOException $e) 
        {
            error_log("Login query failed: ". $e->getMessage());
            die("An unexpected error occurred. Please try again later.");
        }
    }
    else 
    {
        header("Location:login.php");
        die();
    }

    function checkInput(string $pwd, string $username) : bool
    {
        return empty($pwd) || empty($username);
    }

    function username_exists(object $pdo, string $username, string $pwd) : bool
    {
        $query = "SELECT pwd FROM donor WHERE username = :username;"; 
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$result) {
            return false;
        }
        
        return password_verify($pwd, $result["pwd"]);
    }
?>