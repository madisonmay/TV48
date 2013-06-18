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
    <script src="http://d3js.org/d3.v2.js"></script>
    <script src="nv.d3.js"></script>
    <? include('base.php'); ?>

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
    <div class="row-fluid">
        <div class="span3">
            <a href='light.php'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='heat.php'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='power.php'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='management.php'><div class="border">
                <div id="management" class="circle">
                    <div class="opaque">
                        <div class="label-text">Manage</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
    </div>
    </div>
</body>