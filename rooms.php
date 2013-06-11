<!DOCTYPE html>
<html lang="en">
<head>
    <title>TV48 - Manage Rooms</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php') ?>
    <style>
        .spaced {
            margin-right: 5px;
            margin-left: 5px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .btn {
            width: 200px;
        }

    </style>

    <?

        $username = 'thinkcore';
        $password = 'K5FBNbt34BAYCZ4W';
        $database = 'thinkcore_drupal';
        $server = 'localhost';

        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $stmt = $mysqli->stmt_init();

        $stmt->prepare("SELECT `streamId`, `location`, `public`, `userId`, `user`  FROM `lightStreams` WHERE pwm >= 0");

        $stmt->execute();

        $stmt->store_result();

        $stmt->bind_result($streamId, $location, $public, $userId, $user);

        $lights = array();

        while ($stmt->fetch()) {
            if (!$public) {
                $public = "locked";
            } else {
                $public = "public";
            }
            $light = array("streamId" => $streamId, "location" => $location, "public" => $public, "userId" => $userId, "user" => $user);
            array_push($lights, $light);
        }

        $stmt->close();

        echo "<script> window.lights = " . json_encode($lights) . "</script>";

        $stmt = $mysqli->stmt_init();

        $stmt->prepare("SELECT `firstName`, `lastName`, `id` FROM `ESF_users` WHERE `landlord` = 0");

        $stmt->execute();

        $stmt->store_result();

        $stmt->bind_result($firstName, $lastName, $id);

        $tenants = array();

        while ($stmt->fetch()) {
            $tenant = array("name" => $firstName . ' ' . $lastName, "id" => $id);
            array_push($tenants, $tenant);
        }

        $stmt->close();

        echo "<script> window.tenants = " . json_encode($tenants) . "</script>";

    ?>
</head>
<body>
    <? include('header.php') ?>
    Manage Permissions
    <? include('header2.php') ?>
    <div id="content">
    </div>
    </div>
    <script src='scripts/rooms.js'></script>
</body>
</html>