<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Contract extends AppModel {

        public $belongsTo = array('Room', 'User');
        
        public $validate = array(
            'room_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A room id is required'
                )
            ),
            'user_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A user id is required'
                )
            ),
            'start_date' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A start date is required'
                )
            ),
            'end_date' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'An end date is required'
                )
            )
        );
    }
?>