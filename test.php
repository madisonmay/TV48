<!DOCTYPE html>

<!--
Todo:
-Add security measure to prevent unwanted users from accessing feed
-Try to fix weird issues with png export time scale (might need to contact Xively about that)
-->
<html>
  <head>
     <title>TV48 - Power</title>
      <script src="scripts/jquery.min.js"></script>
      <script src="scripts/bootstrap.min.js"></script>
      <script src="scripts/jquery-ui.min.js"></script>
      <link rel="stylesheet" type='text/css' href="stylesheets/bootstrap-combined.min.css">
      <link rel="stylesheet" type='text/css' href="stylesheets/jquery-ui.css">
      <link rel='stylesheet' type='text/css' href='stylesheets/nv.d3.css'>
      <link rel="stylesheet" type='text/css' href="style.css">
      <link rel="stylesheet" type='text/css' href="plot.css">
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

          define('SECONDS_PER_DAY', 86400);

          //Only two weeks of data is available;
          $past = date("Y-m-d\TH:i:sP", time() - 1 * SECONDS_PER_DAY);
          $now = date("Y-m-d\TH:i:sP", time());

          $url = 'http://api.xively.com/v2/feeds/120903.json?key=';
          $key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';
          $request = $url . $key;
          $duration = '6hours';

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $request);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

          $resp = curl_exec($curl);
          $obj_resp = json_decode($resp);
          curl_close($curl);

          $datastreams = $obj_resp->datastreams;

          $data_ids = array();

          // $mysqli = new mysqli($server, $username, $password, $database);

          // /* check connection */
          // if ($mysqli->connect_errno) {
          //   printf("Connect failed: %s\n", $mysqli->connect_error);
          //   exit();
          // }

          $streamId = 'PTOTAL';

          foreach ($datastreams as $data) {
            array_push($data_ids, $data->id);
            if ($data->id == $streamId) {
              $plot_data = $data;
            }
          }

          $data = $plot_data;
          //send request to server
          $datapoints = array();
          $times = array();
          $raw_url = 'http://api.xively.com/v2/feeds/120903/datastreams/';
          $url =  $raw_url . $data->id . '.json?start=' . $past . '&end=' . $now . 'limit=1000&key=';
          $key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';
          $request = $url . $key;
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $request);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          $obj_resp = json_decode(curl_exec($curl));

          foreach ($obj_resp->datapoints as $point) {
              array_push($datapoints, $point->value);
              $date = strtotime($point->at);
              array_push($times, $date*1000);
          }

          echo '<script> window.data["' . $data->id . '"] = ' . json_encode($datapoints) . '</script>';
          echo '<script> window.times["' . $data->id . '"] = ' . json_encode($times) . '</script>';
          echo "<script> window.data_ids = " . json_encode($data_ids) . "</script>";
      ?>

    <style>
      #inner_chart svg {
/*          height: 500px;
          width: 1200px;*/
          font-size: 30px;
          display: block;
          margin-bottom: 50px;
      }

    </style>

  </head>
  <body>
    <a href='home.php' id='home'><img src='images/home.png' class='home-button'></a>
    <div class="full-width px100 center-text dark-text min-width">
      <h1 class="large-text" id='home'>TV48<h1>
      <hr class="fade_line">
    </div>

    <div id='chart'>
        <div id="inner_chart", style="text-align: center">
        </div>
    </div>

    <div id="edit">
      <select id='feed'>
      </select>
    <script>

      function next_chart(svg_id) {
        console.log(window.data[svg_id].length)
        console.log(window.data[svg_id]);
        svg_id = svg_id.toString();
        $('#inner_chart').html('');
        $('#inner_chart').append('<svg id="id'+svg_id+'"></svg>');
        nv.addGraph(function() {
          var chart = nv.models.lineWithFocusChart();

          chart.xAxis
              .axisLabel('')
              .tickFormat(function(d) {
                return d3.time.format("%H:%M")(new Date(d));
               });

          chart.x2Axis
              .axisLabel('Time')
              .tickFormat(function(d) {
                return d3.time.format("%b %d")(new Date(d));
               });

          chart.yAxis
              .axisLabel('Watts')
              .tickFormat(d3.format('.00f'));

          chart.y2Axis
              .tickFormat(d3.format('.00f'));

          var chart_id = '#chart svg#' + 'id' + svg_id;
          d3.select(chart_id)
              .datum([{key: svg_id, values: window.data[svg_id]}])
            .transition().duration(500)
              .call(chart);

          nv.utils.windowResize(chart.update);
          return chart;
        });
      }

      function populate_graph(svg_id) {
        var time = new Date().getTime() / 1000;
        next_chart(svg_id);
      }

      function render_page(svg_id) {
        var key = 'N8ATwDUEURXCVHytooImg1TuwhvJRC5Tg38kovOqnAWEyC1e';
        var height = $(window).height();
        var width = $(window).width();

        $('#inner_chart').css('height', height*.7);
        $('#inner_chart').css('width', width - 65);
        $('#inner_chart').css('display', 'block');

        $('#feed').html('');
        //Populate feed select with datastream values
        for (var i = 0; i < data_ids.length; i++) {
          $('#feed').append('<option value="' + window.data_ids[i] + '">' + window.data_ids[i] + '</option>');
        }

        //Function for populating graph string
        function template(string,data){
            return string.replace(/%(\w*)%/g,function(m,key){
              return data.hasOwnProperty(key)?data[key]:"";});
        }

        $('#feed').val(svg_id);

        $(document).keypress(function(e){
            if (e.which == 13){
                $("#feed-submit").click();
            }
        });

        populate_graph(svg_id);

        //When the feed, duration, or units change, update the graph
        $('#feed').change(function() {
          var streamId = $(this).val().toString();
          //send jquery post request
          $.post('getPower.php', {'streamId': streamId}, function(data, textStatus, jqXHR) {
            var data = JSON.parse(data);
            console.log(data);
            window.data_ids = data.data_ids;
            window.times = data.times;
            window.data = data.data;
            console.log(streamId);
            prepare_data();
            populate_graph(streamId);
          })
        });
      }

      function prepare_data() {
        for(var stream in window.data) {
          for (var i = 0; i < 100; i++) {
              window.data[stream][i] = {x: window.times[stream][i], y: parseInt(window.data[stream][i], 10)};
          }
        }
      }

      $(document).ready(function() {
        prepare_data();
        render_page('PTOTAL');
      });

      $(window).resize(function() {
        render_page($('#feed').val().toString());
      })

    </script>
  </body>
