<script>
    $(document).ready(function() {
        $("select").selectpicker();
    });
</script>

<div class="rooms form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Room'); ?>
    <fieldset>
        <legend><?php echo ('Add a room'); ?></legend>
        <?php 
            $name = array('class' => 'span3', 'placeholder' => 'Name...', 'label' => '', 'required' => true);
            echo $this->Form->input('name', $name);
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3" required>';
            echo $this->Form->select('Users', $users, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
        ?>
            <div style="margin-right: auto; margin-left: auto; float: none;" class="span3">
            <? 
                echo $this->Form->select('type', array(
                        'dorm' => 'Dorm',
                        'studio' => 'Studio',
                        'public' => 'Public'
                    ),
                    array(
                        'class' => 'span3 select',
                        'label' => ''
                    )
                );
            ?>
            </div>
        <?
            $end = array('label' => 'Add', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>