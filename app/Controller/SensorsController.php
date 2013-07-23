<?
	class SensorsController extends AppController {

		public function isAuthorized($user) {
			if (in_array('admin', $this->Session->read('User.roles'))) {
				return true;
			} elseif (in_array($this->action, array('lighting', 'heating', 'electricity', 'refresh', 'piechart', 'electricity_summary'))) {
				return true;
			}

			return false;
		}

		public function lighting() {

			//compose js object
			$lights = $this->Sensor->find('all', array("conditions" => array('Sensor.type' => 'lighting')));
			$js_lights = array();
			foreach ($lights as $light) {
				if ($this->contractExists($light['Sensor']['room_id'], $this->Auth->user('id'))) {
					$js_light = array(
						'streamId' => $light['Sensor']['id'],
						'pwm' => $light['Sensor']['value'],
						'location' => $light['Sensor']['name'],
						'request' => $light['Sensor']['request']);
					array_push($js_lights, $js_light);
				}
			}

			echo("<script> window.lights = " . json_encode($js_lights) . "</script>");

			$this->set('title_for_layout', 'Lighting');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('lighting')); 
		}

		public function edit_lights() {
			if ($this->request->is('post')) {
				$js_lights = json_encode($this->request->data['values']);
				$php_lights = json_decode($js_lights);

				foreach ($php_lights as $light) {
					$brightness = 500*$light->pwm;

					if ($brightness <= 50000) {
						$this->Sensor->id = $light->streamId;
						$this->Sensor->saveField('value', $brightness);
						$this->Sensor->saveField('request', 1);
					}
				}
				echo 1;
			}
			exit(0);
		}

		public function heating() {
			//Potentially a case of mixing presentation and logic? 
			$this->set('title_for_layout', 'Heating');
			$this->set('cssIncludes', array('plot'));
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3')); 
		}

		public function heatmap() {
			$this->set('title_for_layout', 'Energy Use Heatmap');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js')); 
		}

		public function electricity_summary() {
			//Potentially a case of mixing presentation and logic?
			$this->set('title_for_layout', 'Electricity Use');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3'));
			$this->loadModel('Data');
			$opts = array('conditions' => array('Sensor.type' => 'electricity'));
			$sensors = $this->Sensor->find('all', $opts);
			$sensors_values = array();
			$count = 0;
			echo '<script> window.feeds = [];</script>';
			foreach ($sensors as $sensor) {
				if ($this->contractExists($sensor['Sensor']['room_id'], $this->Auth->user('id'))) {
					$count++;
					$sensor_values = array();
					$data = $sensor['Data'];
					foreach ($data as $datum) {
						array_push($sensor_values, $datum['value']);
					}
					$final = array('name' => $sensor['Sensor']['name'], 'values' => $sensor_values);
					echo '<script> window.feeds.push(' . json_encode($final) . ');</script>';
				}
			}
		}

		public function piechart() {
			$this->set('title_for_layout', 'Energy Use Piechart');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3'));

			$this->loadModel('Data');
			$opts = array('conditions' => array('Sensor.type' => 'electricity'));
			$sensors = $this->Sensor->find('all', $opts);
			$sensors_values = array();
			$count = 0;

			//should create php object and then push all at once
			echo '<script> window.feeds = [];</script>';
			foreach ($sensors as $sensor) {
				if ($this->contractExists($sensor['Sensor']['room_id'], $this->Auth->user('id'))) {
					$count++;
					$sensor_values = array();
					$data = $sensor['Data'];
					foreach ($data as $datum) {
						array_push($sensor_values, $datum['value']);
					}
					$final = array('name' => $sensor['Sensor']['name'], 'values' => $sensor_values);
					echo '<script> window.feeds.push(' . json_encode($final) . ');</script>';
				}
			}
		}

		public function electricity() {
			//Potentially a case of mixing presentation and logic? 
			$this->set('title_for_layout', 'Electricity');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3')); 

			//not currently used -- will eventually be integrated with permissions system
			$opts = array('conditions' => array('Sensor.type' => 'electricity'));
			$sensors = $this->Sensor->find('all', $opts);
			$sensor_ids = array();
			foreach ($sensors as $sensor) {
				$room_id = $sensor['Sensor']['room_id'];
				if ($room_id == 0 || $this->contractExists($room_id, $this->Auth->user('id'))) {
					array_push($sensor_ids, $sensor['Sensor']['xively_id']);
				}
			}

			// need better default behavior
			if (count($sensor_ids) < 1) {
				array_push($sensor_ids, 'PTOTAL');
			}

		    define('SECONDS_PER_DAY', 86400);

		    // offset of 21600 to account for time zone -- there has to be a more elegant way of doing this.
		    // xively requires some special formatting, handled in the format string below
		    // 1/4 of a day = 6 hours
		    $past = date("Y-m-d\TH:i:sP", time() - .25 * SECONDS_PER_DAY - 21600);
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
		    $data_names = array();

		    $streamId = 'PTOTAL';

		    $plot_data = '';

		    foreach ($datastreams as $data) {
		    	if (in_array($data->id, $sensor_ids)) {
			        $sensor = $this->Sensor->find('first', array('conditions' => array('Sensor.xively_id' => $data->id, 'delta' => 1)));
			        if ($sensor) {
			        	array_push($data_ids, $data->id);
				        $name = $sensor['Sensor']['name'];
				        $data_names[$data->id] = $name;
				        if ($data->id == $streamId) {
				        	$plot_data = $data;
				        	break;
				        }
			        }
		    	}
		    }
		    //used to satisfy the quirks of Xively API -- number of seconds maps to interval between points
		    
		    $intervals = array(21600 => 0, 43200 => 30, 86400 => 60, 432000 => 300, 1209600 => 900,
		                       2678400 => 1800, 7776000 => 10800, 15552000 => 21600, 31536000 => 43200);

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
		    $url =  $raw_url . $plot_data->id . '.json?start=' . $past . '&duration=' . $seconds . 'seconds&key=';
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
		        array_push($times, $date*1000+21600*1000);
		    }

		    $data_length = count($datapoints);

		    //save in json format for use client side
		    //not a huge fan of using the window object, but for right now it should be fine
		    $id = $plot_data->id;
		    echo '<script> window.data = {}; window.data["' . $id . '"] = ' . json_encode($datapoints) . '</script>';
		    echo '<script> window.times = {}; window.times["' . $id . '"] = ' . json_encode($times) . '</script>';
		    echo "<script> window.data_ids = " . json_encode($data_ids) . "</script>";
		    echo "<script> window.data_length = " . json_encode($data_length) . "</script>";
		    echo "<script> window.data_names = " . json_encode($data_names) . "</script>";

		}

		public function refresh() {

			//grab variables from Jquery post
			$duration = $_POST['duration'];
			$temp = strtotime($duration) - time();

			//The 21600 is there to correct for the time zone -- needs to be updated
			//to be more general
			$past = date("Y-m-d\TH:i:sP", time() - $temp - 21600);
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

			//Requested stream
			$streamId = $_POST['streamId'];

			//Select data that corresponds with the given streamId
			foreach ($datastreams as $data) {
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
			  array_push($times, $date*1000+21600*1000);
			}

			//useful client side
			$data_length = count($datapoints);

			//construct PHP object, encode as JSON, and send to client
			$_data = array($data->id => $datapoints);
			$_times = array($data->id => $times);
			$reply = json_encode(array("data" => $_data, "times" => $_times, "data_length" => $data_length));
			echo $reply;
			exit(0);
		}

		public function index() {
			$this->set('title_for_layout', 'Sensors');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array());
			$lighting = array("conditions" => array("Sensor.type" => "lighting"));
			$heating = array("conditions" => array("Sensor.type" => "heating"));
			$electricity = array("conditions" => array("Sensor.type" => "electricity"));
			$this->set('lighting', $this->Sensor->find('list', $lighting)); 
			$this->set('heating', $this->Sensor->find('list', $heating)); 
			$this->set('electricity', $this->Sensor->find('list', $electricity)); 
		}

		public function add() {
			$this->set('title_for_layout', 'Add a Sensor');
			if ($this->request->is('get')) {
				$rooms = $this->Sensor->Room->find('list');
				$this->set('rooms', $rooms);
			} else {
				//post request
				$this->request->data['Sensor']['room_id'] = $this->request->data['Sensor']['Rooms'];
				$this->Sensor->create();
				if ($this->Sensor->save($this->request->data())) {
					$this->Session->write('flashWarning', 0);
					$this->Session->setFlash(__('Sensor added!'));
					$this->redirect(array('controller' => 'sensors', 'action' => 'add'));
				} else {
					$this->Session->write('flashWarning', 1);
					$this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
					$this->redirect(array('controller' => 'sensors', 'action' => 'add'));
				}
			}
		}

		public function edit() {
			$this->set('title_for_layout', 'Edit a Sensor');
			if ($this->request->is('get')) {
				$rooms = $this->Sensor->Room->find('list');
				$this->set('rooms', $rooms);
				$lighting = $this->request->query['Lighting'];
				$heating = $this->request->query['Heating'];
				$electricity = $this->request->query['Electricity'];
				$found = 0;
				foreach (array($lighting, $heating, $electricity) as $sensor_id) {

					if ($sensor_id) {
						$sensor = $this->Sensor->findById($sensor_id);
						$this->set('room_id', $sensor['Sensor']['room_id']);
						$found++;
						$this->data = $sensor;
					}
				}

				if (!$found) {
					$this->redirect(array('controller' => 'sensors', 'action' => 'index'));
				}
			} else {
				//post request
				$this->request->data['Sensor']['room_id'] = $this->request->data['Sensor']['Rooms'];
				$this->Sensor->id = $this->request->data['Sensor']['id'];
				if ($this->Sensor->save($this->request->data())) {
					$this->Session->write('flashWarning', 0);
					$this->Session->setFlash(__('Sensor saved!'));
					$this->redirect(array('controller' => 'sensors', 'action' => 'index'));
				} else {
					$this->Session->write('flashWarning', 1);
					$this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
					$this->redirect(array('controller' => 'sensors', 'action' => 'index'));
				}
			}
		}
	}
?>