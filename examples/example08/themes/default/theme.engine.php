<?php
/*
Theme php engine preprocessor
*/
function phptemplate_init(&$vars) {
crosslink('style.css', $vars[site_theme].'/style.css','text/css');
crosslink('favicon.ico', $vars[site_theme].'/favicon.ico','image/jpeg');
}

/*
Use this functionality inside php template-enabled layouts
*/
function phptemplate_url($url) {
global $base_url;
if(option('rewrite')=='on') {return "{$base_url}/$url";} else {return "{$base_url}?{$url}";}
}
?>