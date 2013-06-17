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
            $available = (bool) 0;
        } else {
            $available = (bool) 1;
        }

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
            }

            header ('Location: editProperty.php');
            exit(0);
        }

    } else {
        header ('Location: login.php');
        exit(0);
    }

?>