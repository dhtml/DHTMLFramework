<?php
//crosslink('style.css', $theme_path.'/style.css','text/css');
?>
<!DOCTYPE html> 
<html lang="en-US"> 
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title><?= $title ?></title>
<link rel="stylesheet" href="<?= phptemplate_url('style.css') ?>">
<link rel="shortcut icon" href="<?= phptemplate_url('favicon.ico') ?>">
</head>

<body>	
<div id="wrapper">

<div id="sidebar">
<ul>
<?= $sidebar ?>
</ul>
</div>
<?= $content ?>

</div>

</body>
</html>