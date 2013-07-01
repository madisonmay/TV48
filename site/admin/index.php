<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check_admin.php");
    ?>

    <title>TV48 - Properties</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base_admin.php'); ?>

    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->
</head>
<body>
    <? include('header_admin.php'); ?>
    Properties
    <? include('header2_admin.php'); ?>
    <select style='text-align: center; display: block;' class='centered' id='property'>
        <?

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

            //move to external file
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `admin`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
            $stmt->bind_param('s', $session_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($admin, $landlord_id);
            $stmt->fetch();
            $stmt->close();

            if ($admin) {

                $stmt = $mysqli->stmt_init();
                $stmt->prepare("SELECT `property_id`, `property_name` FROM `Property_X_Landlord` WHERE landlord_id = ?");
                $stmt->bind_param('s', $landlord_id);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($property_id, $property_name);
                while ($stmt->fetch()) {
                    echo("<option value='" . $property_id ."'>" . $property_name . "</option>");
                }
                $stmt->close();

            } else {
                header ('Location: ../home.php');
                exit();
            }

        ?>

    </select>
    <div style='text-align: center;'>
        <button type='button' class='btn redirect button-fixed-width' url='addSensor.php'>Add Sensor</button>
        <button type='button' class='btn redirect button-fixed-width' url='editSensors.php'>Edit Sensors</button>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.redirect').click(function() {
                var url = $(this).attr('url');
                var property = $('#property').val();
                window.location = url + '?property=' + property;
            });
        });
    </script>
</body>
</html>