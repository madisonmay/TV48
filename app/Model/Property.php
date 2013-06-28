<?php

    class Property extends AppModel {

        public $hasMany = array("Room", "Tenant");

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A property name is required'
                )
            )
        );
    }
?>