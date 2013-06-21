<!DOCTYPE html>
<html lang="en">
<head>
    <title>TV48 - Manage Tenants</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type='text/css' href="stylesheets/bootstrap-combined.min.css">
    <link rel="stylesheet" type='text/css' href="stylesheets/jquery-ui.css">
    <link rel="stylesheet" type='text/css' href="style.css">
    <script src="scripts/jquery.min.js"></script>
    <script src="scripts/bootstrap.min.js"></script>
    <script src="scripts/jquery-ui.min.js"></script>

    <style>
        .spaced {
            margin-right: 5px;
            margin-left: 5px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .btn {
            width: 200px;
        }

    </style>

    <?

        session_start();
        include('ESF_config.php');

        $mysqli = new mysqli($server, $username, $password, $database);

        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }


        function sendVariable($variable, $varname, $global = 1) {
            if ($global) {
                echo("<script> window" . $varname . " = " . json_encode($variable) . "</script>");
            } else {
                echo("<script> var " . $varname . " = " . json_encode($variable) . "</script>");
            }
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT `firstName`, `lastName`, `id`, `rooms` FROM `ESF_users` WHERE `landlord` = 0");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($firstName, $lastName, $id, $rooms);
        $tenants = array();

        while ($stmt->fetch()) {
            $tenant = array("name" => $firstName . ' ' . $lastName, "id" => $id, "rooms" => $rooms);
            array_push($tenants, $tenant);
        }

        $stmt->close();
        sendVariable($tenants, "tenants");

    ?>
</head>
<body>
    <? include('header.php') ?>
    Manage Permissions
    <? include('header2.php') ?>
    <table class="table table-bordered table-hover" style='background-color: white'>
    <colgroup>
      <col style='width: 10%'>
      <col style='width: 45%'>
      <col style='width: 45%'>
    </colgroup>
    <thead>
      <tr>
        <th style='text-align: center;'>Name</th>
        <th style='text-align: center;'>Permitted</th>
        <th style='text-align: center;'>Restricted</th>
      </tr>
    </thead>
    <tbody id='table-body'>
    </tbody>
    </table>
    </div>
    <script src='scripts/manage.js'></script>
</body>
</html>