<script>
    $(document).ready(function() {
        $("select").selectpicker();
    });
</script>

<div class="tenant form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Room', array('action' => 'edit', 'type' => 'get')); ?>
    <fieldset>
        <legend><?php echo ('Manage Rooms'); ?></legend>
        <?
            echo '<div class="span3 select-wrapper">';
            echo $this->Form->select('Rooms', $rooms, array('class' => 'span3 select', 'label' => 'Room: ', 'required' => true));
            echo '</div>';
            $end = array('label' => 'Edit','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>

</div>