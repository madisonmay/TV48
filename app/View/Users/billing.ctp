<?php 
	foreach ($users as $user) {
		echo '<div style="font-size: 30px; text-align: ; margin-left: auto;"> <b style="display: inline-block; text-align: right; width: 50%;">';
		echo $user['User']['full_name'] . ' :</b> ';
		echo '<div style="display: inline-block; text-align: right; width: 180px;">';
		echo number_format($user['User']['balance'], 2);
		echo ' EUR </div></div>';
		echo '<br>';
	}
?>