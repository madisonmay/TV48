<script>
    function validate() {
        var property = $('#PropertyProperties').val();
        if (property == null || property == "") {
          return false;
        }
    }

    $(document).ready(function() {
        $("select").selectpicker();
    });
</script>

<div class="property form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Property', array('action' => 'edit', 'type' => 'get', 'onsubmit' => 'return validate()')); ?>
    <fieldset>
        <legend><?php echo ('Manage Rental Properties'); ?></legend>
        <?
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3">';
            echo $this->Form->select('Properties', $properties, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
            $end = array('label' => 'Edit','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>

</div>

<div class="property form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Property', array('action' => 'add')); ?>
    <fieldset>
        <legend><?php echo ('Add Rental Property'); ?></legend>
        <?
            echo $this->Form->input('name', array('class' => 'span3', 'placeholder' => 'Property name...', 'label' => ''));
            $end = array('label' => 'Add','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>
