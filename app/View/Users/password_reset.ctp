<div class="users form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Reset password'); ?></legend>
        <?php 
            $username = array('class' => 'span3', 'placeholder' => 'Email...', 'label' => '', 'type' => 'email', 'required' => true);
            echo $this->Form->input('username', $username);
            $end = array('label' => 'Send', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>