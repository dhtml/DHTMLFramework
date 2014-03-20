## Introduction

DHTMLExtreme Framework is a PHP Micro framework for developing web applications. 
This framework is simple to use and it works on all PHP servers, and can be used to 
create multiple websites in a single installation as long as the websites reside on the same host.

It's created and developed by Anthony Ogundipe, CEO of [DHTMLExtreme](http://www.dhtmlextreme.net).


## Features

* Web apps written in modern HTML5, CSS3, JS, PHP and MySQL.
* Compatible with PHP5+
* Good performance on Linux, Windows and Mac OSX servers.
* Easy to package and distribute apps.
* Supports installation of new libraries and modules
* It is very easy to extend and customize


## Quick Start
* Download the [zip master](https://github.com/dhtml/dhtmlframework/archive/master.zip)
* Extract the zip master into your web directory
* Open the examples folder to view the packaged usage examples


Create `index.php`:
```
<?php
require 'lib/dhtmlframework.php';
dispatch('/', 'index');
function index() {
    return '<p>Welcome to our page.</p>';
}
run();
?>
```


## Documents

For more information on how to use this framework to create web applications, see:

* our [Website](http://dhtmlframework.com) for much more.
* our [Wiki](https://github.com/dhtml/dhtmlframework/wiki) for much more.

## Community

We use the [dhtmlframework group](https://groups.google.com/forum/#!forum/dhtmlframework) for issues that are being tracked here on GitHub.

You can chat with us on facebook http://facebook.com/dhtml5 

## License

`dhtmlframework`'s code in this repo uses the MIT license, see our `LICENSE` file.
