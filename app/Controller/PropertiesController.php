<?
	class PropertiesController extends AppController {

		public function index() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array());
			$this->set('properties', $this->Property->find('list')); 
		}

		public function add() {
		    if ($this->request->is('post')) {
		        $this->Property->create();
		        if ($this->Property->save($this->request->data)) {

		            // Redirect user to hompage
		            $this->Session->write('flashWarning', 0);
		            $this->Session->setFlash(__('Property added!'));
		            $this->redirect(array('controller' => 'properties', 'action' => 'index'));
		        } else {
		            $this->Session->write('flashWarning', 1);
		            $this->Session->setFlash(__('An internal error occurred.  Please try again.'));
		        }
		    }
		}

		public function edit() {
		    if ($this->request->is('get')) {
		        $this->set('property', $this->Property->findById($this->request->query));
		    }
		}

		public function save() {
			if ($this->request->is('post')) {
				
			    if ($this->Property->save($this->request->data)) {
			        // Redirect user to hompage
			        $this->Session->write('flashWarning', 0);
			        $this->Session->setFlash(__('Property saved!'));
			        $this->redirect(array('controller' => 'properties', 'action' => 'index'));
			    } else {
			        $this->Session->write('flashWarning', 1);
			        $this->Session->setFlash(__('An internal error occurred.  Please try again.'));
			    }
			}

		}

	}
?>