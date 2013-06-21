<?
    include('ESF_config.php');
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
    print_r(json_encode($lights));

?>