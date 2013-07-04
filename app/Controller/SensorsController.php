<?
	class SensorsController extends AppController {

		public function isAuthorized($user) {
			if (in_array('admin', $this->Session->read('User.roles'))) {
				return true;
			} elseif (in_array($this->action, array('lighting', 'heating', 'electricity'))) {
				return true;
			}

			return false;
		}

		public function lighting() {

		}

		public function heating() {
			//Potentially a case of mixing presentation and logic? 
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3')); 
		}

		public function electricity() {
			//Potentially a case of mixing presentation and logic? 
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3')); 
		}

		public function index() {
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