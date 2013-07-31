<div class="bills form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('BalanceUpdate'); ?>
    <fieldset>
        <legend><?php echo ('Please enter your email address and password'); ?></legend>
        <?php 
        	$username = array('class' => 'span3', 'label' => 'Email: ', 'placeholder' => 'john.doe@example.com...');
        	echo $this->Form->input('email', $username);
        	$password = array('class' => 'span3', 'label' => 'Password: ', 'placeholder' => '•••••••••••');
        	echo $this->Form->input('password', $password);
            if (isset($reset)) {
                echo "<div class=centered style='margin-bottom: 10px;'>";
                echo "<a href='/users/password_reset'>Forgot your password?</a>";
                echo "</div>";
            }
	        $options = array('label' => 'Login',
							 'class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($options); ?>
</div>
