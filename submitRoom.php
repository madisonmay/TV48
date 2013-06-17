<? 

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //There should really be a way to link new users to existing objects in the databse
    //If this could be created, the landlord would not have to wait for all of the tenants
        //to register in the system for he/she to be able to set the permissions for each of the 
        //rooms.  The question is how can this be done in a secure manner?
    include ("ESF_config.php");
    include ("check.php");

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        // Opens a connection to a MySQL server.
        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $_SESSION["id"]);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        if (!$landlord) {
            header ('Location: login.php');
            exit(0);
        }

         if (!($stmt)) {
            die('Invalid query: ' . mysql_error());
        }

        if ($_POST['tenant'] != "-1" || $_POST['room_type'] == 'Public') {
            $available = 0;
        } else {
            $available = 1;
        }

        //Debugging

        // print_r($_POST['tenant']);
        // print('<br>');
        // print_r($_POST['room_type']);
        // print('<br>');
        // print($available);
        // print('<br>');
        // exit(0);

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("INSERT INTO `Rooms` (name, property_id, type, available) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sisi', $_POST["room_title"], $_POST['property_id'], $_POST['room_type'], $available);
        $stmt->execute();
        $stmt->store_result();
        $stmt->close();

        if (!($stmt)) {
            die('Invalid query: ' . mysql_error());
        } else {

            if (!$available && $_POST['tenant'] != "-1") {

                $stmt = $mysqli->stmt_init();
                $stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, LAST_INSERT_ID(), 1, 1, 1, ?)");
                $stmt->bind_param('ii', $_POST["tenant"], $_POST['property_id']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->close();   

                if ($_POST['room_type'] != 'Public') {
                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("UPDATE `ESF_users` SET has_room=1 WHERE id=?");
                    $stmt->bind_param('i', $_POST["tenant"]);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->close();   
                }
            } elseif ($_POST['room_type'] == 'Public') {
                //add User_x_room permssions, set view to 1

                //get the id of the room that was just inserted into Rooms
                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id` FROM `Rooms` WHERE `property_id` = ? ORDER BY `id` DESC LIMIT 1');
                $stmt->bind_param('i', $_POST['property_id']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($room_id);
                $stmt->close();                

                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id` FROM `ESF_users` WHERE landlord_id = ? AND landlord != 1 and property_id = ?');
                $stmt->bind_param('ii', $landlord_id, $_POST['property_id']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($_user_id);

                while ($stmt->fetch()) {
                    //for each user of the property, add view access by adding entry to the cross table
                    $_stmt = $mysqli->stmt_init();
                    $_stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 0, ?)");
                    $_stmt->bind_param('iii', $_user_id, $room_id, $_POST['property_id']);
                    $_stmt->execute();
                    $_stmt->store_result();
                    $_stmt->close(); 
                }
                $stmt->close(); 

            }

            header ('Location: editProperty.php');
            exit(0);
        }

    } else {
        header ('Location: login.php');
        exit(0);
    }

?>