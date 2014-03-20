<?php
include dirname(dirname(__FILE__)).'/lib/dhtmlframework.php';
function configure() {
session_start();
    $env = $_SERVER['HTTP_HOST'] == "localhost" ? 'ENV_DEVELOPMENT' : 'ENV_PRODUCTION';
	option('env',$env);
	option('site_theme','themes/default');
}


dispatch('/', 'index');
dispatch('/set', '_set');
dispatch('/change', '_change');
dispatch('/reset', '_reset');

function index() {
return theme("layout.php",Array('title'=>"Home",'content'=>"The Current Value of session var dhtmlframework is <b>".$_SESSION['dhtmlframework']= empty($_SESSION['dhtmlframework']) ? "not yet defined" : $_SESSION['dhtmlframework'] ."</b>." ));
}

function _set() {
$_SESSION['dhtmlframework']="Anthony Ogundipe";
return theme("layout.php",Array('title'=>"Set Session",'content'=>"<h2>Set Session</h2>The current value is now {$_SESSION['dhtmlframework']}. Go back to the homepage to crosscheck"));
}

function _change() {
//this handler will change the session and redirect home immediately
$_SESSION['dhtmlframework']="Mr DHTML";
redirect("/");
}

function _reset() {
unset($_SESSION['dhtmlframework']);
return theme("layout.php",Array('title'=>"Reset Session",'content'=>"<h2>Reset Session</h2>The session variable has been removed. Return to homepage to crosscheck"));
}


run();
?>