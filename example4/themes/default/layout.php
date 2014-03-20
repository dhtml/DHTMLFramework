<!DOCTYPE html> 
<html lang="en-US"> 
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">

<title><?= $title ?></title>

<style>
	
</style>

<link rel="stylesheet" href="<?= $theme_uri ?>style.css">

<link rel="shortcut icon" href="<?= $theme_uri ?>favicon.ico">

</head>

<body>	
<div id="wrapper">

<div id="content">
<h1>Session Example</h1>

<div id="ddtabs1" class="basictab">
<ul>
<li><a href="<?= l('/') ?>" class="<?= print_if('current',sysget('url'),'') ?>">Home</a></li>
<li><a href="<?= l('set') ?>" class="<?= print_if('current',sysget('url'),'/set') ?>">Set</a></li>
<li><a href="<?= l('change') ?>" >Change & Go Home</a></li>
<li><a href="<?= l('reset') ?>" class="<?= print_if('current',sysget('url'),'/reset') ?>">Reset</a></li>
</ul>
</div>
<br/>
<?= $content ?>


</div>
	
</div><!--/wrapper-->
</body>
</html>