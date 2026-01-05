<?php
    require_once("../includes/session.inc.php");
    require_once("../includes/template.php");

    // Redirect to dashboard if registration is successful
    if (isset($_SESSION["patient"]) && isset($_GET["register"]) && filter_var($_GET["register"], FILTER_SANITIZE_STRING) === "success") {
        header("Location: dashboard.php");
        exit();
    }

    function check_errors() {
        if (!empty($_SESSION["patient_error_register"])) {
            echo '<div class="container mt-3">';
            foreach ($_SESSION["patient_error_register"] as $error) {
                echo '<div class="alert alert-danger alert-dismissible fade show text-center mx-auto" role="alert" style="width: fit-content;">';
                echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="background:none;">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      </div>';
            }
            echo '</div>';
            unset($_SESSION["patient_error_register"]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Register</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../images/blood-drop.svg" type="image/x-icon">
    <style>
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc;
        }
        .form-container {
            border-radius: 10px;
            padding: 20px;
            margin: 10px auto 50px;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            border: 1px solid #1ac9bc;
            margin: 5px;
            transition: all 0.3s ease-in-out;
        }
        .btn-custom:hover {
            background-color: #1abc9c;
            color: #fff;
        }
        .text-center.custom-text-center {
            padding: 10px;
            max-width: 400px;
            margin: auto;
        }
        .navbar-custom {
            background-color: #f8f88f;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-top:80px;">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
            <a class="navbar-brand" href="../index.php" style="color: #777; font-size:22px; letter-spacing:2px;">RAGAT CHAIYO</a>
        </nav>
        
        <?php check_errors(); ?>
        
        <div class="text-center custom-text-center">
            <a class="btn btn-custom active" href="../patient/register.php">As Patient</a>
            <a class="btn btn-custom" href="../donor/register.php">As Donor</a>
        </div>
        
        <div style="display:block;">
            <?php register_template("Patient Register"); ?>
        </div>
    </div>
    
    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
