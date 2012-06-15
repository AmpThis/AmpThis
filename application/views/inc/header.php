<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?= $page_title; ?></title>
<? foreach ($css as $stylesheet) : ?>
<link rel="stylesheet" type="text/css" href="<?= $stylesheet['href']; ?>" media="<?= $stylesheet['media']; ?>" />
<? endforeach; ?>
<? foreach($js as $script) : ?>
<script type="text/javascript" src="<?= $script['src']; ?>"></script>
<? endforeach; ?> 
</head>
<body>