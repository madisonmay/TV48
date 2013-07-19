<?php

    App::uses('AuthComponent', 'Controller/Component');
    class BalanceUpdate extends AppModel {

        public $belongsTo = array('User', 'Room', 'Sensor');
        
        public $validate = array(
            'user_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A user id is required'
                )
            ),
            'delta' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A delta is required'
                )
            ),
            'balance' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A balance is required'
                )
            )
        );
    }
?>