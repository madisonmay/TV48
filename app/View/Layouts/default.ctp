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


	<link href='http://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
	<?	
		//boilerplate includes
		echo $this->element('check'); 
		echo $this->Html->script('jquery.min');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('http://code.jquery.com/ui/1.10.2/jquery-ui.min.js');
		echo $this->Html->script('combobox');
		echo $this->Html->css('bootstrap-combined.min.css');
		echo $this->Html->css('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');
		echo $this->Html->css('nv.d3');
		echo $this->Html->css('combobox');
		echo $this->Html->css('flat-ui');
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
</head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style='background-color: #333333; color: #eeeeee;'>
		    <div class="container"> 
		    	<a class="brand" href="#" style='color: #eeeeee'>
		            TV48
		        </a>
		        <ul class="nav">
		            <li class="divider-vertical" style='margin-top: 18px;'></li>
		            <li><a href='light.php' style='color: #eeeeee'>Light</a></li>
		            <li><a href='heat.php' style='color: #eeeeee'>Heat</a></li>
		            <li><a href='power.php' style='color: #eeeeee'>Power</a></li>
		            <li><a href='management.php' style='color: #eeeeee'>Manage</a></li>
		            <li><a href='admin.php' style='color: #eeeeee'>Admin</a></li>
		        </ul>
		    </div>
		</div>
	</div>
	<div style='margin-bottom: 50px;'></div>
	<?php echo $this->fetch('content'); ?>
</body>
</html>
