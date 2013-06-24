<?

    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $username = 'thinkcore';
    $password = 'K5FBNbt34BAYCZ4W';
    $database = 'thinkcore_drupal';
    $server = 'localhost';

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

    //pass date of creation to client side code
    $created = $obj_resp->created;

    $datastreams = $obj_resp->datastreams;
    $compressed_data = array();

    foreach ($datastreams as $stream) {
      $compressed_data[$stream->id] = array();
      array_push($compressed_data[$stream->id], (int)$stream->current_value);      
    }

    echo('<p>' . json_encode($compressed_data) . '</p>');
?>
