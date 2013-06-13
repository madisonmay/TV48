<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Add Property</title>
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
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        if (!$landlord) {
            header ('Location: home.php');
            exit();
        }

    ?>

</head>
<body>
    <? include('header.php'); ?>
    Add Property
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='submitRoom.php' method='post'>
        <input type='text' class='centered' style='display: block;' placeholder='Property name...' required>
        <div class='thin-wrapper'>
            <select style='text-align: center; display: block;' class='centered' id='tenant' required>
                            
            <?
                $pid = $_GET['property'];

                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id`, `firstName`, `lastName` FROM `ESF_users` WHERE landlord_id = ? AND has_room = 0 and landlord != 1');
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

            </select>
        </div>
        <input type='submit' value='Submit' class='btn btn-success'>
    </form>

    </div>
</body>
</html>