<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Edit Tenant</title>
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
        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $pid = $_GET['property'];
        $tenant = $_GET['tenant'];

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        echo('<script> var pid = ' . json_encode($pid) . '</script>');

        //should probably be refactored to external file
        if ($landlord) {

            $authorized = 0;
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `property_id`, `property_name` FROM `Property_X_Landlord` WHERE landlord_id = ?");
            $stmt->bind_param('i', $landlord_id);
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
            $stmt->prepare("SELECT `firstName`, `lastName`, `tenant_id`, `has_room`, `email` FROM `ESF_users` WHERE id = ?");
            $stmt->bind_param('i', $tenant);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($firstName, $lastName, $tenant_id, $has_room, $email);
            $stmt->fetch();
            $stmt->close();


            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `room_id`, `view`, `modify`, `pay` FROM `User_X_Room` WHERE user_id = ? AND pay=1");
            $stmt->bind_param('i', $tenant);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($room_id, $view, $modify, $pay);
            $stmt->fetch();
            $stmt->close();

            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `name`, `type` FROM `Rooms` WHERE id = ?");
            $stmt->bind_param('i', $room_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($room_name, $room_type);
            $stmt->fetch();
            $stmt->close();

            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT `start_date`, `end_date`, `balance` FROM `Tenants` WHERE user_id = ?");
            $stmt->bind_param('i', $tenant);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($startDate, $endDate, $balance);
            $stmt->fetch();
            $stmt->close();


            echo "<script>" . 
                    "window.firstName = " . json_encode($firstName) . ";" .
                    "window.lastName = " . json_encode($lastName) . ";" .
                    "window.email = " . json_encode($email) . ";" .
                    "window.startDate = " . json_encode($startDate) . ";" .
                    "window.endDate = " . json_encode($endDate) . ";" .
                    "window.balance = " . json_encode($balance) . ";" .
                    "window.has_room = " . json_encode($has_room) . ";";
            if ($has_room) {
                echo "window.room_id = " . json_encode($room_id) . ";" .
                    "window.room_name = " . json_encode($room_name) . ";" .
                    "window.room_type = " . json_encode($room_type) . ";" .
                    "window.view = " . json_encode($view) . ";" .
                    "window.modify = " . json_encode($modify) . ";" .
                    "window.pay = " . json_encode($pay) . ";" .
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
    Edit Tenant
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='updateTenant.php' method='post'>
        <input type='text' class='centered' style='display: block;' id='firstName' name='firstName' placeholder='First name...' required>
        <input type='text' class='centered' style='display: block;' id='lastName' name='lastName' placeholder='Last name...' required>
        <input type='text' class='centered datepicker' style='display: block;' id='startDate' name='startDate' placeholder='Start date...' required>
        <input type='text' class='centered datepicker' style='display: block;' id='endDate' name='endDate' placeholder='End date...' required>
        <div class="input-prepend">
            <span class="add-on">€</span>
            <input type='text' class='centered' style='width: 180px;' id='balance' name='balance' placeholder='Account balance...'>
        </div>
        <input type='email' class='centered' style='display: block;' id='email' name='email' placeholder='Email...' required>
        <select style='text-align:center; display: block;' class='centered' id='room' name='room_id'>
            
            <?

                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id`, `name`, `type` FROM `Rooms` WHERE property_id = ? AND available = 1 AND type != "Public"');
                $stmt->bind_param('i', $pid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($room_id, $room_name, $room_type);

                $count = 0;
                while($stmt->fetch()) {
                    echo("<option value='" . $room_id ."'>" . $room_name . "</option>");
                    $count++;
                }

                $stmt->close();


                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `room_id` FROM `User_X_Room` WHERE user_id = ? AND pay=1 and modify=1');
                $stmt->bind_param('i', $tenant);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($user_room_id);
                $stmt->fetch();

                $old_room_type = '-1';
                if ($has_room != 0) {
                    $count++;
                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare('SELECT `name`, `type` FROM `Rooms` WHERE id = ?');
                    $stmt->bind_param('i', $user_room_id);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($room_name, $room_type);
                    $stmt->fetch();
                    echo("<option value='" . $user_room_id ."'>" . $room_name . "</option>");  
                    $old_room_type = $room_type;
                }

                if ($count == 0) {
                    echo("<option value='-1'>No Rooms Available</option>");
                }

            ?>

            <option value='-1'>None</option>
        </select>
        <select style='text-align: center; display: block;' id='language' nane='language' class='centered'>
            <option value='English'> English </option>
            <option value='Dutch'> Dutch </option>
        </select>
        <input type='submit' value='Submit' class='btn btn-success'>
        <input type='hidden' name='user_id' value=<? echo $tenant; ?>>
        <input type='hidden' name='property_id' value=<? echo $pid; ?>>
        <input type='hidden' name='old_room_id' value=<? echo $user_room_id; ?>>
        <input type='hidden' name='old_room_type' value=<? echo $old_room_type; ?>>
    </form>
    </div>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker();
            $('#firstName').val(window.firstName);
            $('#lastName').val(window.lastName);
            $('#startDate').val(window.startDate);
            $('#endDate').val(window.endDate);
            $('#email').val(window.email);
            $('#balance').val(window.balance);

            if (!window.has_room) {
                console.log("No room");
                $('#room').val('-1');
            } else {
                console.log(window.room_name);
                $('#room').val(window.room_id);
            }
        });
    </script>
</body>
</html>