<div class="admin form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Property'); ?>
    <fieldset>
        <legend><?php echo ('Sign up for an account'); ?></legend>
        <?php 
            $first_name = array('class' => 'span3', 'placeholder' => 'First name...', 'label' => '');
            $last_name = array('class' => 'span3', 'placeholder' => 'Last name...', 'label' => '');
            $username = array('class' => 'span3', 'placeholder' => 'Email...', 'label' => '', 'type' => 'email');
        	$password = array('class' => 'span3', 'placeholder' => 'Password...', 'label' => '');
            $confirm_password = array('class' => 'span3', 'type' => 'password', 'label' => '', 'placeholder' => 'Confirm password...', "oninput" => "checkPassword(this)");
            echo $this->Form->input('first_name', $first_name);
            echo $this->Form->input('last_name', $last_name);
        	echo $this->Form->input('username', $username);
        	echo $this->Form->input('password', $password);
            echo $this->Form->input('confirm_password', $confirm_password);
	        $end = array('label' => 'Register',
							 'class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($end); ?>