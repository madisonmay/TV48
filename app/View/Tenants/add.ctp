<script>
    $(function() {
        $(".date").blur(function() {
            var date = Date.parse($(this).val()).toString('MMMM d, yyyy')
            $(this).val(date);
        });
    })
</script>
<? echo $this->Html->script('date'); ?>
<div class="users form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Tenant'); ?>
    <fieldset>
        <legend><?php echo ('Add a tenant'); ?></legend>
        <?php 
            $first_name = array('class' => 'span3', 'placeholder' => 'First name...', 'label' => '');
            $last_name = array('class' => 'span3', 'placeholder' => 'Last name...', 'label' => '');
            $username = array('class' => 'span3', 'placeholder' => 'Email...', 'label' => '', 'type' => 'email');
            $start_date = array('class' => 'span3 date', 'placeholder' => 'Start date...', 'label' => '');
            $end_date = array('class' => 'span3 date', 'placeholder' => 'End date...', 'label' => '');
            $balance = array('class' => 'span3', 'placeholder' => 'Balance...', 'label' => '', 'type' => 'text');
            echo $this->Form->input('first_name', $first_name);
            echo $this->Form->input('last_name', $last_name);
            echo $this->Form->input('username', $username);
            echo $this->Form->input('start_date', $start_date);
            echo $this->Form->input('end_date', $end_date);
            echo $this->Form->input('balance', $balance);
            $end = array('label' => 'Add', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>