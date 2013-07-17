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
			$this->set('title_for_layout', 'Data Logger');
			$midnight = strtotime('today midnight');
			date_default_timezone_set('Europe/Brussels');
			// if ($midnight - time() < 600) {
				$this->loadModel('Sensor');
				$this->loadModel('Contract');
				$this->loadModel('User');

				//deactivate the necessary contracts that have expired
				$contracts = $this->Contract->find('all', array('conditions' => array('deactivated' => 0)));
				foreach ($contracts as $contract) {
					if (strtotime($contract['Contract']['end_date']) < time()) {
						$this->Contract->id = $contract['Contract']['id'];
						$datetime = date('F j, Y', time());
						$this->Contract->saveField('deactivated', $datetime);
					}
				}

				//the real business
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
						//success - update user balance
						$room = $this->Sensor->findById($sensor['Sensor']['id']);
						$room_id = $room['Room']['id'];

						$contract_opts = array('conditions' => array(
							'Contract.deactivated' => 0,
							'Contract.pay' => 1,
							'Contract.room_id' => $room_id));
						$contracts = $this->Contract->find('all', $contract_opts);
						$numUsers = count($contracts);
						if ($numUsers) {
							$data_opts = array('conditions' => array(
								'Data.sensor_id' => $sensor['Sensor']['id']),
								'order' => array('Data.created'));
							$previous_values = $this->Data->find('all', $data_opts);
							if (count($previous_values) > 1) {
								//at least two data points exist now
								$previous_value = $previous_values[count($previous_values) - 2]['Data']['value'];
								$delta = $current_value - $previous_value;
								echo "Current value: ";
								pr($current_value);
								echo "Previous value: ";
								pr($previous_value);
								echo "Delta: ";
								pr($delta);
								$watts_per_person = $delta/$numUsers;

								//.2 (the cost per kwh) should eventually be a value tied to the landlord (dynamic)
								$cost_per_person = ($watts_per_person/1000)*.2;
								if ($cost_per_person) {
									foreach ($contracts as $contract) {
										echo 'User id: ';
										$user = $this->User->findById($contract['User']['id']);
										pr($user['User']['id']);
										echo 'Room id: ';
										pr($contract['Room']['id']);
										echo 'User balance: ';
										pr($user['User']['balance']);
										echo 'minus';
										pr($cost_per_person);
										echo '<br>------------------------<br>';
										$this->User->id = $user['User']['id'];
										$this->User->saveField('balance', $user['User']['balance'] - $cost_per_person);
									}
								}
							}
						}
					} else {
						//failure
						echo "1";
					}
				}
			// }
			exit(0);
		}
	}
?>