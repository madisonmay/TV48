<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Rooms</title>
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

</head>
<body>
    <? include('header.php'); ?>
    Rooms
    <? include('header2.php'); ?>
    <select style='text-align: center; display: block;' class='centered' id='room'>
        <?

            $pid = $_GET['property'];
            $session_id = $_SESSION['id'];

            echo "<script> var property = '" . $pid . "'</script>";

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

            if ($landlord) {

                $stmt = $mysqli->stmt_init();
                $stmt->prepare("SELECT `id`, `name` FROM `Rooms` WHERE property_id = ?");
                $stmt->bind_param('i', $_GET['property']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($room_id, $room_name);
                while ($stmt->fetch()) {
                    echo("<option value='" . $room_id ."'>" . $room_name . "</option>");
                }
                $stmt->close();

            } else {
                header ('Location: home.php');
                exit();
            }

        ?>

    </select>
    <button type='submit' class='centered btn btn-success'> Edit </button>
    </div>
    <script>
        $(document).ready(function() {
            $('.btn-success').click(function() {
                var url = 'modifyRoom.php';
                var room = $('#room').val();
                window.location = url + '?room=' + room + '&property=' + property;
            });
        });
    </script>
</body>
</html>