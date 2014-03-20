<?php
include dirname(dirname(__FILE__)).'/lib/dhtmlframework.php';

function configure()
{
    $env = $_SERVER['HTTP_HOST'] == "localhost" ? 'ENV_DEVELOPMENT' : 'ENV_PRODUCTION';
	option('env',$env);
	option('site_theme','themes/default');
}


dispatch('/', 'index');
dispatch('/dhtml', 'dhtml');
dispatch('/css', 'css');
dispatch('/forums', 'forums');

dispatch('/404', 'e404');

function index() {
return theme("layout.php",Array('title'=>"Home | Welcome To DHTMLFramework",'content'=>"<h2>Home</h2>Welcome to dhtmlframework. This is an example of how to theme your output"));
}

function dhtml() {
return theme("layout.php",Array('title'=>"DHTML",'content'=>"<h2>DHTML</h2>This framework supports dhtml by default."));
}

function css() {
return theme("layout.php",Array('title'=>"CSS",'content'=>"<h2>CSS</h2>This framework supports cascading stylesheet."));
}

function forums() {
return theme("layout.php",Array('title'=>"Forums",'content'=>"<h2>Forums</h2>Welcome to our forums."));
}

function e404() {
return theme("layout.php",Array('title'=>"Page Not Found",'content'=>"<h2>Error 404</h2>The requested page is not available. The link you followed may be broken, or the page may have been removed."));
}


run();
?>