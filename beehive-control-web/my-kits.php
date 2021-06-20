<?php
// Check if no one is logged in
if(!isset($_COOKIE["user"])) {
    header('Location: index.php'); //Redirect to index.php
}

include("connect-to-db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Kits | Beehive Control</title>
    <link rel="icon" href="img/fav.ico" type="image/ico" sizes="16x16">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/footer-bottom.js"></script>
</head>

<body style="font-weight: 800; background-color: #fff4e0;">
    <nav style="padding-left: 30px; padding-right: 30px;" class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="home.php">
            <img src="img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
            Beehive Control
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                        echo '<i class="fa fa-user"></i>&nbsp;&nbsp;'.$_COOKIE["user"];
                        ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="log-out.php">Log out</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid" style="background-image: url('img/honey-background.png'); background-repeat: no-repeat;">
        <div class="row">
            <div class="col-sm-1 col-xs-1"></div>
            <div class="col-sm-10 col-xs-10">
                <div style="margin-top: 30px" class="btn-group">
                    <a href="home.php" type="button" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="col-sm-1 col-xs-1"></div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-xs-1"></div>
            <div style="background-color: #5f5f5fdd; padding: 30px; margin-top: 30px; border-radius: 25px;" class="col-sm-4 col-xs-10">
                <h3 style="color: #dbb961; font-weight: 800">Add new Kit:</h3>
                <form method="post" action="my-kits.php" autocomplete="off">
                    <div class="form-group">
                        <p style="font-size: small; font-weight: 500;">Insert the 5 letter code of your Kit, in order to link it with your account.</p>
                        <label for="kitid">Kit ID:</label>
                        <input type="text" class="form-control" id="kitid" maxlength="5" minlength="5" name="kitid" required>
                    </div>
                    <button style="background-color: #dbb961;" type="submit" class="btn">Add</button>
                    <?php
                    if(isset($_POST["kitid"])) {
                        $sql = "SELECT kid FROM kit WHERE kid = '".$_POST["kitid"]."'"; // Get a kit id that's equal to the given kit id
                        $kitid = mysqli_query($conn, $sql)->fetch_assoc(); // Execute query
                        if(!empty($kitid["kid"]))
                            $kitid = $kitid["kid"];
                        if($kitid == $_POST["kitid"]) {
                            $sql = "SELECT username FROM kit WHERE kid = '".$kitid."'"; // Get the username linked with this kit id
                            $username = mysqli_query($conn, $sql)->fetch_assoc(); // Execute query
                            if($username["username"] == $_COOKIE["user"]) {
                                echo "<br><br><p style='color: darkred'>Kit id is already linked with your account!</p>";
                            } else {
                                echo "<br><br><p style='color: darkred'>Kit id is linked with a different account!</p>";
                            }
                        } else {
                            $sql = "INSERT INTO kit VALUES ('".$_POST["kitid"]."', '".$_COOKIE["user"]."')";
                            if(mysqli_query($conn, $sql))
                                echo "<br><br><p style='color: darkgreen'> Kit added successfully!</p>";
                        }
                    }
                    ?>
                </form>
            </div>
            <div class="col-sm-4 col-xs-1"></div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-xs-1"></div>
            <div style="background-color: #5f5f5fdd; padding: 30px; margin-top: 30px; margin-bottom: 30px; border-radius: 25px;" class="col-sm-4 col-xs-10">
                <h3 style="color: #dbb961; font-weight: 800">My Kits:</h3>
                <?php
                $sql = "SELECT kid FROM kit WHERE username = '".$_COOKIE["user"]."'"; // Get user's kitids
                $result = mysqli_query($conn, $sql); // Execute query
                if (mysqli_num_rows($result) > 0) {
                    echo '<ul>';
                    while($row = mysqli_fetch_assoc($result)) {
                        echo '<li>'.$row["kid"].'</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p style="color:darkred;">There was no Kit linked with your account.</p>';
                }
                ?>
            </div>
            <div class="col-sm-4 col-xs-1"></div>
        </div>
    </div>

    <?php
    include("footer.php");
    ?>
</body>

</html>

<?php
?>



