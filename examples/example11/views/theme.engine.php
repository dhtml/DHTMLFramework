<?php
function phptemplate_init(&$vars) {
crosslink('style.css', $vars[site_theme].'/template.css','text/css');
crosslink('favicon.ico', $vars[site_theme].'/favicon.ico','image/jpeg');
}

function phptemplate_url($url) {
global $base_url;
if(option('rewrite')=='on') {return "{$base_url}/$url";} else {return "{$base_url}?{$url}";}
}
?>