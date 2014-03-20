<?php
include dirname(dirname(__FILE__)).'/lib/dhtmlframework.php';

dispatch('/', 'index');

function index() {
    return '<p>Welcome to our page.</p>';
}

run();
?>