<!-- Home.php Todo List:
Add in statistics/dashboard
Add in link to management page(s) -->

<!DOCTYPE html>
<html lang="en"></html>
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Home</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>
    <title>TV48 - Home</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylesheets/bootstrap-combined.min.css">
    <link rel="stylesheet" href="stylesheets/jquery-ui.css">
    <link rel="stylesheet" href="style.css">
    <script src="scripts/jquery.min.js"></script>
    <script src="scripts/bootstrap.min.js"></script>
    <script src="scripts/jquery-ui.min.js"></script>
    <style>
        .home-button {
            opacity: 0;
            cursor: default;
        }

        .hidden {
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
    <script src='scripts/home.js'></script>
</head>
<body>
    <? include('header.php'); ?>
    TV48
    <? include('header2.php'); ?>
    <div class="row-fluid not-too-small">
        <div class="span4">
            <a href='light.php'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
        </div>
        <div class="span4">
            <a href='heat.php'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
        </div>
        <div class="span4">
            <a href='power.php'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
        </div>
    </div>
    </div>
</body>