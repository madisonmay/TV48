<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $helpers = array('Html', 'Form', 'Session');
	public $components = array('Session', 'DebugKit.Toolbar', 'Auth' => array(
        'loginRedirect' => array('controller' => 'home', 'action' => 'index'),
        'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
        'authenticate' => array(
	            'Form' => array(
	                'fields' => array('username' => 'email')
	            )
	        )
        )
	);

	function beforeFilter() {
	    parent::__construct();
	    // Your app-wide beforeFilter code, if any
	    $this->Auth->allow(array('controller' => 'users', 'action' => 'login'), 
	    	array('controller' => 'users', 'action' => 'login'));
	}

	public function permissions($user_id) {
		//by default, all permissions are set to 0;
		$permissions = array('view_public' => 0, 'modify_public' => 0, 'pay_public' => 0); 

		//load models
		$this->loadModel('User');
		$this->loadModel('Role');

		//find user object
		$opts = array('conditions' => array('id' => $user_id));
		$user = $this->User->find('first', $opts);

		//find all corresponding role objects
		$opts = array('conditions' => array('name' => json_decode($user['User']['roles'])));
		$user_roles = $this->Role->find('all', $opts);

		//iterate through and flip permissions to 1 where applicable
		foreach ($user_roles as $user_role) {
			foreach ($permissions as $k => $v) {
				if ($user_role['Role'][$k]) {
					$permissions[$k] = 1;
				}
			}
		}
		return $permissions;
	}

	public function hasRole($role, $user) {
		return in_array($role, json_decode($user['User']['roles']));
	}

	public function filterByRole($users, $role) {
		$result = array();
		foreach ($users as $user) {
			if ($this->hasRole($role, $user)) {
				array_push($result, $user);
			}
		}
		return $result;
	}

	public function findByRole($role) {
		$this->loadModel('User');

		$users = $this->User->find('all');
		$users = $this->filterByRole($users, "tenant");

		$user_list = array();

		foreach ($users as $user) {
			$user_list[$user['User']['id']] = $user['User']['full_name'];
		}

		return $user_list;
	}

	public function removeByRole($users, $role) {
		$result = array();
		foreach ($users as $user) {
			if (!$this->hasRole($role, $user)) {
				array_push($result, $user);
			}
		}
		return $result;
	}

	public function has_room($user_id) {
		//replaces has_room attribute in database
		$this->loadModel('Contract');	
		$opts = array('conditions' => array('primary' => 1, 'user_id' => $user_id, 'deactivated' => 0));
		$primary_contract = $this->Contract->find('first', $opts);
		if ($primary_contract) {
			return true;
		}
		return false;
	}

	public function room_available($room_id) {
		//replace 'available' attribute in database
		$this->loadModel('Contract');
		$opts = array('conditions' => array('primary' => 1, 'room_id' => $room_id, 'deactivated' => 0));
		$primary_contract = $this->Contract->find('first', $opts);
		if ($primary_contract) {
			return false;
		}
		else {
			$room = $this->Room->findById($room_id);
			if ($room['Room']['type'] == 'public') {
				return false;
			}

			//room is not public and is not occupied
			return true;
		}
	}

	public function findAvailableRooms() {
		$this->loadModel('Room');
		$rooms = $this->Room->find('all');

		$result = array();
		foreach ($rooms as $room) {
			if ($this->room_available($room['Room']['id'])) {
				array_push($result, $room);
			}
		}

		$room_list = array();

		foreach ($result as $room) {
			$room_list[$room['Room']['id']] = $room['Room']['name'];
		}

		return $room_list;
	}

	public function removeOldRoomContract($room_id) {
		//load models
		$this->loadModel('Room');
		$this->loadModel('Contract');

		//update room to reflect occupancy
		$this->Room->id = $room_id;
		$this->Room->saveField('available', 0);

		//if the room had a previous user, deactivate old contract
		$opts = array('conditions' => array('primary' => 1, 'room_id' => $room_id, 'deactivated' => 0));
		$old_contract = $this->Contract->find('first', $opts);

		if ($old_contract) {
		    //deactivate old contract
		    date_default_timezone_set('Europe/Brussels');
		    $datetime = date('F j, Y', time());
		    $this->Contract->id = $old_contract['Contract']['id'];
		    $this->Contract->saveField('deactivated', $datetime);
		}
	}

	public function removeOldUserContract($user_id) {
		//load models
		$this->loadModel('Room');
		$this->loadModel('Contract');

		$opts = array('conditions' => array('primary' => 1, 'user_id' => $user_id, 'deactivated' => 0));
		$old_contract = $this->Contract->find('first', $opts);

		if ($old_contract) {
			date_default_timezone_set('Europe/Brussels');
			$datetime = date('F j, Y', time());
			$this->Contract->id = $old_contract['Contract']['id'];
			$this->Contract->saveField('deactivated', $datetime);

			//if user had an old room, set available to true (1)
			$this->Room->id = $old_contract['Contract']['room_id'];
			$this->Room->saveField('available', 1);
		}
	}

	//****************************************************************************************************
	//these three functions are too similar -- should be refactored into one or two more general functions
	//****************************************************************************************************
	public function addUserSecondaryContracts($user_id) {

		$this->loadModel('Room');
		$this->loadModel('Contract');
		$this->loadModel('User');

		$user = $this->User->find('first', array('conditions' => array('id' => $user_id)));

		$rooms = $this->Room->find('all', array('conditions' => array('type' => 'public')));

		date_default_timezone_set('Europe/Brussels');
		foreach ($rooms as $room) {
			//array for storing contract values
			$fields = array();

			//convention
			$room_id = $room['Room']['id'];

			$fields['user_id'] = $user_id;
			$fields['room_id'] = $room_id;
			$datetime = date('F j, Y', time());
			$fields['start_date'] = $user['User']['start_date'];
			$fields['end_date'] = $user['User']['end_date'];
			
			//generic approach
			$permissions = $this->permissions($user_id);
			$fields['view'] = $permissions['view_public'];
			$fields['pay'] = $permissions['pay_public'];
			$fields['modify'] = $permissions['modify_public'];
			//deactivated and primary both default to zero

			$this->Contract->create();
			if ($this->Contract->save($fields)) {
				//success
			} else {
				$this->Session->write('flashWarning', 1);
				$this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
			}
		}
                    
	}

	public function updateUserSecondaryContracts($user_id) {

		//load contract and user models
		$this->loadModel('Contract');
		$this->loadModel('User');

		// update all current contracts to reflect change
		// only need to modify "public" contracts now when changed from studio -> dorm
		// or from dorm -> studio since code above handled primary case
		$opts = array('conditions' => array('user_id' => $user_id, 'deactivated' => 0, 'primary' => 0));
		$contracts = $this->Contract->find('all', $opts); //public contracts

		//retrieve array of permissions
		$permissions = $this->permissions($user_id);

	    date_default_timezone_set('Europe/Brussels');
	    foreach ($contracts as $contract) {

	    	//deactivate old contracts
	        $datetime = date('F j, Y', time());
	        $this->Contract->id = $contract['Contract']['id'];
	        $this->Contract->saveField('deactivated', $datetime);

	        //add new contracts -- this way it is simpler to keep track of payments
	        $fields = $contract['Contract'];
	        $fields['id'] = '';
	        $fields['start_date'] = $datetime;
	        $fields['view'] = $permissions['view_public'];
	        $fields['pay'] = $permissions['pay_public'];
	        $fields['modify'] = $permissions['modify_public'];

	        $this->Contract->create();
	        if($this->Contract->save($fields)) {
	        	//success
	        } else {
	        	//error out
	        }
	    }                            
	}

	public function updateSecondaryContracts($room_id, $room_type) {
		//handle case of landlord and admin -- they should not be restricted

		//load contract Model
		$this->loadModel('Contract');
		$this->loadModel('User');
		// update all current contracts to reflect change
		// only need to modify "public" contracts now when changed from studio -> dorm
		// or from dorm -> studio since code above handled primary case
		$opts = array('conditions' => array('room_id' => $room_id, 'deactivated' => 0));
		$contracts = $this->Contract->find('all', $opts); //public contracts

		//eventually users home timezone should be selected
	    date_default_timezone_set('Europe/Brussels');
	    foreach ($contracts as $contract) {

	    	$user_id = $contract['Contract']['user_id'];
	    	$user = $this->findById($user_id);
	    	if (!has_role('admin', $user) && !has_role('landlord', $user)) {
	    		//ensure landlords and admins do not have room access restricted

		    	//deactivate old contracts
		        $datetime = date('F j, Y', time());
		        $this->Contract->id = $contract['Contract']['id'];
		        if ($this->Contract->saveField('deactivated', $datetime)) {
		        	//success
		        } else {
		        	echo $this->getLastQuery();
		        }	
	    	}
	    }        

	    if ($room_type === 'public')  {
	    	$this->addSecondaryContracts($room_id);
	    }                 
	}

	public function addSecondaryContracts($room_id) {
		//load models
		$this->loadModel('User');
		$this->loadModel('Contract');

		$users = $this->User->find('all');
		date_default_timezone_set('Europe/Brussels');
		foreach ($users as $user) {
			//array for storing contract values
			$fields = array();

			//convention
			$user_id = $user['User']['id'];

			$fields['user_id'] = $user_id;
			$fields['room_id'] = $room_id;
			$datetime = date('F j, Y', time());
			$fields['start_date'] = $datetime;
			$fields['end_date'] = $user['User']['end_date'];
			
			//generic approach
			$permissions = $this->permissions($user_id);
			$fields['view'] = $permissions['view_public'];
			$fields['pay'] = $permissions['pay_public'];
			$fields['modify'] = $permissions['modify_public'];
			//deactivated and primary both default to zero

			$this->Contract->create();
			if($this->Contract->save($fields)) {
				//success
			} else {
				echo $this->Contract->getLastQuery();
				exit(0);
			}
		}
	}


	public function addRole($user_id, $role_name) {
		//load models
		$this->loadModel('User');

		//find user object
		$opts = array('conditions' => array('id' => $user_id));
		$user = $this->User->find('first', $opts);

		$roles = json_decode($user['User']['roles']);
		if (!in_array($role_name, $roles)) {
			array_push($roles, $role_name);
		}

		$this->User->id = $user_id;
		$this->User->saveField('roles', json_encode($roles));
	}


	public function removeRole($user_id, $role_name) {
		//load models
		$this->loadModel('User');

		//find user object
		$opts = array('conditions' => array('id' => $user_id));
		$user = $this->User->find('first', $opts);

		$roles = json_decode($user['User']['roles']);

		//find index of role in roles
		$role_index = array_search($role_name, $roles);

		//if found, splice array and remove desired role
		if ($role_index) {
			array_splice($role_index, 1);
		}

		//update user
		$this->User->id = $user_id;
		$this->User->saveField('roles', json_encode($roles));
	}

	public function addAdminsAndLandlords($room_id) {
		$this->loadModel('User');
		$this->loadModel('Contract');

		$landlords = $this->filterByRole('landlord');
		$admins = $this->filterByRole('admin');

		$users = $landlords + $admins;
		foreach ($users as $user_id => $user_name) {
			//array for storing contract values
			$fields = array();

			//convention
			$fields['user_id'] = $user_id;
			$fields['room_id'] = $room_id;
			$datetime = date('F j, Y', time());
			$fields['start_date'] = $datetime;
			
			//generic approach
			$permissions = $this->permissions($user_id);
			$fields['view'] = $permissions['view_public'];
			$fields['pay'] = $permissions['pay_public'];
			$fields['modify'] = $permissions['modify_public'];
			//deactivated and primary both default to zero

			$this->Contract->create();
			if($this->Contract->save($fields)) {
				//success
			} else {
				echo $this->Contract->getLastQuery();
				exit(0);
			}
		}
	}

	public function addAllRooms($user_id) {
		$this->loadModel('Room');
		$this->loadModel('Contract');

		$rooms = $this->Room->find('all');

		foreach ($rooms as $room) {
			//array for storing contract values
			$fields = array();

			//convention
			$fields['user_id'] = $user_id;
			$fields['room_id'] = $room['Room']['id'];
			$datetime = date('F j, Y', time());
			$fields['start_date'] = $datetime;
			
			//generic approach
			$permissions = $this->permissions($user_id);
			$fields['view'] = $permissions['view_public'];
			$fields['pay'] = $permissions['pay_public'];
			$fields['modify'] = $permissions['modify_public'];
			//deactivated and primary both default to zero

			$this->Contract->create();
			if($this->Contract->save($fields)) {
				//success
			} else {
				echo $this->Contract->getLastQuery();
				exit(0);
			}
		}
	}
}
