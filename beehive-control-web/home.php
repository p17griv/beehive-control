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
    <title>Home | Beehive Control</title>
    <link rel="icon" href="img/fav.ico" type="image/ico" sizes="16x16">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
    <script src="js/footer-bottom.js"></script>
</head>

<body style="font-weight: 800; background-color: #fff4e0">
    <!-- Navbar -->
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
                        <a class="dropdown-item" href="my-kits.php">My Kits</a>
                        <a class="dropdown-item" href="log-out.php">Log out</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Navbar -->

    <!-- Main Body -->
    <div class="container-fluid" style="background-image: url('img/honey-background.png'); background-repeat: no-repeat;">
        <!-- Kit Selection Dropdown Button -->
        <div class="row">
            <div class="col-sm-1 col-xs-1"></div>
            <div class="col-sm-10 col-xs-10">
                <div style="margin-top: 30px" class="btn-group">
                    <?php
                    $sql = "SELECT kid FROM kit WHERE username = '".$_COOKIE["user"]."'"; // Get user's kitids
                    $result = mysqli_query($conn, $sql); // Execute query
                    if (mysqli_num_rows($result) > 0) {
                        if(isset($_GET["kit"]))
                            echo '<button style="margin-right: 5px;" type="button" class="btn btn-secondary" id="selected-kit">Kit: <span style="color: #dbb961; font-weight: 500">'.$_GET["kit"].'</span></button>';
                        echo '
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Select Kit
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">';
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<a href="home.php?kit='.$row["kid"].'" class="dropdown-item" type="button">'.$row["kid"].'</a>';
                        }
                        echo '</div>';
                    } else {
                        echo '
                        <a href="my-kits.php" type="button" class="btn btn-secondary">
                            Add new Kit
                        </a>';
                    }
                    ?>
                </div>
            </div>
            <div class="col-sm-1 col-xs-1"></div>
        </div>
        <!-- Kit Selection Dropdown Button -->

        <!-- Beehives' Information -->
        <div class="row">
            <div class="col-sm-1 col-xs-1"></div>
            <div style="background-color: #5f5f5fdd; padding: 30px; margin-top: 10px; margin-bottom: 30px; border-radius: 25px;" class="col-sm-10 col-xs-10">
                <div class="row" id="mainframe">
                    <div class="col-sm-3 col-1"></div>
                    <div class="col-sm-6 col-10">
                        <div style="color: #f8c923; border: 10px ridge #dbb961;border-radius: 25px; margin: 5px; text-align: center;">
                            <?php
                            if(isset($_GET["kit"]))
                                echo "<p>Establishing connection with sensor data...</p>";
                            else
                                if(mysqli_num_rows($result) > 0)
                                    echo "<p>Select one of your Kits in order to display sensor data!</p>";
                                else
                                    echo "<p>Add a new Kit!";
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-3 col-1"></div>
                </div>
            </div>
            <div class="col-sm-1 col-xs-1"></div>
        </div>
        <!-- Beehives' Information -->

    </div>
    <!-- Main Body -->

    <script>
        var node = document.getElementById('selected-kit');
	var prev_data = "";
        if(node != null) {
            kitid = node.textContent.split(':')[1].replace(' ','');
            mainframe = document.getElementById("mainframe");

            // Create a client instance: Broker, Port, Websocket Path, Client ID
            client = new Paho.MQTT.Client("100.24.224.29", Number(9001), "/ws", "js-utility-DI1m6");

            // Connect the client, providing an onConnect callback
            client.connect({
                onSuccess: onConnect
            });

            // set callback handlers
            client.onConnectionLost = function (responseObject) {
                console.log("Connection Lost: "+responseObject.errorMessage);
                mainframe.innerHTML = '<div class="col-sm-3 col-1"></div>' +
                    '<div class="col-sm-6 col-10">' +
                    '<div style="color: darkred; border: 10px ridge #dbb961;border-radius: 25px; margin: 5px; text-align: center;">'+
                    '<p>Lost connection with sensor data!</p>' +
                    '<div class="col-sm-3 col-1"></div>';
            }

            client.on_subscribe = function () {
                console.log("Subscribed to: '"+kitid+"' topic!");
            }

            client.onMessageArrived = function (message) {
                console.log("Message Arrived: "+message.payloadString);
                data = message.payloadString.split(",");
                if (data.toString().localeCompare(prev_data) != 0) {
					mainframe.innerHTML = "";
			    	prev_data = data;
			    	for (let i=1; i<data.length-1; i++) {
                    	beehive = data[i].split(":")[0];
                   		temperature = data[i].split(":")[1];
                    	weight = data[i].split(":")[2];

                    	normal_color = "#84e058";
                    	warning_color = "#f8e935";
                    	danger_color = "#b60000";

                    	temperature_color = normal_color;
                    	weight_color = normal_color;

                    	if (parseFloat(temperature) < 10 || parseFloat(temperature) > 35) {
                        	temperature_color = danger_color;
                        	alert("An extreme temperature of "+temperature+"°C was spotted in beehive "+beehive+"!");
                    	}
                    	else if (parseFloat(temperature) < 16 || parseFloat(temperature) > 27) {
                       		temperature_color = warning_color;
                        	alert("Temperature of "+temperature+"°C was spotted in beehive "+beehive+".");
                    	}

                    	if (parseFloat(weight) > 8) {
                    	    weight_color = danger_color;
                        	alert("Beehive "+beehive+" is almost full! \n Capacity: "+Math.round(parseFloat(weight) / 9 * 100 * 10) / 10+"%");
                    	}
                    	else if (parseFloat(weight) > 6) {
                       		weight_color = warning_color;
                        	alert("Beehive "+beehive+" is about to get full! \n Capacity: "+Math.round(parseFloat(weight) / 9 * 100 * 10) / 10+"%");
                    	}

                    	mainframe.innerHTML += '<div class="col-sm-4 col-6">' +
                        	'<div style="color: #dbb961; border: 10px ridge #dbb961;border-radius: 25px; margin: 5px; text-align: center;">' +
                        	'<p>Beehive: <span style="color: black; font-size: larger">'+beehive+'</span></p>' +
                        	'<img style="background-color: #fff4e0; border-radius: 25px; width: 50%;" src="img/logo.gif"/>' +
                        	'<p><i class="fa fa-thermometer-three-quarters"></i> Temperature: <span style="color: '+temperature_color+'; font-size: larger">'+temperature+'°C</span></p>' +
                        	'<p><i class="fa fa-flask"></i> Capacity: <span style="color: '+weight_color+'; font-size: larger">'+Math.round(parseFloat(weight) / 9 * 100 * 10) / 10+'%</span><br>'+weight+'/9kg</p>' +
                        	'</div>' +
                        	'</div>';
                	}
            	}
			}

            // Called when the connection is made
            function onConnect() {
                console.log("Connected!");
                client.subscribe(kitid);
            }

        }
    </script>

    <?php
    include("footer.php");
    ?>
</body>

</html>





