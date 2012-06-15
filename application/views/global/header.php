<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= SITE_TITLE; ?></title>
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/imgs/favicon.ico" type="image/x-icon" />
		<link rel="icon" href="<?= base_url(); ?>assets/imgs/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/default.css" type="text/css" />
        <link rel="stylesheet" href="<?= base_url(); ?>assets/css/format.css" type="text/css" />
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.ui.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.illuminate.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/gluco.tabs.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/selectbox.jquery.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/easypaginate.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.countdown.js"></script>
        <script type="text/javascript">
			function serverTime() { 
				var time = null; 
				$.ajax({url: '<?= base_url(); ?>admin/server_time', 
					async: false, dataType: 'text', 
					success: function(text) { 
						time = new Date(text); 
					}, error: function(http, message, exc) { 
						time = new Date(); 
				}}); 
				return time; 
			}		
		</script>
    </head>
    <body>
    <div id="header" class="SHORT_SHADOWS">
        <div class="WRAP">
            <h1 class="title" title="<?= base_url(); ?>"><span>Glucommander&trade;</span></h1>
            <div id="nav">
            	<ul>
                	<?php foreach($nav as $item): ?>
                    	<li class="<?= $item->class; ?>"><a href="<?= base_url() . $item->href; ?>"><?= $item->name; ?></a></li>
                    <? endforeach; ?>
                </ul>
            </div>
            <div class="CLEARFIX"></div>
        </div>
    </div>
