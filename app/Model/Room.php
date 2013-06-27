<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Room extends AppModel {

        public $belongsTo = array('Property', 'Tenant');

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A room name is required'
                )
            ),
            'property_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A property_id is required'
                )
            ),
            'type' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A type is required'
                )
            )
        );
    }
?>