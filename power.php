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
<<<<<<< HEAD
      <? include("base.php"); ?>
      <link rel="stylesheet" type='text/css' href="stylesheets/plot.css">
=======
      <script src="scripts/jquery.min.js"></script>
      <script src="scripts/bootstrap.min.js"></script>
      <script src="scripts/jquery-ui.min.js"></script>
      <link rel="stylesheet" type='text/css' href="stylesheets/bootstrap-combined.min.css">
      <link rel="stylesheet" type='text/css' href="stylesheets/jquery-ui.css">
      <link rel='stylesheet' type='text/css' href='stylesheets/nv.d3.css'>
      <link rel="stylesheet" type='text/css' href="style.css">
      <link rel="stylesheet" type='text/css' href="plot.css">
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
      <script src="http://d3js.org/d3.v2.js"></script>
      <script src="nv.d3.js"></script>
      <script>
          window.data = {};
          window.times = {};
      </script>

      <?
<<<<<<< HEAD

          session_start();
          error_reporting(E_ALL);
          ini_set('display_errors', 1);

=======
          session_start();

          error_reporting(E_ALL);
          ini_set('display_errors', 1);

          //config file won't be included for some reason
          //pass is included by hand
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
          $username = 'thinkcore';
          $password = 'K5FBNbt34BAYCZ4W';
          $database = 'thinkcore_drupal';
          $server = 'localhost';

<<<<<<< HEAD
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

=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
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

<<<<<<< HEAD
          //compile datapoints and strip unnecessary properties
          //new code recently added to check for global permissions ($landloard)
          //and if permissions are not found, give access to only the room
          //associated with the current user and the total for the house.
          //support for 'public areas' still needs to be added

          foreach ($datastreams as $data) {
            if ($landlord) {
              array_push($data_ids, $data->id);
              if ($data->id == $streamId) {
                $plot_data = $data;
              }
            } else {
              //rooms will eventually be an array, so this code is out of date
              if ($data->id == $rooms || $data->id == "PTOTAL") {
                array_push($data_ids, $data->id);
                if ($data->id == $streamId) {
                  $plot_data = $data;
                }
              }
            }
          }

          //used to satisfy the quirks of Xively API -- number of seconds maps to interval between points
=======
          //compile datapoints and strip unnecessary ingo
          foreach ($datastreams as $data) {
            array_push($data_ids, $data->id);
            if ($data->id == $streamId) {
              $plot_data = $data;
            }
          }

          //used to satisfy the quirks of Xively API
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
          $intervals = array(21600 => 0, 43200 => 30, 86400 => 60, 432000 => 300, 1209600 => 900,
                             2678400 => 1800, 7776000 => 10800, 15552000 => 21600, 31536000 => 43200);

          $data = $plot_data;

<<<<<<< HEAD
          //set by user -- defaults to 6 hours
=======
          //set by user
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
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

<<<<<<< HEAD
          //not entirely functional -- will not always display the full timespan of data
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
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
<<<<<<< HEAD
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
=======
        width: 25px;
        display: block;
        display: none;
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
      }

    </style>

  </head>

  <!-- minimal html -->
  <body>
<<<<<<< HEAD
    <? include('header.php') ?>
    TV48
    <? include('header2.php') ?>
=======
    <a href='home.php' id='home'><img src='images/home.png' class='home-button'></a>
    <div class="full-width px100 center-text dark-text min-width">
      <h1 class="large-text" id='home'>TV48<h1>
      <hr class="fade_line">
    </div>
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a

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
<<<<<<< HEAD
      <div class='text-center'>
        <img id='settings' href="#myModal" role='button' data-toggle='modal' class='centered' src='images/settings.png'>
      </div>

      <!-- Modal -->
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
          </select>
        </div>
        <div class="modal-footer">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button class="btn btn-success unit-change">Save changes</button>
        </div>
      </div>

    </div>
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
    </div>

    <script>
      nv.dev = false;
      var convert_time = {'seconds': 1, 'minutes': 60, 'hours': 3600, 'days': 86400,
          'weeks': 604800, 'months': 86400*30, 'years': 365*86400}

<<<<<<< HEAD
      var convert_cost = {'Euro cents per Hour': .0085, 'Euros per Month': 8765 / 1200 * .0085, 'Euros per Year': 8765 / 100 * .0085,
                          'Grams of CO2 per Hour': .76, 'Watts': 1}
      function update_values(svg_id, ratio) {
        for (var i = 0; i < window.data[svg_id].length; i++) {
          window.data[svg_id][i]['y'] *= ratio; 
        }
      }

      function next_chart(svg_id) {
        update_values(svg_id, convert_cost[window.units]);
=======
      function next_chart(svg_id) {
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a

        //nvd3 magic -- see nvd3 docs for details
        svg_id = svg_id.toString();
        $('#inner_chart').html('');
        $('#inner_chart').append('<svg id="id'+svg_id+'"></svg>');
        nv.addGraph(function() {
          var chart = nv.models.lineWithFocusChart().margin({left: 80, bottom: 50})
<<<<<<< HEAD
                        .tooltipContent(function(key, y, e, graph) { return '<h3>' + e + ' ' + window.units + '</h3>' })
=======
                        .tooltipContent(function(key, y, e, graph) { return '<h3>' + e + ' watts</h3>' })
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a

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
<<<<<<< HEAD
              .axisLabel(window.units)
=======
              .axisLabel('Watts')
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
              .tickFormat(d3.format('.00f'));

          chart.y2Axis
              .tickFormat(d3.format('.00f'));

<<<<<<< HEAD
          //pass data to chart, specify transition length, initialize chart
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
          var chart_id = '#chart svg#' + 'id' + svg_id;
          d3.select(chart_id)
              .datum([{key: svg_id, values: window.data[svg_id].slice(0, window.data_length-1)}])
            .transition().duration(500)
              .call(chart);

<<<<<<< HEAD
          //update chart on window resize
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
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

<<<<<<< HEAD
        //last point does not exist, definitely update chart
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        else if (window.last_point === undefined) {
          next_chart(svg_id);
        }

<<<<<<< HEAD
        //times do not match -- indicating new information
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        else if (window.new_time != window.last_time) {
          next_chart(svg_id);
        }

<<<<<<< HEAD
        //datapoints do not match -- indicating new information
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        else if (window.new_point != window.lastPoint) {
          next_chart(svg_id);
        }

<<<<<<< HEAD
        //update times to reflect the current state
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        window.last_time = window.new_time;
        window.last_point = window.new_point;
      }

      function render_page(svg_id) {

<<<<<<< HEAD
        //check DOM elements for relevant values
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        var units = $('#units').val();
        var duration = $('#duration').val();
        var seconds = convert_time[units] * duration;

        //called on page load -- sets params based on clients computer
        //and calls helper functions to render data
        var height = $(window).height();
        var width = $(window).width();

<<<<<<< HEAD
        //avoid flickering by changing properties and then making chart visible
=======
        //avoid flickerign
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        $('#inner_chart').css('height', height*.6);
        $('#inner_chart').css('width', width - 65);
        $('#inner_chart').css('display', 'block');

<<<<<<< HEAD
        //remove feed html to prepare for addition of new feed value
        $('#feed').html('');
        //Populate feed select with datastream values
        //May eventually want to compose string and then add it all at once to
        //speed up the process a bit.
=======
        $('#feed').html('');
        //Populate feed select with datastream values
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        for (var i = 0; i < data_ids.length; i++) {
          $('#feed').append('<option value="' + window.data_ids[i] + '">' + window.data_ids[i] + '</option>');
        }

<<<<<<< HEAD
        //update feed value and populate graph with the appropriate information
        $('#feed').val(svg_id);
=======
        //Function for populating graph string
        function template(string,data){
            return string.replace(/%(\w*)%/g,function(m,key){
              return data.hasOwnProperty(key)?data[key]:"";});
        }

        $('#feed').val(svg_id);

>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        populate_graph(svg_id);

        //When the feed, duration, or units change, update the graph
      }

<<<<<<< HEAD
      //called when the desired time period or feed changes
      function update_graph() {

        //make loading icon visible -- it's been there the whole time
        $('#loading').css('display', 'block');
        $('#settings').css('display', 'none');
=======
      function update_graph() {

        $('#loading').css('display', 'block');
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a

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
<<<<<<< HEAD
          $('#settings').css('display', 'block');
=======
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
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

<<<<<<< HEAD
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
=======
      $(document).ready(function() {

        var date_created = new Date(created);
        var current_date = new Date();
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
        window.timeDiff = (current_date.getTime() - date_created.getTime())/1000;

        $('#units').val("hours");
        $('#duration').val("6");
        prepare_data();
        render_page('PTOTAL');
      });

      //dynamic plot resizing based on screen size
      $(window).resize(function() {
<<<<<<< HEAD
        var svg_id = $('#feed').val().toString();
        render_page(svg_id);
=======
        var feed = $('#feed').val().toString();
        render_page(feed);
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a
      })

    </script>
  </body>
