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
	<?	
		// //boilerplate includes
		// echo $this->element('check'); 
		echo $this->Html->script('jquery.min');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('bootstrap-select');
		echo $this->Html->script('combobox');
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
			$('a').mouseover(function() {
			  $(this).css("color", "#46a546");
			});

			$('a').mouseout(function() {
			  $(this).css("color", "#eeeeee");
			});
		})

	</script>
</head>
<body>
	<!-- Site navigation -->
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style='background-color: #333333; color: #eeeeee;'>
		    <div class="container"> 
		    	<a class="brand" href="/" style='color: #eeeeee'>
		            TV48
		        </a>
		        <ul class="nav">
		            <li class="divider-vertical" style='margin-top: 18px;'></li>
		            <li><a href='/light' style='color: #eeeeee'>Light</a></li>
		            <li><a href='/heat' style='color: #eeeeee'>Heat</a></li>
		            <li><a href='/power' style='color: #eeeeee'>Power</a></li>
                	<?php 
                		if ($this->Session->read('Auth.User')) {
		                	if (in_array('landlord', $this->Session->read('User.roles'))) {
		                		echo "<li><a href='/home/manage' style='color: #eeeeee'>Manage</a></li>";
		                	}
		                	if (in_array('admin', $this->Session->read('User.roles'))) {
		                		echo "<li><a href='/admin' style='color: #eeeeee'>Admin</a></li>";
		                	}                	
                		}
	                ?>
		        </ul>
		        <ul class="nav pull-right">
		        	<?php if (!$this->Session->read('Auth.User')): ?>
			        	<li>
			        		<a href="/users/add" style='color: #eeeeee'>Register</a>
			        	</li>
		        	<?php endif; ?>
    	        	<?php if (!$this->Session->read('Auth.User')): ?>
    		        	<li>
    		        		<a href="/users/login" style='color: #eeeeee'>Login</a>
    		        	</li>
    	        	<?php endif; ?>
    	        	<?php if ($this->Session->read('Auth.User')): ?>
    		        	<li>
    		        		<a href="/users/logout" style='color: #eeeeee'>Logout</a>
    		        	</li>
    	        	<?php endif; ?>
		        </ul>
		    </div>
		</div>
	</div>

	<!-- Container for flash messages -->
	<div style='margin-bottom: 75px;'></div>
	<? if($this->Session->check('Message.flash')): ?>
		<div style='text-align: center;'>
			<button onclick='disappear(this);' class='btn alert <? if ($this->Session->read("flashWarning")) {echo 'btn-danger';} else {echo 'btn-success';} ?>' style='text-align: center; margin-bottom: 10px; padding: 10px;'>
				<?php echo $this->Session->flash(); ?>
			</button>
		</div>
	<? endif; ?>

	<!-- Main content -->
	<?php echo $this->fetch('content'); ?>

	<!-- SQL for debugging purposes -->
	<div style='text-align: center; margin-top: 10px;'>
		<button class='btn' style='text-align: center; margin-bottom: 10px;'>
			<?php echo $this->element('sql_dump'); ?>
		</button>
	</div>
</body>
</html>