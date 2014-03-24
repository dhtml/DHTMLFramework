<?php
include dirname(dirname(dirname(__FILE__))).'/lib/dhtmlframework.php';

function configure() {
    option('site_theme','views');
    option('view',Array('template.php'));
}
dispatch('/', 'index');

function index() {
setvar(Array('title'=>"Home",'content'=>"<h2>Home Page</h2>Welcome to dhtmlframework."));
return theme();
}
run();
?>