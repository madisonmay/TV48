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

    <script src='scripts/power.js'></script>
  </body>
