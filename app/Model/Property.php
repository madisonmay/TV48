<?php

    class Property extends AppModel {

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A first name is required'
                )
            )
        );
    }
?>