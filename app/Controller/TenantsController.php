<?
	class TenantsController extends AppController {

		public function index() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('date'));
			$this->set('tenants', $this->Tenant->find('list')); 
		}

		public function add() {
		    if ($this->request->is('post')) {
		        $this->Tenant->create();
		        if ($this->Tenant->save($this->request->data)) {
		        	$this->Session->write('flashWarning', 1);
		        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));
		        } else {
		            $this->Session->write('flashWarning', 1);
		            $this->Session->setFlash(__('An internal error occurred.  Please try again.'));
		        }
		    }
		}

		public function edit() {
		    if ($this->request->is('get')) {
		        $this->set('property', $this->Tenant->findById($this->request->query));
		    }
		}

		public function save() {
			if ($this->request->is('post')) {
				
			    if ($this->Property->save($this->request->data)) {
			        // Redirect user to hompage
			        $this->Session->write('flashWarning', 0);
			        $this->Session->setFlash(__('Tenant saved!'));
			        $this->redirect(array('controller' => 'properties', 'action' => 'edit'));
			    } else {
			        $this->Session->write('flashWarning', 1);
			        $this->Session->setFlash(__('An internal error occurred.  Please try again.'));
			    }
			}

		}

	}
?>