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

        $session_id = $_SESSION['id'];

        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $session_id);
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
    <form style='text-align: center;' action='submitProperty.php' method='POST'>
        <input type='text' class='centered' style='display: block;' name='name' placeholder='Property name...' required>
        <input type='submit' value='Submit' class='btn btn-success'>
    </form>
    </div>
</body>
</html>