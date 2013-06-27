<?
	class TenantsController extends AppController {

		public function index() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array('date'));
			$tenant_opts = array('conditions' => array('Tenant.property_id' => $this->request->query['property']),
								 'fields' => array('Tenant.id')); 
			$tenant_ids = array_values($this->Tenant->find('list', $tenant_opts));
			$opts   = array(
			         'fields' => array('id', 'full_name'),
			         'conditions' => array('User.tenant_id' => $tenant_ids)
			);
			$this->set('tenants', $this->Tenant->User->find('list', $opts)); 
		}

		public function add() {
		    if ($this->request->is('post')) {
		        $this->Tenant->create();
		        $this->request->data['Tenant']['property_id'] = $this->request->query['property']; 
		        if ($this->Tenant->save($this->request->data)) {
		            // Redirect user to hompage
		            $this->request->data['User']['tenant_id'] = $this->Tenant->id;
		            $this->request->data['User']['landlord_id'] = $this->Auth->user('id');
		            $this->Tenant->User->create();
		            if ($this->Tenant->User->save($this->request->data)) {
		            	$this->request->data['Tenant']['user_id'] = $this->Tenant->User->id;
		            	if ($this->Tenant->save($this->request->data)) {
				            $this->Session->write('flashWarning', 0);
				            $this->Session->setFlash(__('Tenant added!'));
				            $this->redirect('/properties/edit?Properties=' . $this->request->query['property']);
				        } else {
				        	$this->Session->write('flashWarning', 1);
				        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
				        }
			        } else {
			        	$this->Session->write('flashWarning', 1);
			        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));
			        }
		        } else {
		            $this->Session->write('flashWarning', 1);
		            $this->Session->setFlash(__('An internal error occurred.  Please try again.'));
		        }
		    }
		}

		public function edit() {
		    if ($this->request->is('get')) {
		    	//select tenant by user_id
		    	$user_id = $this->request->query['Tenants'];
		        $this->data = $this->Tenant->find('first', array('conditions' => array('Tenant.user_id' => $user_id)));
		    } else {

		    	//ensure fields are updated, and a new entry is not inserted
		    	//all that you have to do is set the id of the model and the associated model
		    	//security on the front end might be a bit difficult, though
		    	$this->Tenant->id = $this->request->data['Tenant']['id'];
		    	$this->Tenant->User->id = $this->request->data['User']['id'];

    	        if ($this->Tenant->save($this->request->data)) {
    	            if ($this->Tenant->User->save($this->request->data)) {
    	            	if ($this->Tenant->save($this->request->data)) {
    			            $this->Session->write('flashWarning', 0);
    			            $this->Session->setFlash(__('Tenant saved!'));
    			            $this->redirect('/properties/edit?Properties=' . $this->request->data['Property']['id']);
    			        } else {
    			        	$this->Session->write('flashWarning', 1);
    			        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
    			        }
    		        } else {
    		        	$this->Session->write('flashWarning', 1);
    		        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));
    		        }
    	        } else {
    	            $this->Session->write('flashWarning', 1);
    	            $this->Session->setFlash(__('An internal error occurred.  Please try again.'));
    	        }
		    }
		}
	}
?>