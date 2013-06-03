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
	    <link rel="stylesheet" href="stylesheets/bootstrap-combined.min.css">
	    <link rel="stylesheet" href="stylesheets/jquery-ui.css">
	    <link rel="stylesheet" href="style.css">
	    <!--[if lt IE 9]>
	        <style>

	            .hide-this {
	                display: none;
	            }

	        </style>
	    <![endif]-->
		<style>

			html {
				background-color: #eeeeee;
			}

			body {
				background-color: #eeeeee;
			    font-family: 'Lato', sans-serif;
			    padding: 25px;
			}

			.fade_line{
			    display:block;
			    border:none;
			    color:white;
			    height:2px;
			    background:black;
			    background: -webkit-gradient(radial, 50% 50%, 0, 50% 50%, 1000, from(#333), to(#fff));
			    margin-left: 5%;
			    margin-right: 5%;
			}

			.center-text {
			    text-align: center;
			}

			.large-text {
			    font-size: 50px;
			}

			.dark-text {
			    color: #333333;
			}

			.centered {
			    border: 2px black auto;
			    display: block;
			    margin-right: auto;
			    margin-left: auto;
			}

			.px100 {
			    height: 100px;
			}

			.full-width {
				width: 100%;
			}

			#main_title {
				text-align: center;
				font-size: 30px;
				margin-bottom: 50px;
				margin-top: 25px;
			}

			#template {
				background-color: #ffffff;
				text-align: center;
				font-size: 15px;
				margin-bottom: 50px;
				width: 800px;
				height: 325px;
				border: 20px #333333 solid;
				margin-right: auto;
				margin-left: auto;
				padding: 20px;
				border-radius: 25px;
			}

			#edit {
				text-align: center;
				font-size: 30px;
				min-width: 890px;
			}

			#feed {
				display: block;
				margin-right: auto;
				margin-left: auto;
			}

			#duration {
				margin-left: auto;
				margin-right: auto;
				display: inline-block;
			}

			#units {
				margin-left: auto;
				margin-right: auto;
				display: inline-block;
				width: 160px;
			}

			.small-width {
				width: 40px;
			}

			#wrapper {
				position: relative;
				width: 100%;
				height: 100%;
			}

			.min-width {
				min-width: 890px;
			}

			.larger-font {
				float: left;
				padding-left: 30px;
				font-size: 20px;
				white-space:nowrap;
				overflow:hidden;
				display: inline-block;
				z-index: 200px;
			}

			.key {
				float: right;
				position: relative;
				width: 20px;
				height: 20px;
				background-color: #46a564;
				left: -420px;
				border-radius: 3px;
				margin-top: 230px;
				display: inline-block;
				z-index: 200px;
			}

			.image {
				margin-left: -20px;
			}

			.large-text {
			    font-size: 50px;
			    cursor: pointer;
			    cursor: hand;
			}

		</style>

		 <?

            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            $url = 'http://api.xively.com/v2/feeds/120903.json';
            // $key = 'N8ATwDUEURXCVHytooImg1TuwhvJRC5Tg38kovOqnAWEyC1e';
            $key = 'N8ATwDUEURXCVHytooImg1TuwhvJRC5Tg38kovOqnAWEyC1e';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-ApiKey: ' . $key));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $resp = curl_exec($curl);

            $obj_resp = json_decode($resp);

            curl_close($curl);

            $datastreams = $obj_resp->datastreams;

            $data_ids = array();

            foreach ($datastreams as $data) {
                array_push($data_ids, $data->id);
            }

            echo "<script> var data_ids = " . json_encode($data_ids) . "</script>";
            echo "<script> var created = " . json_encode($obj_resp->created) . "</script>";

        ?>

	</head>
	<body>
		<!--[if lt IE 9]>
		    <div style='text-align: center; border: 5px solid #333333;'>We're sorry.  We do not currently support versions of Internet Explorer earlier than 9.0.  Please either upgrade to a<a href='http://windows.microsoft.com/en-us/internet-explorer/ie-10-worldwide-languages'> more recent version of Internet Explorer </a> or view this site in a <a href='www.google.com/chrome'>different browser</a>.  Thanks!</div>
		<![endif]-->
		<a href='home.php' id='home'><img src='images/home.png' class='home-button'></a>
		<div class="full-width px100 center-text dark-text min-width">
			<h1 class="large-text" id='home'>TV48<h1>
			<hr class="fade_line">
		</div>

		<div id='chart'></div>

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
			  <option value="years">years</option>
			</select>
		</div>

		<script>

			$(document).ready(function() {

				var key = 'N8ATwDUEURXCVHytooImg1TuwhvJRC5Tg38kovOqnAWEyC1e';
				var height = $(window).height();
				var width = $(window).width();

				var timer_id = 0;
				//Populate feed select with datastream values
				for (var i = 0; i < data_ids.length; i++) {
					$('#feed').append('<option value="' + data_ids[i] + '">' + data_ids[i] + '</option>');
				}

				//Get time created from PHP script to prevent auth error when large value is entered
				var date_created = new Date(created);
				var current_date = new Date();
				var timeDiff = (current_date.getTime() - date_created.getTime())/1000;


				//Function for populating graph string
				function template(string,data){
		    		return string.replace(/%(\w*)%/g,function(m,key){
		    			return data.hasOwnProperty(key)?data[key]:"";});
				}


				var feed = data_ids[0];
				var duration = '60 minutes';

				$('#feed').val(feed);
				$('#duration').val(60);
				$('#units').val('minutes');

				$(document).keypress(function(e){
				    if (e.which == 13){
				        $("#feed-submit").click();
				    }
				});

				update_graph();

				function update_graph() {
					var key = 'N8ATwDUEURXCVHytooImg1TuwhvJRC5Tg38kovOqnAWEyC1e';
					console.log("Updated...");
					var convert_time = {'seconds': 1, 'minutes': 60, 'hours': 3600, 'days': 86400,
										'weeks': 604800, 'months': 86400*30, 'years': 365*86400}
					var graph = "<div id='template' style='display: none;'><h2 id='graph_title'>%title%: Laatste %duration_string%</h2>" +
					"<img class='image' src=\"http://api.xively.com/v2/feeds/120903/datastreams/%feed%.png?width=730&height=250&colour=%2346a564" +
					"&duration=%duration%&b=true&g=true&scale=auto&timezone=Brussels&s=7&key=" + key + "\">" +
					"<div class='key'><div><div class='larger-font'>Watts</div></div>";

					//Checking to see if sufficient data is available
					var feed = $('#feed').val();
					var units = $('#units').val();
					var duration = $('#duration').val();
					var duration = duration + ' ' + units;
					var duration_in_seconds = convert_time[units] * parseFloat(duration);

					if (duration_in_seconds > timeDiff) {
						var delta = Math.ceil(timeDiff / convert_time[units]);
						duration = delta + ' ' + units;
						console.log("Duration in seconds: ", duration_in_seconds);
						console.log("Time since created:  ", timeDiff);
						console.log(duration);
						$('#duration').val(delta);
						console.log("Only " + duration + " of data are available.")


					}

					//Live mode -- updates every 5 seconds and refreshes the graph
					//Refresh isn't noticeable unless the graph has actually been updated
					if (duration_in_seconds < 600) {
						setTimeout(function() {update_graph();}, 5000);
					}

					//Rerendering chart
					$('#chart').html('');

					//Poor man's templating
					graph = template(graph, {title: feed, feed: feed, duration: duration.replace(/\s/g, ''), duration_string: duration});
					$('#chart').append(graph);
					$('#template').css('display', 'block');

				}

				//When the feed, duration, or units change, update the graph
				$('#feed').change(function() {
					update_graph();
				});

				$('#duration').change(function() {
					update_graph();
				});

				$('#units').change(function() {
					update_graph();
				});
			});

		</script>
	</body>
