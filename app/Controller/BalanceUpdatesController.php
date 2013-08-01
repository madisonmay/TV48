<?php

class BalanceUpdatesController extends AppController {

	public function isAuthorized($user) {
		if (in_array('landlord', $this->Session->read('User.roles'))) {
			return true;
		} 
		if (in_array('admin', $this->Session->read('User.roles'))) {
			return true;
		} 
		return false;
	}

	public function index() {
		//divide users into categories in order to provide convenience functions
		//for selecting users for billing
		$this->loadModel('User');
		$this->loadModel('Room');
		$users = $this->User->find('all');
		$users = $this->filterByRole($users, 'tenant');
		$mod_users = array();
		foreach ($users as $user) {
			if ($this->has_room($user['User']['id'])) {
				// echo $user['User']['full_name'] . ' active<br>';
				$user['active'] = 1;
				$user['primary_contract'] = $this->primaryContract($user['User']['id']);
				$room = $this->Room->findById($user['primary_contract']['room_id']);
				$user['Room'] = $room['Room'];
				array_push($mod_users, $user);
			} else {
				// echo $user['User']['full_name'] . ' inactive<br>';
				$user['active'] = 0;
				$user['Room'] = array('name' => 'None', 'type' => 'None');
				$user['primary_contract'] = array('start_date'=>$user['User']['start_date'], 'end_date'=>$user['User']['end_date']);
				array_push($mod_users, $user);
			}
		}
		$this->set('users', $mod_users);
	}

	public function charge() {
		$this->loadModel('User');
		if ($this->request->is('post')) {
			$delta = $this->request->data['delta'];
			$text = $this->request->data['text'];
			$ids = $this->request->data['ids'];
			$num = count($ids);

			if (!$delta || !$text) {
				$this->Session->write('flashWarning', 1);
				$this->Session->setFlash(__('Please fill out the "Reason" and "Cost" fields.')); 
				echo '0';
				exit(0);
			}

			foreach ($ids as $id) {
				$user = $this->User->findById($id);
				$data = array('BalanceUpdate' => array());
				$data['BalanceUpdate']['delta'] = $delta/$num;
				$data['BalanceUpdate']['text'] = $text;
				$data['BalanceUpdate']['user_id'] = $id;
				$data['BalanceUpdate']['balance'] = $user['User']['balance'] - $delta/$num;
				$this->BalanceUpdate->create();
				if ($this->BalanceUpdate->save($data)) {
					$this->User->id = $id;
					$this->User->saveField('balance', $data['BalanceUpdate']['balance']);
				} else {
					$this->Session->write('flashWarning', 1);
					$this->Session->setFlash(__('User balances could not be updated.  Please try again!')); 
					echo '0';
					exit(0);
				}
			}

			$this->Session->write('flashWarning', 0);
			$this->Session->setFlash(__('User balances updated successfully!')); 
			echo '1';
			exit(0);
		} 
	}
}

?>