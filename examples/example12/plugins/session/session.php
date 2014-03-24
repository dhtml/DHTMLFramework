<?php
class session {
public static $clsName;

public function hook_init() {
doLog ("Session started.");
}

public function hook_route(&$routes) {
doLog ("Session set routes.");
}


}
?>