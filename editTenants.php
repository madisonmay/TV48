<!DOCTYPE html>
<html lang="en">
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Tenants</title>
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
</head>
<body>
    <? include('header.php'); ?>
    Tenants
    <? include('header2.php'); ?>
    <select style='text-align: center; display: block;' class='centered' id='tenant'>
        <?

            $username = 'thinkcore';
            $password = 'K5FBNbt34BAYCZ4W';
            $database = 'thinkcore_drupal';
            $server = 'localhost';
            $pid = $_GET['property'];

            echo "<script> var property = '" . $pid . "'</script>";
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

            if ($landlord) {

                $stmt = $mysqli->stmt_init();
                $stmt->prepare("SELECT `firstName`, `lastName`, `id` FROM `ESF_users` WHERE landlord_id = ? AND landlord = 0 AND property_id = ?");
                $stmt->bind_param('ii', $landlord_id, $pid);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($firstName, $lastName, $user_id);
                while ($stmt->fetch()) {
                    echo("<option value='" . $user_id ."'>" . $firstName . ' ' . $lastName . "</option>");
                }
                $stmt->close();

            } else {
                header ('Location: home.php');
                exit();
            }

        ?>

    </select>
    <button type='submit' class='centered btn btn-success'> Edit </button>
    </div>
    <script>
        $(document).ready(function() {
            $('.btn-success').click(function() {
                var url = 'modifyTenant.php';
                var tenant = $('#tenant').val();
                window.location = url + '?tenant=' + tenant + '&property=' + property;
            });
        });
    </script>
</body>
</html>