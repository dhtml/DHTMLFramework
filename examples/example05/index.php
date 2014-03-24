<?php
include dirname(dirname(dirname(__FILE__))).'/lib/dhtmlframework.php';
function configure() {
    option('site_theme','views');
}
dispatch('/', 'index');

function index() {
setvar(Array('title'=>"Home",'content'=>"<h2>Home Page</h2>Welcome to dhtmlframework."));
return theme('layout.php','template.php');
//return theme('layout.php'); //try this instead of above to see the alternative result
//return theme('template.php'); //you can also try this
}
run();
?>