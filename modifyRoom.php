<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Edit Room</title>
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
        $room_id = $_GET['room'];
        $session_id = $_SESSION['id'];

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        echo('<script> var pid = ' . json_encode($pid) . '</script>');
        echo('<script> var room_id = ' . json_encode($room_id) . '</script>');

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

            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `type`, `name`, `available` FROM `Rooms` WHERE id = ?");
            $stmt->bind_param('i', $room_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($room_type, $room_name, $available);
            $stmt->fetch();
            $stmt->close();

            $user_id = 0;
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `user_id`, `view`, `modify`, `pay` FROM `User_X_Room` WHERE room_id = ? AND modify = 1");
            $stmt->bind_param('i', $room_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $view, $modify, $pay);
            $stmt->fetch();
            $stmt->close();

            $old_user_id = $user_id;

            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `firstName`, `lastName` FROM `ESF_users` WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($firstName, $lastName);
            $stmt->fetch();
            $stmt->close();

            // print('Room name: ');
            // print_r($room_name);
            // print('<br>');
            // print('Room type: ');
            // print_r($room_type);
            // print('<br>');
            // print('Room id: ');
            // print_r($room_id);
            // print('<br>');
            // print('Available: ');
            // print_r($available);
            // print('<br>');
            // print('User id: ');
            // print_r($user_id);
            // print('<br>');
            // print('First name: ');
            // print_r($firstName);
            // print('<br>');
            // print('Last name: ');
            // print_r($lastName);
            // print('<br>');
            // print('Can view: ');
            // print_r($view);
            // print('<br>');
            // print('Can modify: ');
            // print_r($modify);
            // print('<br>');
            // print('Must pay: ');
            // print_r($pay);
            // exit(0);

            echo "<script>" . 
                    "window.room_name = " . json_encode($room_name) . ";" .
                    "window.room_type = " . json_encode($room_type) . ";" .
                    "window.room_id = " . json_encode($room_id) . ";" .
                    "window.available = " . json_encode($available) . ";";
            if (!$available) {
                echo "window.user_id = " . json_encode($user_id) . ";" .
                    "window.view = " . json_encode($view) . ";" .
                    "window.modify = " . json_encode($modify) . ";" .
                    "window.pay = " . json_encode($pay) . ";" .
                    "window.firstName = " . json_encode($firstName) . ";" .
                    "window.lastName = " . json_encode($lastName) . ";" .
                  "</script>";                
            } else {
                echo "</script>";
            }


        } else {
            header ('Location: home.php');
            exit();
        }

    ?>

</head>
<body>
    <? include('header.php'); ?>
    Edit Room
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='updateRoom.php' method='post'>
        <input type='text' class='centered' style='display: block;' id='room_name' name='room_name' placeholder='Room name...' required>
        <select style='text-align: center; display: block;' class='centered' name='room_type' id='room_type' required>
            <option value='Public'> Public
            <option value='Dorm'> Dorm
            <option value='Studio'> Studio
        </select>
        <div class='thin-wrapper'>
            <select style='text-align:center; display: block;' class='centered' id='user' name='user_id'>
                
                <?

                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("SELECT `firstName`, `lastName`, `id` FROM `ESF_users` WHERE landlord_id = ? AND landlord = 0 AND property_id = ? AND has_room = 0");
                    $stmt->bind_param('ii', $landlord_id, $pid);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($_firstName, $_lastName, $_user_id);

                    $count = 0;
                    while($stmt->fetch()) {
                        echo("<option value='" . $_user_id ."'>" . $_firstName . ' ' . $_lastName . "</option>");
                        $count++;
                    }

                    $stmt->close();


                    if ((int) $user_id != 0) {
                        $count++;
                        echo("<option value='" . $user_id ."'>" . $firstName . ' ' . $lastName . "</option>");  
                    }

                    if ($count == 0) {
                        echo("<option value='-1'>No Users Need Rooms</option>");
                    }

                ?>

                <option value='-1'>None</option>
            </select>
        </div>
        <input type='submit' value='Submit' class='btn btn-success'>
        <input type='hidden' name='property_id' value=<? echo $_GET['property']; ?>>
        <input type='hidden' name='old_user_id' value=<? echo $old_user_id; ?>>
        <input type='hidden' name='room_id' value=<? echo $room_id; ?>>
        <input type='hidden' name='old_room_type' value=<? echo $room_type; ?>>

    </form>
    </div>
    <script>
        $(document).ready(function() {
            $('#room_name').val(window.room_name);
            $('#room_type').val(window.room_type);
            //eventually need to add in view, modify, and pay permissions

            $('#user').val('-1');
            
            if (!window.available) {
                $('#user').val(window.user_id);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#room_type').change(function() {
                if ($(this).val() === 'Public') {
                    $('#user').val('-1');
                    $('.thin-wrapper').css('display', 'none');
                }  else {
                    $('.thin-wrapper').css('display', 'block');   
                }
            });


            $('.thin-wrapper').css('display', 'block');   
            if (window.room_type === 'Public') {
                $('#user').val('-1');
                $('.thin-wrapper').css('display', 'none');
            } 
        });
    </script>
</body>
</html>