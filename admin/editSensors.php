<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check_admin.php");
    ?>

    <title>TV48 - Edit Sensors</title>
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
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        if (!$landlord) {
            header ('Location: home.php');
            exit();
        }

    ?>

</head>
<body>
    <? include('header_admin.php'); ?>
    Edit Sensors
    <? include('header2_admin.php'); ?>
    <div class='row-fluid'>
        <div class='span4' style='text-align: center;'>
            <h2>Lighting</h2>
            <select style='display: block;' class='centered' id='room' name='room_id'>
                    
                <?

                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("SELECT `streamId`, `location` FROM `lightStreams`");
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($id, $name);

                    $count = 0;
                    while($stmt->fetch()) {
                        $count++;
                        echo("<option value='" . $id ."'>" . $name . "</option>");
                    }

                    $stmt->close();

                    if ($count == 0) {
                        echo("<option value='-1'>No Rooms</option>");
                    }

                ?>


            </select>
            <button class='btn btn-success' style='margin-right: auto; margin-left: auto;'>Edit</button>
        </div>
        <div class='span4' style='text-align: center;'>
            <h2>Heating</h2>
            <select style='display: block;' class='centered' id='room' name='room_id'>
                    
                <?

                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("SELECT `streamId`, `name` FROM `Heat`");
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($id, $name);

                    $count = 0;
                    while($stmt->fetch()) {
                        $count++;
                        echo("<option value='" . $id ."'>" . $name . "</option>");
                    }

                    $stmt->close();

                    if ($count == 0) {
                        echo("<option value='-1'>No Rooms</option>");
                    }

                ?>


            </select>
            <button class='btn btn-success' style='margin-right: auto; margin-left: auto;'>Edit</button>
        </div>
        <div class='span4' style='text-align:center;'>
            <h2>Electric</h2>
            <select style='display: block;' class='centered' id='room' name='room_id'>

                    <?

                        $stmt = $mysqli->stmt_init();
                        $stmt->prepare("SELECT `streamId`, `name` FROM `Power`");
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($id, $name);

                        $count = 0;
                        while($stmt->fetch()) {
                            $count++;
                            echo("<option value='" . $id ."'>" . $name . "</option>");
                        }

                        $stmt->close();

                        if ($count == 0) {
                            echo("<option value='-1'>No Rooms</option>");
                        }

                    ?>


            </select>
            <button class='btn btn-success' style='margin-right: auto; margin-left: auto;'>Edit</button>
        </div>
    </div>
    </div>
</body>
</html>