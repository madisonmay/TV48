<style>

	.centered-content {
		display: block; 
		margin-left: auto; 
		margin-right: auto;
		width: 100%; 
		text-align: center;		
	}

	.d_color {
		color: #eee !important;
	}

	.block-data {
		width: 33%; 
		display: inline-block;
		overflow-y: auto;
	}

	.content-box {
		max-height: 160px; 
		height: 160px; 
		border-radius: 5px; 
		overflow: auto; 
		font-size: 15px; 
		padding-top: 5px; 
		padding-bottom: 5px; 
		margin-right: auto; 
		margin-left: auto;
		font-family: "Courier New", Courier, monospace;
	}

	.data-container {
		width: 100%; 
		float: none; 
		margin-right: auto;
		margin-left: auto; 
		padding: 0; 
		margin: 0;
		height: 162px;
		max-height: 162px;
		padding-bottom: 10px;
	}

	.nv-axislabel {
		fill: #34495e!important;
		dx: -20;
	}

	text {
		fill: #34495e !important;
	}

	.hr-special {
		width: 90%; 
		margin-top: 2px; 
		margin-bottom: 4px; 
		height: 1px; 
		border: none; 
		background-color: #333;
		margin-left: auto;
		margin-right: auto;
	}

	.data-header {
		font-size: 20px;
	}

	@media (max-width:768px) {
		.block-data {
			width: 100%;
			display: block;
		}

		.data-container {
			height: auto;
			max-height: auto;
		}

		.content-box {
			max-height: 500px;
			height: auto;
			margin-bottom: 30px;
		}

		.nv-series text, .nv-series circle {
			display: none;
		}
	}

</style>

<h2 class='centered-content'>
	<?php echo $user['User']['full_name']; ?>
	<a href='/balance_updates/status_report?id=<?php echo $user['User']['id']; ?>'>
		<img src='/img/money.png' style='display: inline-block; cursor: pointer;'>
	</a>
</h2>
<h6 class='centered-content'>
	<?php echo $user['User']['start_date'] . ' - ' . $user['User']['end_date']; ?>
</h6>
<div id='chart' style='height: 350px; width: 85%; display: block; margin-right: auto; margin-left: auto; display: block; float: none;'>
    <svg></svg>
</div>

<div class='data-container'>
	<div class='block-data' style='float: left;'>
		<div class='centered-content content-box'>
			<div class='data-header'> Recent Deposits </div>
			<hr class='hr-special'>
			<?php
				function cmp($a, $b) {
					foreach ($a as $key => $value) {
						foreach ($b as $key => $value) {
							return strtotime($key) < strtotime($key); 
						}
					}
				}
				usort($deposits, 'cmp');

				$max_len = 0;
				foreach ($deposits as $deposit) {
					foreach ($deposit as $date => $delta) {
						$string = $date . number_format($delta, 2);
						if (strlen($string) > $max_len) {
							$max_len = strlen($string);
						}
					}
				}

				foreach ($deposits as $deposit) {
					foreach ($deposit as $date => $delta) {
						$string = $date . number_format($delta, 2);
						echo '<div>' . $date . ':&nbsp;';
						for ($i = 0; $i<$max_len - strlen($string); $i++) {
							echo '&nbsp;';
						}
						echo '€' . number_format($delta, 2) . '</div>';
					}
				}
			?> 
		</div>
	</div>
	<div class='block-data'>
		<div class='centered-content content-box'>
			<div class='data-header'> Energy Usage by Room </div>
			<hr class='hr-special'>
			<?php
				$max_len = 0;
				foreach ($room_wh as $room => $wh) {
					$string = trim($room) . number_format($wh, 0);
					if (strlen($string) > $max_len) {
						$max_len = strlen($string);
					}
				}

				foreach ($room_wh as $room => $wh) {
					$string = trim($room) . number_format($wh, 0);
					echo '<div>' . trim($room) . ':&nbsp;';
					for ($i = 0; $i<$max_len - strlen($string); $i++) {
						echo '&nbsp;';
					}
					echo number_format($wh, 0) . ' Wh</div>';
				}
			?> 
		</div>
	</div>
	<div class='block-data' style='float: right;'>
		<div class='centered-content content-box'>
			<div class='data-header'> Energy Usage by Day</div>
			<hr class='hr-special'>
			<?php

				$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

				$max_len = 0;
				for ($i=0; $i<count($days); $i++) {
					$string = $days[$i] . number_format($day_wh[$i], 0);
					if (strlen($string) > $max_len) {
						$max_len = strlen($string);
					}
				}

				for ($i=0; $i<count($days); $i++) {
					$string = $days[$i] . number_format($day_wh[$i], 0);
					echo '<div>' . $days[$i] . ':&nbsp;';
					$num_spaces = $max_len - strlen($string);
					for ($j=0; $j<$num_spaces; $j++) {
						echo '&nbsp;';
					}
					echo number_format($day_wh[$i], 0) . ' Wh</div>';
				}
			?> 
		</div>
	</div>
</div>

<div style='display: block; position: fixed; padding-bottom: 5px; bottom: 0; left: 0; width: 100%; left: 50%; margin-left: -50%; text-align: center; height: 20px; font-size: 20px; background-color: #333; color: #eee;'>
	<?php 
		echo '<a class="d_color" href="mailto:' . $user['User']['email'] . '">';
		echo  $user['User']['email'] . '</a>';
		echo  ' - ';
		echo '<a class="d_color" href="tel:+' . preg_replace("/[^0-9]/", "", $user['User']['phonenumber']) . '">';
		echo  $user['User']['phonenumber'] . '</a>';
	?>
</div>

<script src='/js/user_chart.js'></script>