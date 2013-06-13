<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("../check.php");
    ?>

    <title>TV48 - Administration</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <script src="../scripts/jquery.min.js"></script>
    <script src="../scripts/bootstrap.min.js"></script>
    <script src="../scripts/jquery-ui.min.js"></script>
    <script src="../scripts/combobox.js"></script>
    <link rel="stylesheet" type='text/css' href="../stylesheets/bootstrap-combined.min.css">
    <link rel="stylesheet" type='text/css' href="../stylesheets/jquery-ui.css">
    <link rel='stylesheet' type='text/css' href='../stylesheets/nv.d3.css'>
    <link rel='stylesheet' type='text/css' href='../stylesheets/combobox.css'>
    <link rel="stylesheet" type='text/css' href="../style.css">
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
        $stmt->prepare("SELECT `admin` FROM `ESF_users` WHERE sessionId = ?");
        $stmt->bind_param('s', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($admin);
        $stmt->fetch();
        $stmt->close();

        if (!$admin) {
            echo('<p>' . $admin . '</p>');
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
    <!--[if lt IE 9]>
        <div style='text-align: center; border: 5px solid #333333;'>We're sorry.  We do not currently support versions of Internet Explorer earlier than 9.0.  Please either upgrade to a<a href='http://windows.microsoft.com/en-us/internet-explorer/ie-10-worldwide-languages'> more recent version of Internet Explorer </a> or view this site in a <a href='www.google.com/chrome'>different browser</a>.  Thanks!</div>
    <![endif]-->
    <div class="hide-this">
    <a href='../home.php' id='home'><img src='../images/home.png' class='home-button'></a>
    <a href='../logout.php' id='logout'><img src='../images/power.png' class='logout-button'></a>
    <div class="full-width px100 center-text dark-text min-width">
        <h1 class="large-text" id='home'>
            Administration
        </h1>
        <hr class="fade_line">
    </div>
    <div style='text-align: center;'>
        <button type='button' class='btn redirect' url='addLandlord.php'>Add Landlord</button>
        <button type='button' class='btn redirect' url='editLandlord.php'>Edit Landlords</button>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.redirect').click(function() {
                var url = $(this).attr('url');
                window.location = url;
            });
        });
    </script>
</body>
</html>