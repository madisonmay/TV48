<?
	class DatasController extends AppController {

		public function beforeFilter() {
		    parent::beforeFilter();
		    $this->Auth->allow('logger');
		}

		public function isAuthorized($user) {
		    return true;
		}

		public function logger() {
			$midnight = strtotime('today midnight');
			if (time() - $midnight < 600) {
				$this->loadModel('Sensor');
				$sensors = $this->Sensor->find('all', array('conditions' => array('Sensor.type' => 'electricity')));
				foreach ($sensors as $sensor) {
					$xively_id = $sensor['Sensor']['xively_id'];
					$raw_url = 'http://api.xively.com/v2/feeds/120903/datastreams/';
					$url =  $raw_url . $xively_id . '.json?key=';
					$key = '-fU3XguRNz7lJxJ-sdR8KcvYqKuSAKxhc2YwREp6TjAzZz0g';

					//combine strings and make request
					$request = $url . $key;
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $request);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$obj_resp = json_decode(curl_exec($curl));
					$current_value = $obj_resp->current_value;
					$data = array();
					$data['Data']['sensor_id'] = $sensor['Sensor']['id'];
					$data['Data']['value'] = $current_value;
					$this->Data->create();
					if ($this->Data->save($data)) {
						//success
						echo "0";
					} else {
						//failure
						echo "1";
					}
				}
			}
			exit(0);
		}
	}
?>