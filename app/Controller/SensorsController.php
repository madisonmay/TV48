<?
	class SensorsController extends AppController {

		public function index() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array());
			$this->set('sensors', $this->Sensor->find('all')); 
		}
	}
?>