<!DOCTYPE html>
<html lang="en">
<head>
    <title>TV48 - Add Room</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>
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
            $('#user').val('-1');

            $('#room_type').change(function() {
                if ($(this).val() === 'Public') {
                    $('#user').val('-1');
                    $('.thin-wrapper').css('display', 'none');
                }  else {
                    $('.thin-wrapper').css('display', 'block');   
                }
            });
        });
    </script>

    <?

        include("check.php");

        $session_id = $_SESSION['id'];

        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }


        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        $pid = $_GET['property'];
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
    Add Room
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='submitRoom.php' method='post'>
        <input type='text' class='centered' style='display: block;' id='room_title' name='room_title' placeholder='Room title...' required>
        <select style='text-align: center; display: block;' class='centered' name='room_type' id='room_type' required>
            <option value='Public'> Public
            <option value='Dorm'> Dorm
            <option value='Studio'> Studio
        </select>
        <div class='thin-wrapper'>
            <select style='text-align: center; display: block;' class='centered' name='tenant' id='tenant' required>
                            
            <?
                $pid = $_GET['property'];

                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id`, `firstName`, `lastName` FROM `ESF_users` WHERE landlord_id = ? AND has_room=0 and landlord != 1');
                $stmt->bind_param('s', $landlord_id);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($user_id, $firstName, $lastName);

                $count = 0;
                while($stmt->fetch()) {
                    echo("<option value='" . $user_id ."'>" . $firstName . ' ' . $lastName . "</option>");
                    $count++;
                }

                $stmt->close();

                if ($count == 0) {
                    echo("<option value='-1'>No Tenants Need Rooms</option>");
                }

            ?>
                <option value='-1'>None</option>

            </select>
        </div>
        <input type='submit' value='Submit' class='btn btn-success'>
        <input class='hidden' name='property_id' value=<? echo($pid); ?>></div>
    </form>

    </div>
    <script>
        $(document).ready(function() {
            $('#tenant').val("-1");
        });
    </script>
</body>
</html>