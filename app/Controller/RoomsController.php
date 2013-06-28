<?
	class RoomsController extends AppController {

		public function index() {
			$opts = array('fields' => array('id', 'name'),
			              'conditions' => array('Room.property_id' => $this->request->query['property'])
			);
			$this->set('rooms', $this->Room->find('list', $opts)); 
		}

		public function add() {
		    if ($this->request->is('post')) {
		        $this->Room->create();
		        $this->request->data['Room']['property_id'] = $this->request->query['property']; 
		        if ($this->Room->save($this->request->data)) {
		            $this->Session->write('flashWarning', 0);
		            $this->Session->setFlash(__('Room added!'));
		            $this->redirect('/properties/edit?Properties=' . $this->request->query['property']);

			    } else {
		        	$this->Session->write('flashWarning', 1);
		        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
		        }		   
		    } else {
		    	//get request -- render input form
				$tenant_opts = array('conditions' => array('Tenant.property_id' => $this->request->query['property']),
									 'fields' => array('Tenant.id')); 
				$tenant_ids = array_values($this->Room->Tenant->find('list', $tenant_opts));
				$opts = array('fields' => array('id', 'full_name'),
				              'conditions' => array('User.tenant_id' => $tenant_ids)
				);
				$this->set('tenants', $this->Room->Tenant->User->find('list', $opts)); 
		    }

		}
	}
?>