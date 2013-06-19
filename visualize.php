<!DOCTYPE html>
<html lang="en">
<head>
    <title>TV48 - Visualization</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>
    <style>

        .thin-wrapper {
            display: none;
        }

        div#preload { 
            display: none; 
        }

        body {
            text-align: center;
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

        function template(string,data){
            return string.replace(/%(\w*)%/g,function(m,key){
                return data.hasOwnProperty(key)?data[key]:"";
            });
        }

        function co2(percent) {
            var cloud = "<div class='remove-me' style='display: inline-block; padding: 15px;'>" +
                             "<div id='gray%count%' style='width: 512px; height: 305px; margin-right: auto; margin-left: auto; background-color: #333333; padding-bottom: 0px; padding-top: 30px;'>" + 
                             "</div>" +
                             "<div style='text-align: center;'>" + 
                                "<img src='images/cloud3.png' style='margin-right: auto; margin-left: auto; margin-top: -397px;'>" +
                            "</div>" + 
                        "</div>";

            $('.remove-me').remove();
            var count = 1;
            while (percent > 1) { 
                percent = percent - 1;
                $('body').append(template(cloud, {'count': count}));
                count++;
            }

            $('body').append(template(cloud, {'count': count}));

            var height = percent * 305;
            var offset = 305 - height;
            $('#gray' + count).css('display', 'none');
            $('#gray' + count).css('height', height + 'px');
            $('#gray' + count).css('margin-top', offset + 'px');
            $('#gray' + count).css('display', 'block');
        }
    </script>
</head>
<body>
    <? include('header.php'); ?>
    CO2 Visualization
    <? include('header2.php'); ?>
</body>
</html>