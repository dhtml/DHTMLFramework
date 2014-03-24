<?php
//echo "Database";
class dbase {
public static $clsName="";
public static $clsPath="";

public function hook_init($clsName,$clsPath) {
self::$clsName=$clsName;
self::$clsPath=$clsPath;

//echo  "My name is ". self::$clsName;
//echo  " and my path is ". self::$clsPath;

//$plugin=getPluginInfo("session");var_dump($plugin);

doLog("Database started.");
}

public function hook_path(&$paths) {
$paths['base_root']="c:\dos";
doLog("Database set paths.");
}

public function hook_controller(&$url) {
doLog ("Database playing with controller - $url");
}

}
?>