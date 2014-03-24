<?php
include dirname(dirname(dirname(__FILE__))).'/lib/dhtmlframework.php';

function configure()
{
    $env = $_SERVER['HTTP_HOST'] == "localhost" ? 'ENV_DEVELOPMENT' : 'ENV_PRODUCTION';
	option('env',$env);
	option('site_theme','themes/default');	
	option('view',Array('layout.php'));
	option('404','e404');

	option('ext_dir','plugins');
	option('ext',Array('dbase','session'));
	
	addMatchTypes(array('cId' => '[a-zA-Z]{2}[0-9](?:_[0-9]++)?'));
	addMatchTypes(array('cIe' => '[a-z]{2}[0-5](?:_[0-5]++)?'));
}


dispatch('/', 'index');
dispatch('/dhtml', 'dhtml');
dispatch('/css', 'css');
dispatch('/forums', 'forums');

crosslink('sample.css', 'plugins/dbase/demo.css','text/plain');
crosslink('sample2.css', 'samplecss','text/css','attachment');

function samplecss() {
$content="body {background:green;}
h2 {color:maroon;}";
return $content;
}


function index() {
setvar(Array('title'=>"Home | Welcome To DHTMLFramework",'content'=>"<h2>Home</h2>Welcome to dhtmlframework. This is an example of how to theme your output"));
return theme();
}

function dhtml() {
setvar(Array('title'=>"DHTML",'content'=>"<h2>DHTML</h2>This framework supports dhtml by default."));
return theme();
}

function css() {
setvar(Array('title'=>"CSS",'content'=>"<h2>CSS</h2>This framework supports cascading stylesheet."));
return theme();
}

function forums() {
setvar(Array('title'=>"Forums",'content'=>"<h2>Forums</h2>Welcome to our forums."));
return theme();
}

function e404() {
setvar(Array('title'=>"Page Not Found",'content'=>"<h2>Error 404</h2>The requested page is not available. The link you followed may be broken, or the page may have been removed."));
return theme();
}


run();
?>