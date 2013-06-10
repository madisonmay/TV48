 <?
  session_start();

  // Turn off error reporting before releasing as production code
  // Should be moved to an external config file with the below params
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  //config file won't be included for some reason
  //pass is included by hand
<<<<<<< HEAD
  include('ESF_config.php');
=======
  $username = 'thinkcore';
  $password = 'K5FBNbt34BAYCZ4W';
  $database = 'thinkcore_drupal';
  $server = 'localhost';
>>>>>>> 265d4b089c5d702f0e43ca0ca99bc50646166d6a

  //grab variables from Jquery post
  $duration = $_POST['duration'];
  $temp = strtotime($duration) - time();

  //The 7200 is there to correct for the time zone -- needs to be updated
  //to be more general
  $past = date("Y-m-d\TH:i:sP", time() - $temp - 7200);
  $now = date("Y-m-d\TH:i:sP", time());

  //grab streamIds from xively
  $url = 'http://api.xively.com/v2/feeds/120903.json?key=';
  $key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';
  $request = $url . $key;

  //Make Xively api call
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $request);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $resp = curl_exec($curl);
  $obj_resp = json_decode($resp);
  curl_close($curl);

  //Extract the relevant information
  $datastreams = $obj_resp->datastreams;

  $data_ids = array();

  //Requested stream
  $streamId = $_POST['streamId'];

  //Select data that corresponds with the given streamId
  foreach ($datastreams as $data) {
    array_push($data_ids, $data->id);
    if ($data->id == $streamId) {

      //matching datastream found
      $plot_data = $data;
      break;
    }
  }

  //used to satisfy the quirks of Xively API -- a number of seconds that gives the duration of a selected
  //time period maps to a minimum number of seconds between data points, as specified by Xively
  $intervals = array(21600 => 0, 43200 => 30, 86400 => 60, 432000 => 300, 1209600 => 900, 2678400 => 1800,
                     2678400 => 3600, 7776000 => 10800, 15552000 => 21600, 31536000 => 43200, 31536000 => 86400);


  $data = $plot_data;


  //convert time from human readable format to seconds
  $seconds = strtotime($duration) - time();

  $delta = '';

  //calculate ideal resolution value
  foreach ($intervals as $time => $delta_time) {
    if ($seconds/100.0 <= $delta_time) {
      if ($seconds <= $time) {
        $delta = '&interval=' . $delta_time;
        break;
      }
    }
  }

  //edge case -- still needs some work to make sure the entire interval is displayed
  if ($seconds < 10800) {
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

  //useful client side
  $data_length = count($datapoints);

  //construct PHP object, encode as JSON, and send to client
  $_data = array($data->id => $datapoints);
  $_times = array($data->id => $times);
  $reply = json_encode(array("data" => $_data, "times" => $_times, "data_ids" => $data_ids, "data_length" => $data_length));
  echo $reply;
?>