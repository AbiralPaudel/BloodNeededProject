<?php
// donatemoney.php

require_once("../includes/session.inc.php");
require_once("../includes/dbh.inc.php"); // For $pdo database connection (even if not used for insert here, it's often included)

// Redirect if the user is not logged in as a donor
if (!isset($_SESSION["donor"])) {
    header("Location: login.php");
    die();
}

$errors = [];
$success_message = "";

/**
 * Function to print Bootstrap alerts for messages.
 * @param string $message The message to display.
 * @param string $type The Bootstrap alert type (e.g., 'success', 'danger', 'warning', 'info').
 */
function print_alert(string $message, string $type)
{
    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show text-center mx-auto" role="alert" style="width: fit-content;">';
    echo $message;
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
    </button>
    </div>
    ';
}

// Check for messages set in the session from a redirection (e.g., from payment.php or khalti_callback.php)
if (isset($_SESSION["money_success_message"])) {
    $success_message = $_SESSION["money_success_message"];
    unset($_SESSION["money_success_message"]);
}
if (isset($_SESSION["money_error_message"])) {
    $errors[] = $_SESSION["money_error_message"];
    unset($_SESSION["money_error_message"]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Money</title>
    <!-- Include Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../images/blood-drop.svg" type="image/x-icon">
    <!-- Custom styles (copied from dashboard.php for consistency) -->
    <style>
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        .navbar-nav .nav-item a , .dropdown a {
            position: relative;
            color: #777;
            text-transform: uppercase;
            margin-right: 10px;
            text-decoration: none;
            overflow: hidden;
        }

        .dropdown-menu , .dropdown-menu a:hover {
            background-color: #f8f88f; /* Change the color to match your navbar background */
        }

        .navbar-nav  li a:hover , .dropdown a:hover {
            color: #1abc9c !important;
        }

        /* Specific styles for this page's form layout */
        .donate-form-container {
            max-width: 500px;
            margin: 150px auto 50px auto; /* Adjust top margin to clear fixed navbar */
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body style="background-color: #f5f5dc;">

    <!-- Bootstrap navigation bar -->
    <div class="container" style="margin-bottom: 100px;">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-shading" style="background-color:#f8f88f;">
        <a class="navbar-brand" href="../index.php" style="color: #777;font-size:22px;letter-spacing:2px;">RAGATCHAIYO</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php?home=1">Home</a>
                </li>
                <li class="nav-item active"> <!-- Highlight the current page (Donate Money) -->
                    <a class="nav-link" href="donatemoney.php">Donate Money <span class="sr-only">(current)</span></a>
                </li>
                <li>
                    <?php
                    // Display donor username and dropdown menu
                    echo 
                    '
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding-left:0px;">
                            '.$_SESSION['donor'].'
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item" href="dashboard.php?profile=1">Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="dashboard.php?logout=1">Logout</a>
                            </li>
                        </ul>
                    </div>
                    ';
                    ?>
                </li>
            </ul>
        </div>
    </nav>
    </div>

    <div class="container donate-form-container">
        <h2 class="text-center mb-4">Donate Money</h2>
        <p class="text-center mb-4">Your monetary contribution helps us operate and reach more people in need.</p>

        <?php
        // Display success message if available
        if (!empty($success_message)) {
            print_alert($success_message, "success");
        }

        // Display error messages if any validation failed
        if (!empty($errors)) {
            foreach ($errors as $error) {
                print_alert($error, "danger");
            }
        }
        ?>

        <form action="payment.php" method="post">
            <div class="form-group">
                <label for="amount">Donation Amount (NPR):</label>
                <input type="number" class="form-control" id="amount" name="amount" min="10" max="1000" step="1" required placeholder="Enter amount between 10 and 1000">
                <small class="form-text text-muted">Minimum donation: 10 NPR, Maximum donation: 1000 NPR (whole numbers only).</small>
            </div>
            <button type="submit" name="initiate_khalti_payment" class="btn btn-primary btn-block" style="background-color: #1abc9c; border-color: #1abc9c;">Donate Now with Khalti</button>
        </form>
        <p class="text-center mt-3"><a href="dashboard.php?home=1">Back to Dashboard</a></p>
    </div>

    <!-- Include Bootstrap JS and jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>