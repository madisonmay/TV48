<!DOCTYPE html>
<html lang="en"></html>
<head>
    <title>TV48 - Lights</title>
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
    <style>
        /*body initially hidden to prevent flickering*/
        body {
            display: none;
        }

    </style>
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

        $stmt->prepare("SELECT `streamId`, `pwm`, `location`, `request`  FROM `lightStreams` WHERE pwm >= 0");

        $stmt->execute();

        $stmt->store_result();

        $stmt->bind_result($streamId, $pwm, $location, $request);

        $lights = array();

        while ($stmt->fetch()) {
            $light = array("streamId" => $streamId, "pwm" => $pwm, "location" => $location, "request" => $request);
            array_push($lights, $light);
        }
        echo "<script> window.lights = " . json_encode($lights) . "</script>";

    ?>
    <script>

      Array.prototype.sortByProp = function(p){
          return this.sort(function(a,b){
              return (a[p] > b[p]) ? 1 : (a[p] < b[p]) ? -1 : 0;
          });
      }

      function render_page() {

          $('.row-fluid').remove();

          var window_width = $(window).width();

          //Varying numbers of columns for different window sizes
          if (window_width > 1000) {
              var num_cols = 4;
          } else if (window_width > 650) {
              var num_cols = 3;
          } else {
              var num_cols = 2;
          }

          //initialize all icons and sliders
          for (var i = 0; i < window.lights.length; i++) {

              if ((i%(num_cols)) == 0) {
                  $('body').append('<div class="row-fluid"></div>');
              }

              var brightness = window.lights[i]['pwm']/500.0;
              var room_name = window.lights[i]['location'];
              var streamId = window.lights[i]['streamId'];

              var room = '<div class="span' + 12/num_cols + '">' +
                  '<img class="centered bulb-off" src="images/bulb_off.png"></img>' +
                  '<img class="centered up bulb-on" src="images/bulb_on.png"></img>' +
                  '<div modified=0 streamId="' + streamId + '" id="slider' + i + '" style="width: 200px; display: block; margin-left: auto; margin-right: auto; margin-top: 128px"></div>' +
                  '<div class="labels">' +
                      '<div>' + room_name + '</div>' +
                      '<div class="brightness">Brightness:</div>' +
                      '<div class="amount"></div>' +
                  '</div>' +
                  '</div>';

              $(".row-fluid ").last().append(room);

              //bind slider function to slider
              $("#slider" + i.toString()).slider({
                  value: brightness,
                  min: 0,
                  max: 100,
                  step: 1,
                  slide: function( event, ui ) {
                      $(this).next('.labels').children(".amount").html( ui.value );
                      $(this).prev(".bulb-on").css({'opacity': ui.value/100.0});
                      $(this).prev(".bulb-off").css({'opacity': 1-ui.value/100.0});
                      $(this).attr("modified", 1);
                  }
              });

              //initial slider values
              var slider = $( "#slider" + i.toString());
              var pwm = slider.slider( "value" );
              slider.next('.labels').children(".amount").html(pwm);
              slider.prev(".bulb-on").css({'opacity': pwm/100.0});
              slider.prev(".bulb-off").css({'opacity': pwm/100.0});
          }

          //slight delay needed to fix flickering issue
          setTimeout(function(){$('body').css('display', 'block')}, 100);
      }

      $(document).ready(function() {

        window.lights.sortByProp('pwm');
        window.lights.reverse();

        render_page();
        reset_modified();
        //Used for testing
        // var num_sliders = 8;
        // var room_names = ['Bedroom', 'Bathroom', 'Kitchen', 'Dining Room', 'Living Room'];

        function update_all(value) {
            for (var i = 0; i < window.lights.length; i++) {
                var slider = $('#slider' + i.toString());
                slider.slider("value", value);
                slider.attr('modified', 1);
                slider.next('.labels').children(".amount").html(slider.slider( "value" ));
                slider.prev(".bulb-on").css({'opacity': slider.slider( "value" )/100.0});
                slider.prev(".bulb-off").css({'opacity': slider.slider( "value" )/100.0});
            }
        }

        function reset_modified() {
            for (var i = 0; i < window.lights.length; i++) {
                var slider = $('#slider' + i.toString());
                slider.attr('modified', 0);
            }
        }

        $('.all-on').click(function() {
            update_all(100);
        })

        $('.all-off').click(function() {
            update_all(0);
        })

        $('.sort-by').change(function() {
            if ($(this).val() === 'pwm') {
                window.lights.sortByProp('pwm');
                window.lights.reverse();
            } else if ($(this).val() === 'location') {
                window.lights.sortByProp('location');
            }
            render_page();
        })

        $('.toggle-sort').click(function() {
            window.lights.reverse();
            render_page();
        })

        function retrieve_all() {
            var values = [];
            for (var i = 0; i <= window.lights.length; i++) {
                var slider = $('#slider' + i.toString());
                var value = slider.next('.labels').children(".amount").html();
                var streamId = slider.attr('streamId');
                if (slider.attr('modified') === '1') {
                  console.log(slider.attr('modified'));
                  values.push({'streamId': streamId, 'pwm': value});
                }
            }
            console.log(values);
            return values
        }

        $('.update').click(function() {
            var values = retrieve_all();
            reset_modified();
            console.log("Values: ", values);
            $.post('editLights.php', {'values': values}, function(data) {
                console.log(data);
            })
        })
      });
    </script>
</head>

<!-- Base html -->
<body>
    <? include('header.php') ?>
    TV48
    <? include('header2.php') ?>
    <div class='top_group'>
        <button class='btn all-off'>All Off</button>
        <button class='btn btn-success update'>Update</button>
        <button class='btn all-on'>All On</button>
    </div>
    <div class='top_group'>
       `<select class='sort-by'>
          <option value='pwm'>Sort by brightness</option>
          <option value='location'>Sort by name</option>
        </select>
        <button class='btn toggle-sort'>↑↓</button>
    </div>
</body>