<!DOCTYPE html>

<!--
Todo:
-Add security measure to prevent unwanted users from accessing feed
-Fix issues with date ranges that are too long to be displayed using test.php as a guide
-Fix timeline so that the proper type of date display is used depending on the duration
-->
<html>
  <head>
     <title>TV48 - Power</title>
      <? include("base.php"); ?>
      <link rel="stylesheet" type='text/css' href="stylesheets/plot.css">
      <script src="http://d3js.org/d3.v2.js"></script>
      <script src="nv.d3.js"></script>
      <script>
          window.data = {};
          window.times = {};
      </script>

      <?

          session_start();
          error_reporting(E_ALL);
          ini_set('display_errors', 1);
          $username = 'thinkcore';
          $password = 'K5FBNbt34BAYCZ4W';
          $database = 'thinkcore_drupal';
          $server = 'localhost';

          $landlord = 0;
          if( isset($_SESSION['id']) ) {
            // Opens a connection to a MySQL server.
            $mysqli = new mysqli($server, $username, $password, $database);

            /* check connection */
            if ($mysqli->connect_errno) {
              printf("Connect failed: %s\n", $mysqli->connect_error);
              exit();
            }

            $stmt = $mysqli->stmt_init();
            $stmt->prepare("SELECT * FROM `ESF_users` WHERE sessionID = ?");
            $stmt->bind_param('s', $_SESSION['id']);
            $stmt->execute();
            $stmt->store_result();

            if (!($stmt))
            {
              die('Invalid query: ' . mysql_error());
            }

            if ($stmt->num_rows != 0) {
              $stmt->close();
              $stmt = $mysqli->stmt_init();
              $stmt->prepare("SELECT landlord, rooms FROM `ESF_users` WHERE sessionID = ?");
              $stmt->bind_param('s', $_SESSION['id']);
              $stmt->execute();
              $stmt->bind_result($landlord, $rooms);
              $stmt->store_result();
              $stmt->fetch();
              $stmt->close();
            }
          }

          define('SECONDS_PER_DAY', 86400);

          // - 7200 is to account for 2 hour difference of time in Belgium to standard time
          // Should be made much more general

          //1/4 of a day = 6 hours
          $past = date("Y-m-d\TH:i:sP", time() - .25 * SECONDS_PER_DAY - 7200);
          $now = date("Y-m-d\TH:i:sP", time());

          //grab streamIds from xively
          $url = 'http://api.xively.com/v2/feeds/120903.json?key=';
          $key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';
          $request = $url . $key;

          //default duration is currently 6 hours
          $duration = '6hours';

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $request);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

          $resp = curl_exec($curl);
          $obj_resp = json_decode($resp);
          curl_close($curl);

          //pass date of creation to client side code
          echo "<script> var created = " . json_encode($obj_resp->created) . "</script>";

          $datastreams = $obj_resp->datastreams;

          $data_ids = array();

          $streamId = 'PTOTAL';

          //compile datapoints and strip unnecessary properties
          //new code recently added to check for global permissions ($landloard)
          //and if permissions are not found, give access to only the room
          //associated with the current user and the total for the house.
          //support for 'public areas' still needs to be added

          //rooms should be pulled from user objects

          $rooms = json_decode($rooms);

          foreach ($datastreams as $data) {
            if ($landlord) {
              array_push($data_ids, $data->id);
              if ($data->id == $streamId) {
                $plot_data = $data;
              }
            } else {
              //rooms will eventually be an array, so this code is out of date
              foreach ($rooms as $room) {
                if ($data->id == $room || $data->id == "PTOTAL") {
                  array_push($data_ids, $data->id);
                  if ($data->id == $streamId) {
                    $plot_data = $data;
                  }
                }
              }
            }
          }

          //used to satisfy the quirks of Xively API -- number of seconds maps to interval between points
          
          $intervals = array(21600 => 0, 43200 => 30, 86400 => 60, 432000 => 300, 1209600 => 900,
                             2678400 => 1800, 7776000 => 10800, 15552000 => 21600, 31536000 => 43200);

          $data = $plot_data;
          //set by user -- defaults to 6 hours
          $duration = '6hours';

          //convert time from human readable format to seconds
          $seconds = strtotime($duration) - time();

          $delta = '';

          // calculate the ideal "resolution" (aka interval)
          foreach ($intervals as $time => $delta_time) {
            if ($seconds/100.0 <= $delta_time) {
              if ($seconds <= $time) {
                $delta = '&interval=' . $delta_time;
                break;
              }
            }
          }

          //not entirely functional -- will not always display the full timespan of data
          if ($seconds < 21600) {
            $delta = '&interval=0';
          }

          //send request to server
          $datapoints = array();
          $times = array();
          $raw_url = 'http://api.xively.com/v2/feeds/120903/datastreams/';
          $url =  $raw_url . $data->id . '.json?start=' . $past . '&duration=' . $seconds . 'seconds&key=';
          $key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';

          //combine strings and make request
          $request = $url . $key . $delta;
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $request);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          $obj_resp = json_decode(curl_exec($curl));

          //push to arrays containing datapoints and time values
          foreach ($obj_resp->datapoints as $point) {
              array_push($datapoints, $point->value);
              $date = strtotime($point->at);
              array_push($times, $date*1000);
          }

          $data_length = count($datapoints);

          //save in json format for use client side
          echo '<script> window.data["' . $data->id . '"] = ' . json_encode($datapoints) . '</script>';
          echo '<script> window.times["' . $data->id . '"] = ' . json_encode($times) . '</script>';
          echo "<script> window.data_ids = " . json_encode($data_ids) . "</script>";
          echo "<script> window.data_length = " . json_encode($data_length) . "</script>";
      ?>

    <style>

    /*nvd3 css modifications*/
      #inner_chart svg {
/*          height: 500px;
          width: 1200px;*/
          font-size: 30px;
          display: block;
          margin-bottom: 50px;
      }

      .nv-axislabel {
          font-size: 20px;
      }

      #loading {
        width: 30px;
        margin-top: 5px;
        display: none;
      }

      #settings {
        margin-top: 5px;
        width: 30px;
        display: block;
        cursor: pointer;
        cursor: hand;
      }

    </style>

  </head>

  <!-- minimal html -->
  <body>
    <? include('header.php') ?>
    TV48
    <? include('header2.php') ?>


    <div id='chart'>
        <div id="inner_chart", style="text-align: center">
        </div>
    </div>

    <!-- simple gui for user interaction -- change time plotted or dataset -->
    <!-- eventually should also allow for datasets that do not contain the current value -->

    <div id="edit">
      <select id='feed'>
      </select>
      <input id='duration' type='number' class='small-width'>
      <select id='units'>
        <option value="seconds">seconds</option>
        <option value="minutes">minutes</option>
        <option value="hours">hours</option>
        <option value="days">days</option>
        <option value="weeks">weeks</option>
        <option value="months">months</option>
        <!-- Option years currently causes problems -- too large of date range -->
        <!-- <option value="years">years</option> -->
      </select>
      <div class='text-center'>
        <img id='loading' class='centered' src="images/load.gif">
      </div>

      <div class='text-center'>
        <img id='settings' href="#myModal" role='button' data-toggle='modal' class='centered' src='images/settings.png'>
      </div>

      <!-- Modal -->

      <!-- Switch around this interface - all that is needed is a mapping betweeen users and rooms, and a way to assign rooms to be public -->
      <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Graph Settings</h3>
        </div>
        <div class="modal-body">
          <h4>Units of Measurement</h4>
          <select id='graph-units'>
            <option value='Watts'>Watts</option>
            <option value='Euro cents per Hour'>Euros cents per Hour</option>
            <option value='Euros per Month'>Euros per Month</option>
            <option value='Euros per Year'>Euros per Year</option>
            <option value='Grams of CO2 per Hour'>Grams of CO2 per Hour</option>
            <option value='Kg of CO2 per Day'>Kg of CO2 per Day</option>
            <option value='Kg of CO2 per Month'>Kg of CO2 per Month</option>
            <option value='Kg of CO2 per Year'>Kg of CO2 per Year</option>
          </select>
        </div>
        <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button class="btn btn-success unit-change">Save changes</button>
        </div>
      </div>

    </div>
    </div>

    <script>
      nv.dev = false;
      var convert_time = {'seconds': 1, 'minutes': 60, 'hours': 3600, 'days': 86400,
          'weeks': 604800, 'months': 86400*30, 'years': 365*86400}

      var convert_cost = {'Euro cents per Hour': .0085, 'Euros per Month': 8765 / 1200 * .0085, 'Euros per Year': 8765 / 100 * .0085,
                          'Grams of CO2 per Hour': .76,  'Kg of CO2 per Day': .76*24/1000, 'Kg of CO2 per Month': .76*24*30/1000, 
                          'Kg of CO2 per Year': .76*24*365/1000, 'Watts': 1}
      function update_values(svg_id, ratio) {
        for (var i = 0; i < window.data[svg_id].length; i++) {
          window.data[svg_id][i]['y'] *= ratio; 
        }
      }

      function next_chart(svg_id) {
        update_values(svg_id, convert_cost[window.units]);

        //nvd3 magic -- see nvd3 docs for details
        svg_id = svg_id.toString();
        $('#inner_chart').html('');
        $('#inner_chart').append('<svg id="id'+svg_id+'"></svg>');
        nv.addGraph(function() {
          var chart = nv.models.lineWithFocusChart().margin({left: 80, bottom: 50})
                        .tooltipContent(function(key, y, e, graph) { return '<h3>' + e + ' ' + window.units + '</h3>' })

          //chart formatting
          chart.xAxis
              .axisLabel('Time')
              .tickFormat(function(d) {
                return d3.time.format("%H:%M")(new Date(d));
               });

          chart.x2Axis
              .axisLabel('')
              .tickFormat(function(d) {
                return d3.time.format("%b %d")(new Date(d));
               });

          chart.yAxis
              .axisLabel(window.units)
              .tickFormat(d3.format('.00f'));

          chart.y2Axis
              .tickFormat(d3.format('.00f'));

          //pass data to chart, specify transition length, initialize chart
          var chart_id = '#chart svg#' + 'id' + svg_id;
          d3.select(chart_id)
              .datum([{key: svg_id, values: window.data[svg_id].slice(0, window.data_length-1)}])
            .transition().duration(500)
              .call(chart);

         //update chart on window resize
          nv.utils.windowResize(chart.update);
          return chart;
        });
      }

      //simple wrapper for plotting
      function populate_graph(svg_id) {

        //for live updating -- only happens when duration of window is < 10 minutes
        window.new_time = window.times[svg_id][0];
        window.new_point = window.data[svg_id][0];

        //do not update chart if new information is not present
        if (window.last_time === undefined) {
          next_chart(svg_id);
        }

        //last point does not exist, definitely update chart
        else if (window.last_point === undefined) {
          next_chart(svg_id);
        }

        //times do not match -- indicating new information
        else if (window.new_time != window.last_time) {
          next_chart(svg_id);
        }

        //datapoints do not match -- indicating new information
        else if (window.new_point != window.lastPoint) {
          next_chart(svg_id);
        }

        //update times to reflect the current state
        window.last_time = window.new_time;
        window.last_point = window.new_point;
      }

      function render_page(svg_id) {

        //check DOM elements for relevant values
        var units = $('#units').val();
        var duration = $('#duration').val();
        var seconds = convert_time[units] * duration;

        //called on page load -- sets params based on clients computer
        //and calls helper functions to render data
        var height = $(window).height();
        var width = $(window).width();

        //avoid flickering by changing properties and then making chart visible
        $('#inner_chart').css('height', height*.6);
        $('#inner_chart').css('width', width - 65);
        $('#inner_chart').css('display', 'block');

        //remove feed html to prepare for addition of new feed value
        $('#feed').html('');
        //Populate feed select with datastream values
        //May eventually want to compose string and then add it all at once to
        //speed up the process a bit.
        for (var i = 0; i < data_ids.length; i++) {
          if (!$('option[value=' + window.data_ids[i] + ']').length) {
            $('#feed').append('<option value="' + window.data_ids[i] + '">' + window.data_ids[i] + '</option>');
          }
        }

        //update feed value and populate graph with the appropriate information
        $('#feed').val(svg_id);
        populate_graph(svg_id);

        //When the feed, duration, or units change, update the graph
      }

      //called when the desired time period or feed changes
      function update_graph() {

        //make loading icon visible -- it's been there the whole time
        $('#loading').css('display', 'block');
        $('#settings').css('display', 'none');

        //as indicated, called when graph state must be updated
        //variables from sliders and inputs are pulled
        var feed = $('#feed').val();
        var units = $('#units').val();
        var duration = $('#duration').val();
        var duration_in_seconds = convert_time[units] * parseFloat(duration);

        //a bit of processing to ensure that the date range is not too large
        if (duration_in_seconds > timeDiff) {
          var delta = Math.ceil(window.timeDiff / convert_time[units]);
          $('#duration').val(delta);
          console.log("Only " + duration + " units of data are available.")
        }

        //Live mode -- updates every 5 seconds and refreshes the graph
        //Refresh isn't noticeable unless the graph has actually been updated
        if (duration_in_seconds < 600) {
          setTimeout(function() {update_graph();}, 5000);
        }

        //more variables are grabbed from DOM elements
        var streamId = $('#feed').val().toString();
        var duration = $('#duration').val().toString() + $('#units').val().toString();
        console.log({'streamId': streamId, 'duration': duration});
        //send jquery post request
        $.post('getPower.php', {'streamId': streamId, 'duration': duration}, function(data) {
          console.log(data);
          var data = JSON.parse(data);
          //data attached to window object to act as global vars
          window.data_ids = data.data_ids;
          window.times = data.times;
          window.data = data.data;
          window.data_length = data.data_length;

          //data is reformatted -- will eventually remove and streamline this step
          prepare_data();

          //graph is populated and rerendered
          populate_graph(streamId);
          $('#loading').css('display', 'none');
          $('#settings').css('display', 'block');
        });
      }

      //format conversion -- perhaps could be done server side
      function prepare_data() {
        for(var stream in window.data) {
          for (var i = 0; i < 100; i++) {
              window.data[stream][i] = {x: window.times[stream][i], y: parseInt(window.data[stream][i], 10)};
          }
        }
      }

      //bind function handlers to events that should change the set of points plotted
      $('#feed').change(function() {
        update_graph();
      });

      $('#duration').change(function() {
        update_graph();
      });

      $('#units').change(function() {
        update_graph();
      });

      $('.unit-change').click(function() {
        var svg_id = $('#feed').val().toString();
        update_values(svg_id, 1.0/convert_cost[window.units]);
        window.units = $('#graph-units').val();
        console.log($('#graph-units').val());
        var feed = $('#feed').val().toString();
        render_page(feed);
        $('#myModal').modal('hide');
      });

      $(document).ready(function() {

        //calculate difference between the time when the feed was created and the current time
        var date_created = new Date(created);
        var current_date = new Date();

        //make global var to save this property
        window.units = 'Watts';
        window.timeDiff = (current_date.getTime() - date_created.getTime())/1000;

        $('#units').val("hours");
        $('#duration').val("6");
        prepare_data();
        render_page('PTOTAL');
      });

      //dynamic plot resizing based on screen size
      $(window).resize(function() {
        var svg_id = $('#feed').val().toString();
        render_page(svg_id);
      })

    </script>
  </body>
