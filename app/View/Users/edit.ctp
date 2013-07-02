<script>
    $(function() {
        $(".date").blur(function() {
            var date = Date.parse($(this).val()).toString('MMMM d, yyyy')
            $(this).val(date);
        });

        $("select").selectpicker();
    })
</script>
<? echo $this->Html->script('date'); ?>
<div class="users form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Edit tenant'); ?></legend>
        <?php 
            $first_name = array('class' => 'span3', 'placeholder' => 'First name...', 'label' => '', 'required' => true);
            $last_name = array('class' => 'span3', 'placeholder' => 'Last name...', 'label' => '','required' => true);
            $username = array('class' => 'span3', 'placeholder' => 'Email...', 'label' => '', 'type' => 'email', 'required' => true);
            $start_date = array('class' => 'span3 date', 'placeholder' => 'Start date...', 'label' => '', 'type' => 'text', 'required' => true);
            $end_date = array('class' => 'span3 date', 'placeholder' => 'End date...', 'label' => '', 'type' => 'text', 'required' => true);
            $balance = array('class' => 'span3', 'placeholder' => 'Balance...', 'label' => '', 'type' => 'text');
            echo $this->Form->input('first_name', $first_name);
            echo $this->Form->input('last_name', $last_name);
            echo $this->Form->input('email', $username);
            echo $this->Form->input('start_date', $start_date);
            echo $this->Form->input('end_date', $end_date);
            echo $this->Form->input('balance', $balance);
            echo '<div style="margin-right: auto; margin-left: auto; float: none; margin-top: 6px; margin-bottom: 6px;" class="span3">';
            echo $this->Form->select('Rooms', $rooms, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
            $end = array('label' => 'Save', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>