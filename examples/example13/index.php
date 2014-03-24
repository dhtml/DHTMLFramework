<?php
include dirname(dirname(dirname(__FILE__))).'/lib/dhtmlframework.php';

function configure() {
session_start();
    $env = $_SERVER['HTTP_HOST'] == "localhost" ? 'ENV_DEVELOPMENT' : 'ENV_PRODUCTION';
	option('env',$env);
	option('site_theme','themes/default');
	
	option('view',Array('layout.php'));
}


dispatch('/', 'index');
dispatch('/set', '_set');
dispatch('/change', '_change');
dispatch('/reset', '_reset');

function index() {
setvar(Array('title'=>"Home",'content'=>"The Current Value of session var dhtmlframework is <b>".$_SESSION['dhtmlframework']= empty($_SESSION['dhtmlframework']) ? "not yet defined" : $_SESSION['dhtmlframework'] ."</b>." ));
return theme();
}

function _set() {
$_SESSION['dhtmlframework']="Anthony Ogundipe";
setvar(Array('title'=>"Set Session",'content'=>"<h2>Set Session</h2>The current value is now {$_SESSION['dhtmlframework']}. Go back to the homepage to crosscheck"));
return theme();
}

function _change() {
//this handler will change the session and redirect home immediately
$_SESSION['dhtmlframework']="Mr DHTML";
redirect("/");
}

function _reset() {
unset($_SESSION['dhtmlframework']);
setvar(Array('title'=>"Reset Session",'content'=>"<h2>Reset Session</h2>The session variable has been removed. Return to homepage to crosscheck"));
return theme();
}


run();
?>