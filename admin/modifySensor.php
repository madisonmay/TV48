<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check_admin.php");
    ?>

    <title>TV48 - Edit Sensor</title>
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

    <script>
        $(document).ready(function() {
            $('#name').val(window.name);
            $('#channel').val(window.channel_id);
            $('#room').val(window.room_id);
        });
    </script>

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

        $sensor_id = $_GET['sensor'];

        function debug($params) {
            foreach ($params as $param) {
                if ($param) {
                    print_r($param);
                    print('<br>');
                } else {
                    print_r($param);
                    print(" - NULL - ");
                }
            }
            exit(0);            
        }

        //more special casing -- should be standardized so that all streams have the same properties
        if ($_GET['type'] === 'Lighting') {  
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `location`, `channel`, `roomId` FROM `lightStreams` WHERE streamId = ?");
            $stmt->bind_param('i', $sensor_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($name, $channel, $room_id);
            $stmt->fetch();
            $stmt->close();
            // debug(array($name, $channel, $room_id));
        } elseif ($_GET['type'] === 'Heating'){
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `name`, `channel`, `room_id` FROM `Heat` WHERE streamId = ?");
            $stmt->bind_param('i', $sensor_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($name, $channel, $room_id);
            $stmt->fetch();
            $stmt->close();
            // debug(array($name, $channel, $room_id));
        } else {
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `name`, `channel`, `room_id` FROM `Power` WHERE streamId = ?");
            $stmt->bind_param('i', $sensor_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($name, $channel, $room_id);
            $stmt->fetch();
            $stmt->close();
            // debug(array($name, $channel, $room_id));
        }

        echo("<script> window.name = " . json_encode($name) . "; </script>");
        echo("<script> window.channel_id = " . json_encode($channel) . "; </script>");
        echo("<script> window.room_id = " . json_encode($room_id) . "; </script>");
        // debug(array($name, $channel, $room_id));

    ?>

</head>
<body>
    <? include('header_admin.php'); ?>
    Edit Sensor
    <? include('header2_admin.php'); ?>
    <form style='text-align: center;' action='submitSensor.php' method='POST'>
        <input type='text' class='centered' style='display: block;' name='name' id='name' placeholder='Sensor name...' required>
        <input type='number' class='centered' style='display: block;' name='channel' id='channel' placeholder='Channel...' required>
        <select style='text-align:center; display: block;' class='centered' id='room' name='room_id'>
                
                <?

                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("SELECT `id`, `name` FROM `Rooms`");
                    //later, property_id should be checked
                    // $stmt->bind_param('i', 3);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($room_id, $room_name);

                    $count = 0;
                    while($stmt->fetch()) {
                        $count++;
                        echo("<option value=" . $room_id . ">" . $room_name . "</option>");
                    }

                    $stmt->close();

                    if ($count == 0) {
                        echo("<option value='-1'>No Rooms</option>");
                    }

                ?>

        </select>
        <input type='submit' value='Submit' class='btn btn-success' style='margin-top: 15px;'>
        <input type='hidden' value=<? echo json_encode($_GET['type']); ?>name='type'>
        <input type='hidden' value=<? echo json_encode($_GET['sensor']); ?>name='sensor_id'>
    </form>
    </div>
</body>
</html>