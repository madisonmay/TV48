 <?
  session_start();

  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  //config file won't be included for some reason
  //pass is included by hand
  $username = 'thinkcore';
  $password = 'K5FBNbt34BAYCZ4W';
  $database = 'thinkcore_drupal';
  $server = 'localhost';

  define('SECONDS_PER_DAY', 86400);

  $duration = $_POST['duration'];
  $temp = strtotime($duration) - time();
  $past = date("Y-m-d\TH:i:sP", time() - $temp);
  $now = date("Y-m-d\TH:i:sP", time());

  //grab streamIds from xively
  $url = 'http://api.xively.com/v2/feeds/120903.json?key=';
  $key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';
  $request = $url . $key;

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

  $streamId = $_POST['streamId'];

  foreach ($datastreams as $data) {
    array_push($data_ids, $data->id);
    if ($data->id == $streamId) {
      $plot_data = $data;
    }
  }

  //used to satisfy the quirks of Xively API
  $intervals = array(21600 => 0, 43200 => 30, 86400 => 60, 432000 => 300, 1209600 => 900,
                     2678400 => 1800, 7776000 => 10800, 15552000 => 21600, 31536000 => 43200);

  $data = $plot_data;


  //convert time from human readable format to seconds
  $seconds = strtotime($duration) - time();

  $delta = '';

  foreach ($intervals as $time => $delta_time) {
    if ($seconds/100.0 <= $delta_time) {
      if ($seconds <= $time) {
        $delta = '&interval=' . $delta_time;
        break;
      }
    }
  }

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

  $_data = array($data->id => $datapoints);
  $_times = array($data->id => $times);
  $reply = json_encode(array("data" => $_data, "times" => $_times, "data_ids" => $data_ids));
  echo $reply;
?>