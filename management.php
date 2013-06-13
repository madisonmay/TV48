<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Management</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>

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

    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->
</head>
<body>
    <? include('header.php'); ?>
    Management
    <? include('header2.php'); ?>
    <div style='text-align: center;'>
        <a href='addProperty.php'><button type='button' class='btn'>Add Property</button></a>
        <a href='editProperty.php'><button type='button' class='btn'>Edit Properties</button></a>
    </div>

    </div>
</body>
</html>