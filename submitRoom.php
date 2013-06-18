<? 

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //There should really be a way to link new users to existing objects in the databse
    //If this could be created, the landlord would not have to wait for all of the tenants
        //to register in the system for he/she to be able to set the permissions for each of the 
        //rooms.  The question is how can this be done in a secure manner?
    include ("ESF_config.php");
    include ("check.php");

    $room_type = $_POST['room_type'];
    $session_id = $_SESSION['id'];
    $tenant = $_POST['tenant'];
    $room_title = $_POST['room_title'];
    $pid = $_POST['property_id'];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        // Opens a connection to a MySQL server.
        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        function addStudio($mysqli, $room_id, $user_id) {
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("UPDATE `ESF_users` SET has_studio=1 WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->close();

            $stmt = $mysqli->stmt_init();
            $stmt->prepare("UPDATE `User_X_Room` SET pay=0 WHERE user_id = ? AND pay=1 and modify=0");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->close();
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($landlord, $landlord_id);
        $stmt->fetch();
        $stmt->close();

        if (!$landlord) {
            header ('Location: login.php');
            exit(0);
        }

         if (!($stmt)) {
            die('Invalid query: ' . mysql_error());
        }

        if ($tenant != "-1" || $room_type == 'Public') {
            $available = 0;
        } else {
            $available = 1;
        }

        //Debugging

        // print_r($tenant);
        // print('<br>');
        // print_r($room_type);
        // print('<br>');
        // print($available);
        // print('<br>');
        // exit(0);

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("INSERT INTO `Rooms` (name, property_id, type, available) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sisi', $room_title, $pid, $room_type, $available);
        $stmt->execute();
        $stmt->store_result();
        $stmt->close();

        //get the id of the room that was just inserted into Rooms
        $stmt = $mysqli->stmt_init();
        $stmt->prepare('SELECT `id` FROM `Rooms` WHERE `property_id` = ? ORDER BY `id` DESC LIMIT 1');
        $stmt->bind_param('i', $pid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($room_id);
        $stmt->fetch();
        $stmt->close();  

        if (!($stmt)) {
            die('Invalid query: ' . mysql_error());
        } else {

            if (!$available && $tenant != "-1") {

                $stmt = $mysqli->stmt_init();
                $stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, LAST_INSERT_ID(), 1, 1, 1, ?)");
                $stmt->bind_param('ii', $tenant, $pid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->close();   

                if ($room_type != 'Public') {

                    if ($room_type == 'Studio') {
                        $has_studio = 1;
                    } else {
                        $has_studio = 0;
                    }

                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare("UPDATE `ESF_users` SET has_room=1, has_studio=? WHERE id=?");
                    $stmt->bind_param('ii', $has_studio, $tenant);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->close();   
                }
            } elseif ($room_type == 'Public') {
                //add User_x_room permssions, set view to 1              

                $stmt = $mysqli->stmt_init();
                $stmt->prepare('SELECT `id`, `has_studio` FROM `ESF_users` WHERE landlord_id = ? AND landlord != 1 and property_id = ?');
                $stmt->bind_param('ii', $landlord_id, $pid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($_user_id, $has_studio);

                //should do our best to avoid for loops
                while ($stmt->fetch()) {
                    //for each user of the property, add view access by adding entry to the cross table
                    if ($has_studio) {
                        $pay_public = 0;
                    } else {
                        $pay_public = 1;
                    }

                    $_stmt = $mysqli->stmt_init();
                    $_stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, ?, 0, ?)");
                    $_stmt->bind_param('iiii', $_user_id, $room_id, $pay_public, $pid);
                    $_stmt->execute();
                    $_stmt->store_result();
                    $_stmt->close(); 
                }

                $stmt->close(); 

            } 

            if (($room_type == 'Studio') && $tenant != "-1"){
                addStudio($mysqli, $room_id, $tenant);
            }

            header ('Location: editProperty.php');
            exit(0);
        }

    } else {
        header ('Location: login.php');
        exit(0);
    }

?>