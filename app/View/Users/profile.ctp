<style>

	.centered-content {
		display: block; 
		margin-left: auto; 
		margin-right: auto;
		width: 100%; 
		text-align: center;		
	}

	.d_color {
		color: #34495E !important;
	}

</style>

<h2 class='centered-content'>
	<?php echo $user['User']['full_name']; ?>
</h2>
<h6 class='centered-content'>
	<?php echo $user['User']['start_date'] . ' - ' . $user['User']['end_date']; ?>
</h6>
<div style='display: block; position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center; height: 20px; font-size: 20px;'>
	<?php 
		echo '<a class="d_color" href="mailto:' . $user['User']['email'] . '">';
		echo  $user['User']['email'] . '</a>';
		echo  ' - ';
		echo '<a class="d_color" href="tel:+' . preg_replace("/[^0-9]/", "", $user['User']['phonenumber']) . '">';
		echo  $user['User']['phonenumber'] . '</a>';
	?>
</div>