<div id="content">
<h1>Welcome To DHTMLFramework Theme Example 2 (without .htaccess)</h1>

<!--
print_if('current',sysget('url'),'/')
means
if(sysget('url')=='/') {print('current');}
-->
<div id="ddtabs1" class="basictab">
<ul>
<li><a href="<?= l('/') ?>"      class="<?= print_if('current',sysget('url'),'/') ?>">Home</a></li>
<li><a href="<?= l('dhtml') ?>"  class="<?= print_if('current',sysget('url'),'/dhtml') ?>">DHTML</a></li>
<li><a href="<?= l('css') ?>"    class="<?= print_if('current',sysget('url'),'/css') ?>">CSS</a></li>
<li><a href="<?= l('forums') ?>" class="<?= print_if('current',sysget('url'),'/forums') ?>">Forums</a></li>
<li><a href="<?= l('error') ?>"  class="">Error</a></li>
</ul>
</div>
<br/>
<?= $content ?>
</div>

<?php theme_varexport('sidebar'); ?>
<li>The base_url is <?= $base_url ?>
<li>The base_path is <?= $base_path ?>
<li>The theme_url is <?= $paths[theme_url] ?>
<li>The theme_uri is <?= $theme_uri ?>
<?php theme_varexport(); ?>
