<script>
    $(document).ready(function() {
        $("select").selectpicker();
    });
</script>

<div class="tenant form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Tenant', array('action' => 'edit', 'type' => 'get')); ?>
    <fieldset>
        <legend><?php echo ('Manage Tenants'); ?></legend>
        <?
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3">';
            echo $this->Form->select('Tenants', $tenants, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
            $end = array('label' => 'Edit','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>

</div>