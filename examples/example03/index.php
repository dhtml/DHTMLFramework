<?php
include dirname(dirname(dirname(__FILE__))).'/lib/dhtmlframework.php';

// mapping routes
dispatch('/', 'home'); //this will map the index of your site
dispatch('/users', 'ListAction'); //this will map siteurl/users.php
dispatch('/users/[i:id]','users_show'); //this will map users/1 or users/12
dispatch('/users/[i:id]/[edit|delete|update:action]', 'select_action'); //this will match users/12/delete or users/13/update

//you must create functions for the routes
function home() {
return "Welcome to our home page. Check out the following links.
<ul>
<li><a href='index.php?q=users'>/users</a> (default)</li>
<li><a href='index.php?q=users/10'>/users/10</a> (default)</li>
<li><a href='index.php?q=users/10/edit'>/users/10/edit</a> (default)</li>
<li><a href='index.php?q=users/10/update'>/users/10/update</a> (default)</li>
<li><a href='index.php?q=users/10/delete'>/users/10/delete</a> (default)</li>
<hr width='200' align='left'/>
<li><a href='./users'>/users</a> (htaccess required)</li>
<li><a href='./users/10'>/users/10</a> (htaccess required)</li>
<li><a href='./users/10/edit'>/users/10/edit</a> (htaccess required)</li>
<li><a href='./users/10/update'>/users/10/update</a> (htaccess required)</li>
<li><a href='./users/10/delete'>/users/10/delete</a> (htaccess required)</li>

</ul>
";
}

function ListAction() {
global $base_url;
return "&lt;&lt;<a href='$base_url'>Go Home</a><br/><br/>Select user actions";
}

function users_show() {
global $base_url;
return "&lt;&lt;<a href='$base_url'>Go Home</a><br/><br/>Show users actions of user ".arg(1);
}

function select_action() {
global $base_url;
return "&lt;&lt;<a href='$base_url'>Go Home</a><br/><br/>Select " . arg(2) . " action for user ".arg(1);
}

run();
?>