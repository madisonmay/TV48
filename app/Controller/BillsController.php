<?
	class BillsController extends AppController {

		//currently deprecated in favor of prepaid style billing

		function beforeFilter() {
			parent::beforeFilter();
			$this->Auth->allow('index');
		}

		public function isAuthorized($user) {
			if (in_array('landlord', $this->Session->read('User.roles'))) {
				return true;
			} 
			if (in_array('admin', $this->Session->read('User.roles'))) {
				return true;
			} 
			return false;
		}

		public function index() {
			if ($this->request->is('get')) {

			} else {
				
			}
		}

		// public function index() {
		// 	//currently not used but may be reimplemented in the future
		// 	if ($this->request->is('post')) {
		// 		$this->loadModel('Contract');
		// 		$this->loadModel('Sensor');
		// 		$this->loadModel('Data');
		// 		$electric_total = $this->request->data['Bill']['electricity_cost'];
		// 		$start = strtotime($this->request->data['Bill']['start_date']);
		// 		if ($this->request->data['Bill']['start_date']) {
		// 			$end = strtotime($this->request->data['Bill']['end_date']);
		// 		} else {
		// 			$end = strtotime('today');
		// 		}

		// 		//complex and complicated -- take it slow
		// 		//remaining steps -- figure out how many different contracts are valid at one time, then divide the total
		// 		//for each contract by that amount //for each contract, add the new amount to an array of use value.
		// 		//sum up all user usages and convert absolute -> percentage.
		// 		//take a percentage of the original price

		// 		$sensor_opts = array('conditions' => array('Sensor.type' => 'electricity'));
		// 		$sensors = $this->Sensor->find('all', $sensor_opts);
		// 		$values = array();
		// 		foreach ($sensors as $sensor) {
		// 			$contract_opts = array('conditions' => array(
		// 				'Contract.pay' => 1, 
		// 				'Contract.room_id' => $sensor['Room']['id']));
		// 			$contracts = $this->Contract->find('all', $contract_opts);
		// 			foreach ($contracts as $contract) {
		// 				$start_date = strtotime($contract['Contract']['start_date']);
		// 				if ($contract['Contract']['deactivated']) {
		// 					$end_date = strtotime($contract['Contract']['deactivated']);
		// 				} else {
		// 					$end_date = strtotime($contract['Contract']['end_date']);
		// 				}
		// 				if ($end_date > $start && $start_date < $end) {
		// 					$first = max(array($start, $start_date));
		// 					$last = min(array($end, $end_date));
		// 					$first_opts = array('conditions' => array('Data.created >' => $first, 'Data.sensor_id' => $sensor['Sensor']['id']));
		// 					$first_value = $this->Data->find('first', $first_opts);
		// 					$last_opts = array('conditions' => array('Data.created <' => $last, 'Data.sensor_id' => $sensor['Sensor']['id']));
		// 					$last_values = $this->Data->find('all', $last_opts);
		// 					if ($last_values) {
		// 						$last_value = $last_values[count($last_values) - 1];
		// 					} else {
		// 						$last_value = 0;
		// 					}
		// 					if ($last_value) {
		// 						$delta = $last_value['Data']['value'] - $first_value['Data']['value'];
		// 						if (array_key_exists($contract['Contract']['id'], $values)) {
		// 							$values[$contract['Contract']['id']] += $delta;
		// 						} else {
		// 							$values[$contract['Contract']['id']] = $delta;
		// 						}
		// 					}
		// 				}
		// 			}
		// 		}
		// 		pr($values);
		// 		//calculate how much each user owes
		// 		exit(0);
		// 	}
		// }
	}
?>
