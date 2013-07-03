<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Room extends AppModel {

        public $hasAndBelongsToMany = array('User');
        public $hasMany = array('Contract', 'Sensor');

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A room name is required'
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