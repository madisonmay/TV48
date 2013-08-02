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
			if ($midnight - time() < 3600) {
				$this->loadModel('Sensor');
				$this->loadModel('Contract');
				$this->loadModel('User');

				//deactivate the necessary contracts that have expired
				$contracts = $this->Contract->find('all', array('conditions' => array('deactivated' => 0)));
				foreach ($contracts as $contract) {

					//if the contract has expired
					if (strtotime($contract['Contract']['end_date']) < time()) {
						$this->Contract->id = $contract['Contract']['id'];
						$datetime = date('F j, Y', time());
						//set the deactivated field to the current datetime
						$this->Contract->saveField('deactivated', $datetime);
					}
				}

				//the real business
				$sensors = $this->Sensor->find('all', array('conditions' => array('Sensor.type' => 'electricity', 'solar' => 0)));
				foreach ($sensors as $sensor) {

					//url components
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

					//compose new data object and save to the db
					$data = array();
					$data['Data']['sensor_id'] = $sensor['Sensor']['id'];
					$data['Data']['value'] = $current_value;
					$this->Data->create();

					if ($this->Data->save($data)) {
						$this->loadModel('BalanceUpdate');
						//successful save - update user balance
						$room_id = $sensor['Room']['id'];

						if (!$sensor['Sensor']['delta']) {
							//find all valid contracts that concern that sensor
							//also make sure sensor is cumulative ^^
							$contract_opts = array('conditions' => array(
								'Contract.deactivated' => 0,
								'Contract.pay' => 1,
								'Contract.room_id' => $room_id));
							$contracts = $this->Contract->find('all', $contract_opts);

							//count them to divide the balance evenly
							$numUsers = count($contracts);

							//assuming this value is greater than 0 (needs to be checked to prevent divide by 0)
							if ($numUsers) {

								//find the change in the cumulative energy consumption
								$data_opts = array('conditions' => array(
									'Data.sensor_id' => $sensor['Sensor']['id']),
									'order' => array('Data.created'));
								$previous_values = $this->Data->find('all', $data_opts);
								if (count($previous_values) > 1) {
									//at least two data points exist now
									$previous_value = $previous_values[count($previous_values) - 2]['Data']['value'];
									$delta = $current_value - $previous_value;

									//divide by the number of users to evenly distribute cost
									$watts_per_person = $delta/$numUsers;

									//convert kwh to a monetary value
									//.2 (the cost per kwh) should eventually be a value tied to the landlord (dynamic)
									$kw_per_person = ($watts_per_person/1000.00);

									//if this value is nonzero
									if ($kw_per_person) {

										//update the balance for each user
										foreach ($contracts as $contract) {
											$user = $this->User->findById($contract['User']['id']);

											//make BalanceUpdate object
											$data = array();
											$data['BalanceUpdate']['delta'] = $kw_per_person*$user['User']['price'];
											if ($kw_per_person*$user['User']['price'] > .001) {
												echo $kw_per_person . " ";
												echo $user['User']['price']  . " ";
												echo $data['BalanceUpdate']['delta'] . "<br>";
												$data['BalanceUpdate']['balance'] = $user['User']['balance'] - $kw_per_person*$user['User']['price'];
												$data['BalanceUpdate']['user_id'] = $user['User']['id'];
												$data['BalanceUpdate']['wh_delta'] = $watts_per_person;
												$data['BalanceUpdate']['wh'] = $watts_per_person + $user['User']['wh'];
												$data['BalanceUpdate']['room_id'] = $contract['Room']['id'];
												$data['BalanceUpdate']['sensor_id'] = $sensor['Sensor']['id'];

												$this->User->id = $user['User']['id'];
												$this->User->saveField('balance', $data['BalanceUpdate']['balance']);
												$this->User->saveField('wh', $data['BalanceUpdate']['wh']);
												$this->BalanceUpdate->create();
												$this->BalanceUpdate->save($data);
											}
										}
									}
								}
							}	
						}
						
						// echo "0";
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