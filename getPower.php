<?
  session_start();

  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  include('ESF_config.php');

  define('SECONDS_PER_DAY', 86400);

  //Only two weeks of data is available;
  $past = date("Y-m-d\TH:i:sP", time() - 1.12 * SECONDS_PER_DAY);
  $now = date("Y-m-d\TH:i:sP", time());

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

  $mysqli = new mysqli($server, $username, $password, $database);

  /* check connection */
  if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
  }

  $streamId = $_POST['streamId'];

  foreach ($datastreams as $data) {
    array_push($data_ids, $data->id);
    if ($data->id == $streamId) {
      $plot_data = $data;
    }
  }

  $data = $plot_data;
  //send request to server
  array_push($data_ids, $data->id);
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

  $_data = array($data->id => $datapoints);
  $_times = array($data->id => $times);
  $reply = json_encode(array("data" => $_data, "times" => $_times, "data_ids" => $data_ids));
  echo $reply;
?>