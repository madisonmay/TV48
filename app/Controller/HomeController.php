<?
	class HomeController extends AppController {

		function beforeFilter() {
			parent::beforeFilter();
			$this->Auth->allow('index', 'notepad');
		}

		public function isAuthorized($user) {
			if (!in_array('landlord', $this->Session->read('User.roles')) && $this->action == 'manage') {
				return false;
			} 

			return true;
		}

		public function index() {
			$this->loadModel('Notepad');
			$this->set('cssIncludes', array('home'));
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3', 'home')); 
			$notepad = $this->Notepad->find('first');
			if ($notepad) {
				$this->set('notepad_content', $notepad['Notepad']['content']);	
			} else {
				$this->set('notepad_content', '');		
			}
		}

		public function manage() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array()); 
		}

		public function notepad() {
			$this->loadModel('Notepad');
			$notepads = $this->Notepad->find('all');
			if (!count($notepads)) {
				$this->Notepad->create();
			} else {
				$this->Notepad->id = 1;
			}
			$data = array();
			$data['Notepad']['content'] = $this->request->data['content'];
			if ($this->Notepad->save($data)) {
				//success
				echo 0;
			} else {
				//error
				echo 1;
			}
			exit(0);
		}
	}
?>
