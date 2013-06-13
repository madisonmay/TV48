<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Add Tenant</title>
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

        $pid = $_GET['property'];

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        echo('<script> var pid = ' . json_encode($pid) . '</script>');

        if ($landlord) {

            $authorized = 0;
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `property_id`, `property_name` FROM `Property_X_Landlord` WHERE landlord_id = ?");
            $stmt->bind_param('s', $landlord_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($property_id, $property_name);
            while ($stmt->fetch()) {
                if ($property_id == $pid) {
                    $authorized++;
                }
            }

            $stmt->close();

            if (!$authorized) {
                header ('Location: home.php');
                exit();
            }

        } else {
            header ('Location: home.php');
            exit();
        }

    ?>

</head>
<body>
    <? include('header.php'); ?>
    Add Tenant
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='submitTenant.php' method='post'>
        <input type='text' class='centered' style='display: block;' id='firstName' placeholder='First name...' required>
        <input type='text' class='centered' style='display: block;' id='lastName' placeholder='Last name...' required>
        <input type='email' class='centered' style='display: block;' id='email' placeholder='Email...' required>
        <select style='text-align:center; display: block;' class='centered' id='room'>
            
            <?
                $pid = $_GET['property'];

                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id`, `name`, `type` FROM `Rooms` WHERE property_id = ? AND available = 1 AND type != "Public"');
                $stmt->bind_param('s', $pid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($room_id, $room_name, $room_type);

                $count = 0;
                while($stmt->fetch()) {
                    echo("<option value='" . $room_id ."'>" . $room_name . "</option>");
                    $count++;
                }

                $stmt->close();

                if ($count == 0) {
                    echo("<option value='-1'>No Rooms Available</option>");
                }

            ?>
        </select>
        <select style='text-align: center; display: block;' id='language' class='centered'>
            <option value='English' selected='selected'> English
            <option value='Dutch'> Dutch
        </select>
        <input type='submit' value='Submit' class='btn btn-success'>
    </form>

    </div>
</body>
</html>