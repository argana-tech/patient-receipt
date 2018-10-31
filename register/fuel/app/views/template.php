<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<link rel="shortcut icon" href="img/favicon.ico">
<meta name="description" content="">
<meta name="keywords" content="" lang="ja">
<title><?php if (isset($title)): echo $title . '／'; endif ?>患者受付システム</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="<?php echo Uri::create('assets/css/reset.css'); ?>" type="text/css" >
<link rel="stylesheet" href="<?php echo Uri::create('assets/css/stylesheet.css'); ?>?9*9*9*9" type="text/css" >

<script type="text/javascript" src="<?php echo Uri::create('assets/js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo Uri::create('assets/js/script.js'); ?>"></script>
</head>
<body>
 <?php echo $content; ?>
</body>
</html>