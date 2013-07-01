<div class="users form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Please enter your email address and password'); ?></legend>
        <?php 
        	$username = array('class' => 'span3', 'label' => '', 'placeholder' => 'Email...');
        	echo $this->Form->input('email', $username);
        	$password = array('class' => 'span3', 'label' => '', 'placeholder' => 'Password...');
        	echo $this->Form->input('password', $password);
	        $options = array('label' => 'Login',
							 'class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($options); ?>
</div>