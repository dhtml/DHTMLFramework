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
<h1>Welcome To DHTMLFramework Theme Example</h1>

<div id="ddtabs1" class="basictab">
<ul>
<li><a href="<?= l('/') ?>" rel="sc1" class="<?= print_if('current',sysget('url'),'/') ?>">Home</a></li>
<li><a href="<?= l('dhtml') ?>" rel="sc2" class="<?= print_if('current',sysget('url'),'/dhtml') ?>">DHTML</a></li>
<li><a href="<?= l('css') ?>" rel="sc3" class="<?= print_if('current',sysget('url'),'/css') ?>">CSS</a></li>
<li><a href="<?= l('forums') ?>" class="<?= print_if('current',sysget('url'),'/forums') ?>">Forums</a></li>
<li><a href="<?= l('error') ?>" class="">Error</a></li>
</ul>
</div>
<br/>
<?= $content ?>


</div>
	
</div><!--/wrapper-->
</body>
</html>