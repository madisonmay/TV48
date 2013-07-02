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


	public function filterByRole($users, $role) {
		$result = array();
		foreach ($users as $user) {
			if (in_array($role, json_decode($user['User']['roles']))) {
				array_push($result, $user);
			}
		}
		return $result;
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

	public function updateSecondaryContracts($user_id) {

		//load contract Model
		$this->loadModel('Contract');

		// update all current contracts to reflect change
		// only need to modify "public" contracts now when changed from studio -> dorm
		// or from dorm -> studio since code above handled primary case
		$opts = array('conditions' => array('user_id' => $user_id, 'deactivated' => 0, 'primary' => 0));
		$contracts = $this->Contract->find('all', $opts); //public contracts

		//retrieve array of permissions
		$permissions = $this->permissions($user_id);

	    foreach ($contracts as $contract) {
	        $this->Contract->id = $contract['Contract']['id'];
	        $this->Contract->saveField('pay', $permissions['pay_public']);
	        $this->Contract->saveField('modify', $permissions['modify_public']);
	        $this->Contract->saveField('view', $permissions['view_public']);
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
}
