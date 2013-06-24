<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check_admin.php");
    ?>

    <title>TV48 - Add Sensor</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base_admin.php'); ?>
    <style>

        .thin-wrapper {
            display: none;
        }

    </style>
    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->

    <?

        $pid = $_GET['property'];

        $username = 'thinkcore';
        $password = 'K5FBNbt34BAYCZ4W';
        $database = 'thinkcore_drupal';
        $server = 'localhost';

        $session_id = $_SESSION['id'];

        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `admin` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($admin);
        $stmt->fetch();
        $stmt->close();

        if (!$admin) {
            header ('Location: ../home.php');
            exit();
        }

    ?>

</head>
<body>
    <? include('header_admin.php'); ?>
    Add Sensor
    <? include('header2_admin.php'); ?>
    <form style='text-align: center;' action='submitSensor.php' method='POST'>
        <input type='text' class='centered' style='display: block;' name='name' placeholder='Sensor name...' required>
        <input type='number' class='centered' style='display: block;' name='channel' placeholder='Channel...' required>
        <select style='text-align:center; display: block;' class='centered' id='room' name='room_id'>
                
                <?

                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("SELECT `id`, `name` FROM `Rooms` WHERE property_id = ?");
                    $stmt->bind_param('i', $pid);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($room_id, $room_name);

                    $count = 0;
                    while($stmt->fetch()) {
                        $count++;
                        echo("<option value='" . $room_id ."'>" . $room_name . "</option>");
                    }

                    $stmt->close();

                    if ($count == 0) {
                        echo("<option value='-1'>No Rooms</option>");
                    }

                ?>

        </select>
        <select style='text-align: center; display: block;' class='centered' id='type' name='type'>
            <option value="Lighting"> Lighting
            <option value="Heating"> Heating 
            <option value="Electric"> Electric
        </div>
        <input type='submit' value='Submit' class='btn btn-success' style='margin-top: 15px;'>
    </form>
    </div>
</body>
</html>