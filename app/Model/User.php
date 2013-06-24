<?php

    App::uses('AuthComponent', 'Controller/Component');
    class User extends AppModel {
        public $validate = array(
            'username' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A username is required'
                )
            ),
            'password' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A password is required'
                )          
            )
        );
    }
?>