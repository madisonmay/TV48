<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>

<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>

	<!--[if lt IE 9]>
	    <style>

	        .hide-this {
	            display: none;
	        }

	    </style>
	<![endif]-->

	<!-- Should eventually be moved to local file -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
	<script type='text/javascript' src='https://cdn.firebase.com/v0/firebase.js'></script>
	<?	
		// Some of these scripts should likewise be downloaded and integrated before release

		echo $this->Html->script('jquery.min');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('bootstrap-select');
		echo $this->Html->script('global');
		echo $this->Html->script("//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js");
		echo $this->Html->css('bootstrap-combined.min.css');
		echo $this->Html->css('flat-ui');
		echo $this->Html->css('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/smoothness/jquery-ui.css');
		echo $this->Html->css('nv.d3');
		echo $this->Html->css('combobox');
		echo $this->Html->css('style');

		//Include page specific css and js files
		if(isset($cssIncludes)){
		    foreach($cssIncludes as $css){
		        echo $this->Html->css($css);
		    }
		}

		if(isset($jsIncludes)){
		    foreach($jsIncludes as $js){
		        echo $this->Html->script($js);
		    }
		}
	?>

	<script>
		function disappear(btn) {
			$(btn).stop(true).animate({'opacity': '0', '-moz-opacity': '0', 'filter': 'Alpha(Opacity=0)',
		               					'-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=0}"'}, 500);
		    setTimeout(function() {$(btn).stop(true).animate({'height': '0px', 'padding': '0px'}, 100);}, 1200);
		}

		$(document).ready(function() {

			$('.select').css('margin-left: 150px !important');

			$('a').mouseover(function() {
			  $(this).css("color", "#46a546");
			});

			$('a').mouseout(function() {
			  $(this).css("color", "#eeeeee");
			});

			//focus on the center -- kind of a hack, and should be changed
			scrollTo(($(document).width() - $(window).width()) / 2, 0);
		})

	</script>
	<script>

		$(document).ready(function() {
			if ($(window).innerWidth() < 800) {
				$('.brand').css('margin-left', '15px');
			}
		});

		$(window).resize(function() {
			if ($(window).innerWidth() < 800) {
				$('.brand').css('margin-left', '15px');
			}
		});
	</script>

	<!--[if lte IE 9]>

		<script>
			function IE() {
				if (Function('/*@cc_on return document.documentMode===10@*/')()){
					if ($(window).innerWidth() < 1000) {
						$('#footer-tab').animate({'bottom': '+=15'}, 100);
						$('#footer').animate({'bottom': '+=15'}, 100);
						$('#input').animate({'bottom': '+=15'}, 100);
					}
				}
			}

			$(document).ready(function() {
				IE();
			})

		</script>
	<![endif]-->
	<script>
		$(document).ready(function() {

			// not at all ideal, but I couldn't find a work around.  The select picker looks great,
			// but it sure does cause a lot of trouble.
			setTimeout(function() {
				if ($('.select').attr('label') && $(window).width() >= 979) {
					$('.select').css({'margin-left': '150px', 'margin-top': '-35px', 'margin-bottom': '15px'});
					$('div.select').css('opacity', '1');
					$('select.select').each(function(index, value) {
						var new_label_start = '<label style="margin-bottom: 5px; margin-top: 5px;" for="' + $(value).attr('id') + '">';
						var new_label_end = $(value).attr('label') + '</label>';
						$(value).before(new_label_start + new_label_end);
					})
				} else if ($('.select').attr('label') && $(window).width() < 979) {
					$('.select').css('margin-top', '-20px');
					$('div.select').css('opacity', '1');
					$('select.select').each(function(index, value) {
						var new_label_start = '<label style="margin-bottom: 5px; margin-top: 5px;" for="' + $(value).attr('id') + '">';
						var new_label_end = $(value).attr('label') + '</label>';
						$(value).before(new_label_start + new_label_end);
					})
				} else {
					$('.select').css('opacity', '1');
				}
			}, 10);
		});
	</script>


</head>
<body>

	<div class="navbar navbar-fixed-top">
	  <div class="navbar-inner" style='background-color: #333333; color: #eeeeee;'>
	    <div class="container">
	      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="brand" href="/" style='color: #eeeeee'>
	        TV48
	      </a>
	      <div class="nav-collapse collapse">
	      	<ul class="nav">
            	<?php 
            		if ($this->Session->read('Auth.User')) {
            			echo "<li class='divider-vertical' style='margin-top: 18px;'></li>";
            			echo "<li><a href='/sensors/lighting' style='color: #eeeeee; text-align: center;'>Light</a></li>";
	            		echo "<li><a href='/sensors/heating' style='color: #eeeeee; text-align: center;'>Heat</a></li>";
	            		echo "<li><a href='/sensors/electricity_select' style='color: #eeeeee; text-align: center;'>Power</a></li>";
	            		if (in_array('landlord', $this->Session->read('User.roles'))) {
	            			echo "<li><a href='/users/profiles' style='color: #eeeeee; text-align: center;'>Tenants</a></li>";
	            		}
	                	if (in_array('landlord', $this->Session->read('User.roles'))) {
	                		echo "<li><a href='/home/manage' style='color: #eeeeee; text-align: center;'>Manage</a></li>";
	                	}
	                	if (in_array('admin', $this->Session->read('User.roles'))) {
	                		echo "<li><a href='/sensors' style='color: #eeeeee; text-align: center;'>Admin</a></li>";
	                	}  
	                	if (in_array('tenant', $this->Session->read('User.roles'))) {
	                		echo "<li><a href='/users/profile?id=" . $this->Session->read('Auth.User.id');
	                		echo "' style='color: #eeeeee; text-align: center;'>Profile</a></li>";
	                	}                   	
            		}
                ?>
	        </ul>
	        <ul class="nav pull-right">
        	  <?php if (!$this->Session->read('Auth.User')): ?>
	          	<li>
	              <a id='register' href="/users/add" style='color: #eeeeee; text-align: center; width: 60px;'>Register</a>
	        	</li>
        	  <?php endif; ?>
        	  <?php if (!$this->Session->read('Auth.User')): ?>
	        	<li>
	              <a id='login' href="/users/login" style='color: #eeeeee; text-align: center; width: 60px;'>Login</a>
	        	</li>
        	  <?php endif; ?>
        	  <?php if ($this->Session->read('Auth.User')): ?>
	        	<li>
	        	  <a id='logout' href="/users/logout" style='color: #eeeeee; text-align: center; width: 60px;'>Logout</a>
	        	</li>
        	  <?php endif; ?>
            </ul>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- Container for flash messages -->
	<div style='margin-bottom: 75px;'></div>
	<? if($this->Session->check('Message.flash')): ?>
		<div style='text-align: center;'>
			<button onclick='disappear(this);' class='btn alert <? if ($this->Session->read("flashWarning")) {echo 'btn-danger';} else {echo 'btn-success';} ?>' style='text-align: center; color: #ffffff; margin-bottom: 10px; padding: 10px;'>
				<?php echo $this->Session->flash(); ?>
			</button>
		</div>
	<? endif; ?>

	<!-- Main content -->
	<?php echo $this->fetch('content'); ?>

	<!-- SQL for debugging purposes -->
<!-- 	<div style='text-align: center; margin-top: 10px;'>
		<button class='btn' style='text-align: center; margin-bottom: 10px;'>
			<?php echo $this->element('sql_dump'); ?>
		</button>
	</div> -->
</body>
</html>