<?php
// Check if there is someone already logged in
if(isset($_COOKIE["user"])) {
    header('Location: home.php'); //Redirect to home.php
}

include("connect-to-db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign in | Beehive Control</title>
    <link rel="icon" href="img/fav.ico" type="image/ico" sizes="16x16">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/footer-bottom.js"></script>
</head>

<body style="color: #dbb961; font-weight: 800; background-color: #fff4e0">
    <!-- Main Body -->
    <div class="container-fluid">
        <div style="padding-top: 10px; padding-bottom: 10px" class="text-center">
            <img style="width: 10%; height: auto; min-width: 80px;" src="img/logo.gif">
        </div>
        <div class="row">
            <div class="col-sm-4 col-xs-1"></div>
            <div style="background-color: #5f5f5fdd; padding: 30px; border-radius: 25px; margin-bottom: 30px;" class="col-sm-4 col-xs-10">
                <form method="post" action="index.php">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control" id="pwd" name="password" required>
                    </div>
                    <button style="background-color: #dbb961;" type="submit" class="btn">Sign in</button>
                    <?php
                    if(isset($_POST["username"], $_POST["password"])) {
                        $sql = "SELECT password FROM users WHERE username = '".$_POST["username"]."'"; // Get the password for the given username
                        $hashed_pass = mysqli_query($conn, $sql)->fetch_assoc(); // Execute query
                        if(!empty($hashed_pass["password"])) {
                            $hashed_pass = $hashed_pass["password"];
                        }

                        // Check if given password (hash) value is equal to the password stored in db
                        if (password_verify($_POST["password"], $hashed_pass)) {
                            setcookie("user", $_POST["username"], time() + (86400 * 30), "/"); // Create a cookie with user's username
                            header("Location: home.php"); // Redirect to index.php
                        } else {
                            echo "<br><br><p style='color: darkred'>Wrong Username or Password</p>";
                        }
                    }
                    ?>
                </form>

                <br>
                <p>Don't have an account? Create one <a href="sign-up.php">here</a>!</p>
            </div>
            <div class="col-sm-4 col-xs-1"></div>
        </div>
    </div>
    <!-- Main Body -->

    <?php
    include("footer.php");
    ?>
</body>

</html>




