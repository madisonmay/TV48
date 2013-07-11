<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Sensor extends AppModel {

        public $belongsTo = array('Room');
        public $hasMany = array('Data' => array(
            'order' => 'Data.created ASC'
        ));

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A sensor is required'
                )
            ),
            'type' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A type is required'
                )
            ),
            'channel' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A channel is required'
                )
            ),
            'room_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'An associated room is required'
                )
            )
        );
    }
?>