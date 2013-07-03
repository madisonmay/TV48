<?
	class SensorsController extends AppController {

		public function index() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array());
			$lighting = array("conditions" => array("Sensor.type" => "lighting"));
			$heating = array("conditions" => array("Sensor.type" => "heating"));
			$electricity = array("conditions" => array("Sensor.type" => "electricity"));
			$this->set('lighting', $this->Sensor->find('all', $lighting)); 
			$this->set('heating', $this->Sensor->find('all', $heating)); 
			$this->set('electricity', $this->Sensor->find('all', $electricity)); 
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
	}
?>