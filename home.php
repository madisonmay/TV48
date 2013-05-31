<!DOCTYPE html>
<html lang="en"></html>
<head>
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
    <div class="hide-this">
    <a href='home.php' id='home'><img src='images/home.png' class='home-button'></a>
    <div class="full-width px100 center-text dark-text min-width">
        <h1 class="large-text" id='home'>TV48<h1>
        <hr class="fade_line">
    </div>
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