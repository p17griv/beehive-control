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
    <title>Sign up | Beehive Control</title>
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
                <form method="post" action="sign-up.php">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control" id="pwd" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd"> Confirm Password:</label>
                        <input type="password" class="form-control" id="pwd" name="password2" required>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" required> I agree to the <a href="#">Terms & Conditions</a></label>
                    </div>
                    <button style="background-color: #dbb961;" type="submit" class="btn">Sign me up!</button>
                    <?php
                    if (isset($_POST["username"], $_POST["email"], $_POST["password"], $_POST["password2"])) {
                        $sql = "SELECT email FROM users WHERE email = '".$_POST["email"]."'"; // Get an user email that's equal to the given email
                        $email = mysqli_query($conn, $sql)->fetch_assoc(); // Execute query
                        if(!empty($email["email"]))
                            $email = $email["email"];
                        $sql = "SELECT username FROM users WHERE username = '".$_POST["username"]."'";
                        $username = mysqli_query($conn, $sql)->fetch_assoc(); // Execute query
                        if(!empty($username["username"]))
                            $username = $username["username"];
                        $hashed_pass = password_hash($_POST["password"],PASSWORD_BCRYPT ); // Hash the given password

                        // If password isn't equal to password confirmation
                        if ($_POST["password"] == $_POST["password2"]) {
                            // If there isn't an email in db that's equal with the given email
                            if ($email != $_POST["email"]) {
                                // If there isn't an username in db that's equal with the given username
                                if ($username != $_POST["username"]) {
                                    $sql = "INSERT INTO users VALUES ('".$_POST["username"]."', '".$hashed_pass."', '".$_POST["email"]."')";
                                    $conn->query($sql); // Execute query

                                    setcookie("user", $_POST["username"], time() + (86400 * 30), "/"); // Set a cookie with the new user's id as value
                                    header('Location: home.php'); // Redirect to home.php
                                } else {
                                    echo "<br><br><p style='color: darkred'>Username already exists!</p>";
                                }
                            } else {
                                echo "<br><br><p style='color: darkred'>Email already in use for another account!</p>";
                            }
                        } else {
                            echo "<br><br><p style='color: darkred'>Password confirmation fail!</p>";
                        }
                    }
                    ?>
                </form>

                <br>
                <p>Already have an account? Sign in <a href="index.php">here</a>!</p>
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

<?php
?>



