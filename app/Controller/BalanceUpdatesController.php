<?php

class BalanceUpdatesController extends AppController {

	public $components = array('RequestHandler');

	public function isAuthorized($user) {
		if (in_array('landlord', $this->Session->read('User.roles'))) {
			return true;
		} 
		if (in_array('admin', $this->Session->read('User.roles'))) {
			return true;
		} 
		if (in_array($this->action, array('status_report'))) {
			return true;
		}
		return false;
	}

	public function index() {
		$this->set('title_for_layout', 'Add Expenses');
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

	public function manage() {
		$this->set('title_for_layout', 'Balance Updates');
		$opts = array('conditions' => array('BalanceUpdate.room_id' => 0, 'BalanceUpdate.sensor_id' => 0,
					  'BalanceUpdate.text !=' => '', 'BalanceUpdate.reimbursement' => 0),
					  'order' => array('BalanceUpdate.created DESC', 'BalanceUpdate.text'));
		$updates = $this->BalanceUpdate->find('all', $opts);
		$filtered_updates = array();
		foreach ($updates as $update) {
			$opts = array('conditions' => array('BalanceUpdate.reimbursement' => $update['BalanceUpdate']['id']));
			if (!$this->BalanceUpdate->find('first', $opts)) {
				array_push($filtered_updates, $update);
			}
		}
		$this->set('updates', $filtered_updates);
	}

	public function remove() {
		$this->loadModel('User');
		if ($this->request->is('post')) {
			$id = $this->request->data['id'];
			$update = $this->BalanceUpdate->findById($id);
			$user = $this->User->findById($update['User']['id']);
			$data = array('BalanceUpdate' => array());
			$data['BalanceUpdate']['delta'] = $update['BalanceUpdate']['delta'];
			$data['BalanceUpdate']['text'] = $update['BalanceUpdate']['text'] . ' reimbursed';
			$data['BalanceUpdate']['user_id'] = $id;
			$data['BalanceUpdate']['balance'] = $user['User']['balance'] + $update['BalanceUpdate']['delta'];
			$data['BalanceUpdate']['reimbursement'] = $id;
			$this->BalanceUpdate->create();
			if ($this->BalanceUpdate->save($data)) {
				$this->User->id = $update['User']['id'];
				$this->User->saveField('balance', $data['BalanceUpdate']['balance']);
				echo "1";
				exit(0);
			} else {
				echo '0';
				exit(0);
			}
		}
	}

	public function status_report() {
		$this->loadModel('User');
		$this->response->type('pdf');
		$user = $this->User->findById($this->request->query('id'));
		$this->set('user', $user);

		$this->set('title_for_layout', 'Status Report' . $user['User']['full_name']);
		$opts = array('conditions' => array('BalanceUpdate.room_id' => 0, 'BalanceUpdate.sensor_id' => 0,
					  'BalanceUpdate.text !=' => '', 'BalanceUpdate.reimbursement' => 0),
					  'order' => array('BalanceUpdate.created DESC', 'BalanceUpdate.text'));
		$updates = $this->BalanceUpdate->find('all', $opts);
		$filtered_updates = array();

		foreach ($updates as $update) {
			$opts = array('conditions' => array('BalanceUpdate.reimbursement' => $update['BalanceUpdate']['id']));
			if (!$this->BalanceUpdate->find('first', $opts)) {
				array_push($filtered_updates, $update);
			}
		}

		$deposits = array();
		foreach ($user['BalanceUpdate'] as $update) {
		    $date = date('F j, Y', $update['created']);
		    if ($update['room_id']) {
		    	//no-op
		    } else {
		        array_push($deposits, $update);
		    }
		}

		function cmp($a, $b) {
			return strtotime($a[0]) > strtotime($b[0]);
		}

		date_default_timezone_set('Europe/Brussels');
		$month_wh = array();
		$mod_month_wh = array();
		foreach ($user['BalanceUpdate'] as $update) {
		    if ($update['room_id']) {
		        $date = strftime('%B %Y', $update['created']);
		        if (isset($month_wh[$date])) {
		        	$month_wh[$date] += $update['delta'];
		        } else {
		        	$month_wh[$date] = $update['delta'];
		        }
		    }
		}

		foreach($month_wh as $date => $cost) {
			array_push($mod_month_wh, array($date, $cost));
		}

		usort($mod_month_wh, 'cmp');
		$this->set('deposits', $deposits);
		$this->set('updates', $filtered_updates);
		$this->set('month_wh', $mod_month_wh);

		$this->render('/BalanceUpdates/pdf/status_report');
	}

	public function all_reports() {
		$this->loadModel('User');
		$this->response->type('pdf');
		$this->set('title_for_layout', 'Status Reports');

		function cmp($a, $b) {
			return strtotime($a[0]) > strtotime($b[0]);
		}

		$users = $this->User->find('all');
		$users = $this->filterByRole($users, 'tenant');
		$mod_users = array();
		$final_users = array();

		foreach ($users as $user) {
			if ($this->has_room($user['User']['id'])) {
				array_push($mod_users, $user);
			}
		}

		foreach ($mod_users as $user) {
			$opts = array('conditions' => array('BalanceUpdate.room_id' => 0, 'BalanceUpdate.sensor_id' => 0,
						  'BalanceUpdate.text !=' => '', 'BalanceUpdate.reimbursement' => 0),
						  'order' => array('BalanceUpdate.created DESC', 'BalanceUpdate.text'));
			$updates = $this->BalanceUpdate->find('all', $opts);
			$filtered_updates = array();

			foreach ($updates as $update) {
				$opts = array('conditions' => array('BalanceUpdate.reimbursement' => $update['BalanceUpdate']['id']));
				if (!$this->BalanceUpdate->find('first', $opts)) {
					array_push($filtered_updates, $update);
				}
			}

			$deposits = array();
			foreach ($user['BalanceUpdate'] as $update) {
			    $date = date('F j, Y', $update['created']);
			    if ($update['room_id']) {
			    	//no-op
			    } else {
			        array_push($deposits, $update);
			    }
			}

			date_default_timezone_set('Europe/Brussels');
			$month_wh = array();
			$mod_month_wh = array();
			foreach ($user['BalanceUpdate'] as $update) {
			    if ($update['room_id']) {
			        $date = strftime('%B %Y', $update['created']);
			        if (isset($month_wh[$date])) {
			        	$month_wh[$date] += $update['delta'];
			        } else {
			        	$month_wh[$date] = $update['delta'];
			        }
			    }
			}

			foreach($month_wh as $date => $cost) {
				array_push($mod_month_wh, array($date, $cost));
			}

			usort($mod_month_wh, 'cmp');
			$user['deposits'] = $deposits;
			$user['updates'] = $filtered_updates;
			$user['month_wh'] = $mod_month_wh;
			array_push($final_users, $user);
		}
		$this->set('users', $final_users);
		$this->render('/BalanceUpdates/pdf/all_reports');
	}
}

?>