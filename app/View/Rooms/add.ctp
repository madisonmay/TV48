<script>
    $(document).ready(function() {

        $("select").selectpicker();

        $('#thin-wrapper').css('display', 'none');
        $('#RoomType').change(function() {
            if ($(this).val() === 'public' || $(this).val() === '') {
                $('#RoomUsers').val('');
                $('#thin-wrapper').css('display', 'none');
            }  else {
                $('#thin-wrapper').css('display', 'block');   
            }
        });
    });
</script>

<div class="rooms form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Room'); ?>
    <fieldset>
        <legend><?php echo ('Add a room'); ?></legend>
        <?php 
            $name = array('class' => 'span3', 'placeholder' => 'Dorm 1', 'label' => 'Name: ', 'required' => true);
            echo $this->Form->input('name', $name);
        ?>
            <div class="span3 select-wrapper">
            <? 
                echo $this->Form->select('type', array(
                        'dorm' => 'Dorm',
                        'studio' => 'Studio',
                        'public' => 'Public'
                    ),
                    array(
                        'class' => 'span3 select',
                        'label' => 'Room type: ',
                        'required' => true
                    )
                );
            ?>
            </div>
        <?
            echo '<div id="thin-wrapper" class="span3 select-wrapper" required>';
            echo $this->Form->select('Users', $users, array('class' => 'span3 select', 'label' => 'User: '));
            echo '</div>';
        ?>
        <?
            $end = array('label' => 'Add', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>