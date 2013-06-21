<?
    session_start();
    include('ESF_config.php');


    $mysqli = new mysqli($server, $username, $password, $database);

    /* check connection */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $js_lights = json_encode($_POST['values']);
        $php_lights = json_decode($js_lights);

        foreach ($php_lights as $light) {

            $brightness = 500*$light->pwm;

            if ($brightness <= 50000) {
                $stmt = $mysqli->stmt_init();
                $stmt->prepare("UPDATE `lightStreams` SET `pwm` = ?, `request` = 1, `time` = NOW() WHERE `streamId` = ?");
                $stmt->bind_param('ss', $brightness, $light->streamId);
                $stmt->execute();
                $stmt->store_result();
            }
        }

        echo true;
    }

?>
