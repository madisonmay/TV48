<!DOCTYPE html>
<html lang="en"></html>
<head>
    <title>TV48 - Lights</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>
    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->
    <style>
        /*body initially hidden to prevent flickering*/
        body {
            display: none;
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
        $stmt->prepare("SELECT `streamId`, `pwm`, `location`, `request`  FROM `lightStreams` WHERE pwm >= 0");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($streamId, $pwm, $location, $request);
        $lights = array();

        while ($stmt->fetch()) {
            $light = array("streamId" => $streamId, "pwm" => $pwm, "location" => $location, "request" => $request);
            array_push($lights, $light);
        }
        echo "<script> window.lights = " . json_encode($lights) . "</script>";

    ?>
    <script src='scripts/light.js'></script>
</head>

<!-- Base html -->
<body>
    <? include('header.php') ?>
    TV48
    <? include('header2.php') ?>
    <div class='top_group'>
        <button class='btn all-off'>All Off</button>
        <button class='btn btn-success update'>Update</button>
        <button class='btn all-on'>All On</button>
    </div>
    <div class='top_group'>
       `<select class='sort-by'>
          <option value='pwm'>Sort by brightness</option>
          <option value='location'>Sort by name</option>
        </select>
        <button class='btn toggle-sort'>↑↓</button>
    </div>
</body>