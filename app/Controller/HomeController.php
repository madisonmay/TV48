<?
	class HomeController extends AppController {

		function beforeFilter() {
			parent::beforeFilter();
			$this->Auth->allow('index');
		}

		function isAuthorized($user) {
			if (!in_array('landlord', $this->Session->read('User.roles')) && $this->action == 'manage') {
				return false;
			} 

			return true;
		}

		public function index() {
			$this->set('title_for_layout', 'CORE: Home');
			$this->set('cssIncludes', array('home'));
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3', 'home'));
		}

		public function manage() {
			$this->set('title_for_layout', 'Management');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array()); 
		}
	}
?>
