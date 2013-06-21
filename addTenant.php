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

    <style>
        ::-webkit-inner-spin-button { display: none; }
    </style>

    <?

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
    Add Tenant
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='submitTenant.php' method='post'>
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

            <option value='-1'>None</option>
        </select>
        <select style='text-align: center; display: block;' id='language' nane='language' class='centered'>
            <option value='English'> English </option>
            <option value='Dutch'> Dutch </option>
        </select>
        <input type='submit' value='Submit' class='btn btn-success'>
        <input class="hidden" style='display: none;' name='property_id' value='<? echo $pid; ?>'>
    </form>
    </div>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({ stepMonths: 1 });

        });
    </script>
</body>
</html>