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
    <script>
      $(document).ready(function() {
        $('.border').mouseover(function() {
          $(this).stop(true).animate({"borderColor": "#46a546"}, 500);
          $(this).children('.circle').children('.opaque').stop(true)
            .animate({'opacity': '.9', '-moz-opacity': '.9', 'filter': 'Alpha(Opacity=90)',
                     '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=90}"'}, 500);
        });

        $('.border').mouseout(function() {
          $(this).stop(true).animate({"borderColor": "#333333"}, 500);
          $(this).children('.circle').children('.opaque').stop(true)
            .animate({'opacity': '0', '-moz-opacity': '0', 'filter': 'Alpha(Opacity=0)',
                     '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=0}"'}, 500);
        });
      });
    </script>
</head>
<body>
    <!--[if lt IE 9]>
        <div style='text-align: center; border: 5px solid #333333;'>We're sorry.  We do not currently support versions of Internet Explorer earlier than 9.0.  Please either upgrade to a<a href='http://windows.microsoft.com/en-us/internet-explorer/ie-10-worldwide-languages'> more recent version of Internet Explorer </a> or view this site in a <a href='www.google.com/chrome'>different browser</a>.  Thanks!</div>
    <![endif]-->
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