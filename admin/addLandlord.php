<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Add Landlord</title>
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

    ?>

</head>
<body>
    <? include('header.php'); ?>
    Add Landlord
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='submitLandlord.php' method='post'>
        <input type='text' class='centered' style='display: block;' id='firstName' placeholder='First name...' required>
        <input type='text' class='centered' style='display: block;' id='lastName' placeholder='Last name...' required>
        <input type='email' class='centered' style='display: block;' id='email' placeholder='Email...' required>
        <select style='text-align: center; display: block;' id='language' class='centered'>
            <option value='English' selected='selected'> English
            <option value='Dutch'> Dutch
        </select>
        <input type='submit' value='Submit' class='btn btn-success'>
    </form>

    </div>
</body>
</html>