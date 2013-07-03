<script>
    $(document).ready(function() {
        $("select").selectpicker();
    });
</script>

<div class="form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User', array('action' => 'edit', 'type' => 'get')); ?>
    <fieldset>
        <legend><?php echo ('Manage Tenants'); ?></legend>
        <?
            echo '<div class="span3 select-wrapper">';
            echo $this->Form->select('Users', $users, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
            $end = array('label' => 'Edit','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>

</div>