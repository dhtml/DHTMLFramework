<?php                                                                  
# ============================================================================ #

/**
 *  D H T M L F R A M E W O R K
 * 
 *  a PHP micro framework.
 * 
 *  For more informations: {@link https://github.com/dhtml/dhtmlframework}
 *  
 *  @author Anthony Ogundipe
 *  @copyright Copyright (c) 2014 Anthony Ogundipe
 *  @license http://opensource.org/licenses/mit-license.php The MIT License
 *  @package dhtmlframework
 */

#   -----------------------------------------------------------------------    #
#    Copyright (c) 2014 Anthony Ogundipe                                        #
#                                                                              #
#    Permission is hereby granted, free of charge, to any person               #
#    obtaining a copy of this software and associated documentation            #
#    files (the "Software"), to deal in the Software without                   #
#    restriction, including without limitation the rights to use,              #
#    copy, modify, merge, publish, distribute, sublicense, and/or sell         #
#    copies of the Software, and to permit persons to whom the                 #
#    Software is furnished to do so, subject to the following                  #
#    conditions:                                                               #
#                                                                              #
#    The above copyright notice and this permission notice shall be            #
#    included in all copies or substantial portions of the Software.           #
#                                                                              #
#    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,           #
#    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES           #
#    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND                  #
#    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT               #
#    HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,              #
#    WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING              #
#    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR             #
#    OTHER DEALINGS IN THE SOFTWARE.                                           #
# ============================================================================ # 

define('DHTMLFRAMEWORK', '1.0.0');
define('DF_START_MICROTIME',   microtime(true));
define('CONTROLLER','default');
define('DS', DIRECTORY_SEPARATOR );
define('LPATH',str_replace('/',DIRECTORY_SEPARATOR,dirname($_SERVER['SCRIPT_FILENAME']))); //local path of site root directory 



function unregister_globals()
{
  $args = func_get_args();
  foreach($args as $k => $v)
    if(array_key_exists($k, $GLOBALS)) unset($GLOBALS[$k]);
}

if(ini_get('register_globals'))
{
  unregister_globals( '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', 
                      '_ENV', '_FILES');
  ini_set('register_globals', 0);
}

function remove_magic_quotes($array)
{
  foreach ($array as $k => $v)
    $array[$k] = is_array($v) ? remove_magic_quotes($v) : stripslashes($v);
  return $array;
}

if (get_magic_quotes_gpc())
{
  $_GET    = remove_magic_quotes($_GET);
  $_POST   = remove_magic_quotes($_POST);
  $_COOKIE = remove_magic_quotes($_COOKIE);
  ini_set('magic_quotes_gpc', 0);
}

if(function_exists('set_magic_quotes_runtime') && get_magic_quotes_runtime()) set_magic_quotes_runtime(false);


//framework starts
function dispatch($path,$handler) {
global $routes;
$routes["$path"]=array( 
            'request' => $path, 
            'action' => $handler, 
        );
}

//retrieve arguments
function arg($pos=null) {
global $arguments;
if (is_null($pos)) {return $arguments;}
else if(isset($arguments[$pos])) {return $arguments[$pos];}
else {return "";}
}

function setparam($name,$value) {
global $params;
$params[$name]=$value;
}

function theme($template,$data) {
global $theme_url,$theme_path,$base_url,$site_theme;

$uri=$site_theme.'/'.$template;
$local=lpath($uri);
$theme_uri=rpath($site_theme).'/';
if(!file_exists($local)) {$results=("Theme not initialized");}
else {
if(is_array($data)) {extract($data);}
ob_start();
include $local;
$results=ob_get_contents();
ob_end_clean();
}
return utf8_encode($results);
}

function redirect($path,$resolve=true) {
	if($resolve) {$path=l($path);}
	header("Location:$path");
	exit();
}


function option($name = null, $values = null)
{
  static $options = array();
  $args = func_get_args();
  $name = array_shift($args);
  if(is_null($name)) return $options;
  if(!empty($args))
  {
    $options[$name] = count($args) > 1 ? $args : $args[0];
  }
  if(array_key_exists($name, $options)) return $options[$name];
  return;
}




//evaluate a function
function eval_func($func,$data='') {
if (function_exists("{$func}")) {
@$ret=eval("return $func('$data');");
}
return $ret; 
}

function sysget($data) {
global $datapack;
//d($datapack);
return $datapack[$data];
}

function print_if($print,$cond1,$cond2) {
if($cond1==$cond2) {return $print;}
}

function run() {
eval_func('configure'); //call configure if it exists
conf_init(); //initialize paths

global $routes,$params,$paths,$url,$datapack,$req,$arguments;
$req[]=array_merge(array_values($routes),Array('controller'=>CONTROLLER,'param'=>$params));	

Route::$request[]=$req[0];

$url=$_GET['q'];
//die($url);
if($url=="" && !empty($_SERVER['QUERY_STRING'])) {$url=$_SERVER['QUERY_STRING'];}

//create arguments array
$arguments=explode('/',$url);

if(!empty($url)) {$url="/$url";} //current url


$datapack=Array('url'=>$url,'requests'=>$req,'globals'=>$paths);

$route=Route::matchURI("$url");

if(!empty($route)) {
$cb=$route["action"];
 if(!function_exists($cb)) {$content="Please check router config";} else {$content=eval_func($cb,$url);}
} else {
//404 error page
$route=Route::matchURI('/404');

if(!empty($route)) {
$cb=$route["action"];
 if(!function_exists($cb)) {$content="Please check router config";} else {$content=eval_func($cb,$url);}
} else {die("Please define 404 handler");}

}

echo $content;
}



//library starts
    Class Route 
    { 
        //TRAP REQUESTS ARRAY: 
        public static $request; 
        private static $param; 


        public static function matchURI($uri = null) { 
            $uri = (!$uri) ? $_SERVER['PATH_INFO'] : $uri; 
            $uri = (!$uri) ? '/' : rtrim($uri,"\/"); 
            if(!empty(self::$request)) { 
                $count=count(self::$request); 
                for($i=0; $i<$count; ++$i) { 
                    foreach(self::$request[$i] as $k => $v) { 
                        if (is_array($v) and $k !== 'param') { 
                            self::$param = self::$request[$i]['param']; 
                            $v['request'] = preg_replace_callback("/\<(?<key>[0-9a-z_]+)\>/", 
                                'Route::_replacer', 
                                str_replace(")",")?", $v['request']) 
                            ); 
                            $rulleTemp = array_merge((array)self::$request[$i], (array)$v); 
                            if(($t = self::_reportRulle($rulleTemp, $uri))) 
                                return $t; 
                        } 
                    } 
                } 

            } else return array(); 
        } 

        private static function _replacer($matches) { 
            if(isset(self::$param[$matches['key']])) { 
                return "(?<".$matches['key'].">".self::$param[$matches['key']].")"; 
            } else return "(?<".$matches['key'].">"."([^/]+)".")"; 
        } 

        private static function _reportRulle($ini_array, $uri) { 
			if(is_array($ini_array) and $uri) { 
                if(@preg_match("#^".$ini_array['request']."$#", $uri, $match)){ 
                    $r = array_merge((array)$ini_array, (array)$match); 
                    foreach($r as $k => $v) 
                        if((int)$k OR $k == 'param' OR $k == 'request') 
                            unset($r[$k]); 
                    return $r; 
                } 
            } 
        } 
        /** =================================================================== **/ 
    }

//trace path start	
global $paths,$base_url,$browser_path_complete, $base_path, $base_root,$browser_path_full,$browser_full_path,$browser_path,$browser_url,$theme_url,$theme_path,$site_theme;

function conf_init() {
global $paths,$base_url,$browser_path_complete, $base_path, $base_root,$browser_path_full,$browser_full_path,$browser_path,$browser_url,$theme_path,$site_theme,$theme_url,$theme_path;

  if (isset($_SERVER['HTTP_HOST'])) {
    // As HTTP_HOST is user input, ensure it only contains characters allowed
    // in hostnames. See RFC 952 (and RFC 2181).
    // $_SERVER['HTTP_HOST'] is lowercased here per specifications.
    $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
    if (!get_valid_http_host($_SERVER['HTTP_HOST'])) {
      // HTTP_HOST is invalid, e.g. if containing slashes it may be an attack.
      header('HTTP/1.1 400 Bad Request');
      exit;
    }
  }
  else {
    // Some pre-HTTP/1.1 clients will not send a Host header. Ensure the key is
    // defined for E_ALL compliance.
    $_SERVER['HTTP_HOST'] = '';
  }


  if (isset($base_url)) {
    // Parse fixed base URL from settings.php.
    $parts = parse_url($base_url);
    if (!isset($parts['path'])) {
      $parts['path'] = '';
    }
    $base_path = $parts['path'] .'/';
    // Build $base_root (everything until first slash after "scheme://").
    $base_root = substr($base_url, 0, strlen($base_url) - strlen($parts['path']));
  }
  else {
    // Create base URL
    $base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

    $base_url = $base_root .= '://'. $_SERVER['HTTP_HOST'];

    // $_SERVER['SCRIPT_NAME'] can, in contrast to $_SERVER['PHP_SELF'], not
    // be modified by a visitor.
$dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/');
$dir2=LPATH;

$dir=str_replace('\\','/',$dir);
$dir2=str_replace('\\','/',$dir2);

$uri=explode('/',$dir);
$uri2=explode('/',$dir2);
//echo $s;
$pos=-1;
while (list(, $val) = each($uri2)) {$pos++;
if($val==$uri[0]) {break;}
}
$uri2 = array_slice($uri2,$pos);   

$uri=implode('/',$uri2);
$dir=$uri;
    if ($dir) {
      $base_path = "/$dir";
      $base_url .= $base_path;
      $base_path .= '/';
    }
    else {
      $base_path = '/';
    }
  }

$site_theme=option('site_theme');

if(empty($site_theme)) {$site_theme="default";}
  
$browser_path_complete=base_path_url($_SERVER['REQUEST_URI']);
//$browser_path_complete= $base_url . $_SERVER['QUERY_STRING']=="" ? "" : $_SERVER['QUERY_STRING'];
$browser_path_full=$browser_path_complete;
$browser_full_path=$browser_path_complete;
$browser_path=$browser_path_complete;
$browser_url=$browser_path_complete;
$theme_url=$base_url.'/'.$site_theme;
$theme_path=$base_path.$site_theme;

$paths['base_url']=$base_url;
$paths['browser_path_complete']=$browser_path_complete;
$paths['base_path']=$base_path;
$paths['base_root']=$base_root;
$paths['browser_path_full']=$browser_path_full;
$paths['browser_full_path']=$browser_full_path;
$paths['browser_path']=$browser_path;
$paths['browser_url']=$browser_url;
$paths['theme_url']=$theme_url;
$paths['theme_path']=$theme_path;
$paths['site_theme']=$site_theme;

if(rewrite_on()) {option('rewrite','on');}
}


//evaluates the local path of string e.g. "images/gallery/web/home.jpg";
function lpath($path) {
$path=str_replace(Array('../','./'),'',$path);

if(strpos($path,LPATH)===false) {
$path=LPATH.DS.$path;
}

$dir=str_replace('\\','/',$path);
$uri=explode('/',$dir);
$uri=implode(DS,$uri);
return $uri;	
}


//evaluates the remote path of string e.g. "images/gallery/web/home.jpg";
function rpath($path) {
global $base_url;

$path=str_replace(Array('../','./'),'',$path);




if(strpos($path,$base_url)===false) {
$path=$base_url.'/'.$path;
}

$dir=str_replace('\\','/',$path);
$uri=explode('/',$dir);
$uri=implode('/',$uri);
return $uri;	
}

//evaluates the remote path of string e.g. "images/gallery/web/home.jpg";
function rpath2($path) {
global $base_url;

$path=str_replace(Array('../','./'),'',$path);

$path=str_replace(Array('?',"{$base_url}/","{$base_url}"),Array('&','',''),$path);


if(strpos($path,$base_url)===false) {
$path=$base_url.'/?q='.$path;
} else {
$path=$path;
}

$dir=str_replace('\\','/',$path);
$uri=explode('/',$dir);
$uri=implode('/',$uri);
return $uri;	
}

function bpath($url) {return rpath($path);}



function apache_rewrite_on() {
if(!function_exists('apache_get_modules')) {return -1;} //
if(in_array('mod_rewrite',apache_get_modules())) {return 1;} else {return 0;}

if(function_exists('apache_get_modules'))  $r=apache_get_modules();

}

function rewrite_on() {
if(file_exists(LPATH.DS.'.htaccess')) {return true;}
else if(file_exists(LPATH.DS.'web.config')) {return true;}
else {return false;}
}

//return the right path for a link e.g home
function l($link) {
global $settings,$base_url;

switch($link) {
case "":	
case "/":	
case "index.php":	
return $base_url;
break;	
}

if(option('rewrite')=="on") {$link=rpath($link);} else {$link=rpath2($link);}	
return $link;	
}


//shows system information
function sysinfo() {
global $datapack;
if(!empty($datapack)) {
d($datapack);
}
}

function get_valid_http_host($host) {
  return preg_match('/^\[?(?:[a-z0-9-:\]_]+\.?)+$/', $host);
}




//remove base path and replace with base_url
function base_path_url($path) {
global $base_path,$base_url;

if(preg_match('/http/',$path)) {return $path;}

$path2=dhtml_strip_base($path);
$path=$base_url . '/' . $path2;

return $path;
}

function dhtml_strip_base($path) {
global $base_path,$base_url;

$baselen=strlen($base_path);
if(substr($path,0,$baselen)==$base_path) {
$path=substr($path,$baselen);
}

return $path;
}

function base_url_path($path) {
global $base_path,$base_url;

if(preg_match('/http/',$path)) {return $path;}

if(substr_count($path,$base_path)>0) {return $path;}

$path=str_replace("$base_url/","$base_path",$path);
return $path;
}

//trace path stop
	
//xdump start

function xdump($arr,$stop=false) {
	$arr=(array) $arr; 
	d($arr);
	if($stop) {die();}
}

if(!isset($GLOBALS['DD']))                  	$GLOBALS['DD']                  	= array();
if(!isset($GLOBALS['DD']['download']))        $GLOBALS['DD']['download']        = 0;
if(!isset($GLOBALS['DD']['wordwrap']))        $GLOBALS['DD']['wordwrap']        = 0;
if(!isset($GLOBALS['DD']['headers']))         $GLOBALS['DD']['headers']         = 0;
if(!isset($GLOBALS['DD']['logfile']))         $GLOBALS['DD']['logfile']         = '.#dlog.htm';
if(!isset($GLOBALS['DD']['maxlogfilesize']))  $GLOBALS['DD']['maxlogfilesize']  = 500000;
if(!isset($GLOBALS['DD']['maxstringforhex'])) $GLOBALS['DD']['maxstringforhex'] = 50;
if(!isset($GLOBALS['DD']['watchcookie']))     $GLOBALS['DD']['watchcookie']     = 0;
if(!isset($GLOBALS['DD']['init']))            $GLOBALS['DD']['init']            = 0;
if(!isset($GLOBALS['DD']['debug']['func']))   $GLOBALS['DD']['debug']['func']   = '';

#########################################################################
#########################################################################
#########################################################################
/*
 * dd - DataDumper - Dump any resource with syntax highlighting, 
 *      indenting and variable type information to the screen in a very intuitive format
 *
 * based on Dumpr and on dBug
 *
 * Licensed under the terms of the GNU Lesser General Public License:
 *      http://www.opensource.org/licenses/lgpl-license.php
 *
 * Author    Emile Schenk
 *           https://sourceforge.net/projects/datadumper
 * License   LGPL
 * Modified  July 2012
 * Revision  4.1
 */

function dd() {
	d('Please use the new functionname: d()');
}

function d() {
	if($GLOBALS['DD']['watchcookie']!=1 || $_COOKIE['ddphp']==1) {
		new dBug( func_get_args() );
	}
}

function dc() {
	if($_COOKIE['ddphp'] == 1) {
		new dBug( func_get_args() );
	}
}

function dcookie() {
	$GLOBALS['DD']['watchcookie'] = 1;
}

function de() {
	if($GLOBALS['DD']['watchcookie']!=1 || $_COOKIE['ddphp']==1) {
		new dBug( func_get_args() );
		exit;
	}
}

function df() {
	if($GLOBALS['DD']['watchcookie']!=1 || $_COOKIE['ddphp']==1) {
		$data=func_get_args();
		$data['file']=1;
		new dBug($data);
	}
}

#############################################
class dBug {
	var $xmlDepth=array();
	var $xmlCData;
	var $xmlSData;
	var $xmlDData;
	var $xmlCount=0;
	var $xmlAttrib;
	var $xmlName;
	var $arrType=array("array","object","resource");
	var $arraydim = 0;
	var $bInitialized = false;
	var $arrHistory = array();
	var $logfile='_dlog.htm';

	//constructor
	function dBug($var) {
		if(isset($var['file'])){
			$this->log2file=$var['file'];
		}
		$this->getVariableName();

		if(substr_count($_SERVER['SERVER_PROTOCOL'], 'HTTP') == 0) {
			echo "----- $this->file Line $this->line -----\n";
			echo "----- $this->varnameOnly -----\n";
			var_dump($var);
			echo "----------------------------------------\n";
			return;
		}
		
		if(isset($GLOBALS['DD']['logfile'])) $this->logfile = $GLOBALS['DD']['logfile'];
		unset($var['file']);
		if(count($var)==0) {
			if(isset($GLOBALS['DD']['debug'])) {
				if(is_callable($GLOBALS['DD']['debug']['func'])) {
					$GLOBALS['DD']['debug']['func']($GLOBALS['DD']['debug']);
				}
				$datax = array($this->varname."\$GLOBALS['DD']['debug']" => $GLOBALS['DD']['debug']);
			}
			else {
				$datax = array($this->varname.'$_SESSION' => $_SESSION, $this->varname.'$_POST' => $_POST);
			}
		}
		else {
			$datax = array($this->varname => $var[0]);
		}
	
/*
		if($this->log2file ==1) {
			if(file_exists($this->logfile)) {
				$stat = stat($this->logfile);
				if($stat[size] < $DD['maxlogfilesize']) $cont = file_get_contents($this->logfile);
				$cont_a=explode('#~#~#~', $cont);
				$now=mktime();
				$this->logdata = $cont_a[0];
				for($i=1; $i<count($cont_a); $i+=2) {
					if($cont_a[$i] > ($now-7200)) {
						$this->logdata .= "#~#~#~$cont_a[$i]#~#~#~\n".$cont_a[$i+1]."\n";
					}
				}
			}
			else {
				$this->logdata = $this->initJSandCSS();
			}
		}
*/
		
		foreach($datax as $i => $d) {
			$this->bInitialized = false;
			$this->varname = $i;
			$this->dBug2($d);
		}
		
		if(isset($this->log2file)){
			if($this->log2file ==1) {
				if(file_exists($this->logfile)) {
					$logfilesize = filesize($this->logfile);
					if($logfilesize > 500000) {
						$dd = date("ymd_Hi");
						$tmp = explode('.', $this->logfile);
						$tmp[ count($tmp)-2 ] .= "_$dd";
						$bakname = implode('.', $tmp);
						$ret = rename($this->logfile, $bakname);
						$this->out = $this->initJSandCSS() . "\n" . $this->out;
					}
				}
				else {
					$this->out = $this->initJSandCSS() . "\n" . $this->out;
				}
				
				
				if(error_log($this->out, 3, $this->logfile) === false) {
					echo "<p>Logfile cannot be written: {$this->logfile}</p>";
				}
			}
			else {
			//include js and css scripts
				if($GLOBALS['DD']['init'] == 0) {
					$GLOBALS['DD']['init'] = 1;
					$this->out = $this->initJSandCSS() . $this->out;
				}
				echo $this->out;
			}
		}else{
			//include js and css scripts
			if($GLOBALS['DD']['init'] == 0) {
				$GLOBALS['DD']['init'] = 1;
				$this->out = $this->initJSandCSS() . $this->out;
			}
			echo $this->out;
		}
	}

###################################
	function dBug2($var) {
		$arrAccept=array("array","object","xml"); //array of variable types that can be "forced"
		
		if(isset($forceType)){
			if(in_array($forceType,$arrAccept))
				$this->{"varIs".ucfirst($forceType)}($var);
			else
				$this->checkType($var);
		}else{
			$this->checkType($var);
		}
	}

###################################
	function ddserialize($var) {
		try {
			$var_ser = serialize($var);
		}
		catch(Exception $e) {
			if(is_object($var)) {
				$class = get_class($var);
			}
			$var_ser = "Cannot serialize variable of class $class (dummy number " . rand() . ')';
		}
		return $var_ser;
	}

###################################
	//get variable name
	function getVariableName() {
		$arrBacktrace = debug_backtrace();

		//possible 'included' functions
		$arrInclude = array("include","include_once","require","require_once");
		
		//check for any included/required files. if found, get array of the last included file (they contain the right line numbers)
		for($i=count($arrBacktrace)-1; $i>=0; $i--) {
			$arrCurrent = $arrBacktrace[$i];
			if(array_key_exists("function", $arrCurrent) && 
				(in_array($arrCurrent["function"], $arrInclude) || ($arrCurrent["function"] != "d" && $arrCurrent["function"] != "dc" && $arrCurrent["function"] != "de" && $arrCurrent["function"] != "df")))
				continue;

			$arrFile = $arrCurrent;
			
			break;
		}
		
		$arrLines = file($arrFile["file"]);
		$code = $arrLines[($arrFile["line"]-1)];
		//find call to dBug class
		preg_match('/\bde{0,1}f{0,1}c{0,1}\s*\(\s*(.+)\s*\);/i', $code, $arrMatches);
#dd($arrBacktrace);		
		$this->varname = $arrMatches[1];
		$this->varnameOnly = str_replace("'", '', $this->varname);
		if(isset($this->log2file)){
			if($this->log2file ==1) {
					$url_time = (isset($_SERVER[HTTPS])) ? 'https' : 'http';
					$url_time .= "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI] &nbsp;- &nbsp;".date("d.m.y H:i:s").'<br>';
					$this->varname = "<span style='font-weight:normal;'>$url_time</span>{$this->varname}";
			}
		}
		$this->file = str_replace('\\', '/', $arrFile['file']);
		$this->line = $arrFile['line'];
	}
	
	//create the main table header
	function makeTableHeader($type,$header,$colspan=2) {
		if(!$this->bInitialized) {
			$header = "{$this->varname} ($header), <span style='font-weight:normal;'>{$this->file} - line {$this->line}</span>";
			$this->bInitialized = true;
		}
		else {
			$header = "{$this->name} ($header)";
		}
		$this->out .= "\n\n<table cellspacing=1 cellpadding=1 class=\"dBug_".$type."\"><tr>\n<td class=\"dBug_".$type."Header\" colspan=".$colspan.">".$header."</td></tr>";
	}
	
	//create the table row header
	function makeTDHeader($type,$header) {
		$this->out .= "<tr>\n<td valign=\"top\" class=\"dBug_".$type."Key\">".$header."</td><td>";
	}
	
	//close table row
	function closeTDRow() {
		return "</td></tr>\n";
	}
	
	//error
	function  error($type) {
		$error="Error: Variable cannot be a";
		// this just checks if the type starts with a vowel or "x" and displays either "a" or "an"
		if(in_array(substr($type,0,1),array("a","e","i","o","u","x")))
			$error.="n";
		return ($error." ".$type." type");
	}

	//check variable type
	function checkType($var) {
		switch(gettype($var)) {
			case "resource":
				$this->varIsResource($var);
				break;
			case "object":
				$this->varIsObject($var);
				break;
			case "array":
				$this->arraylevel++;
				if(!isset($this->maxarraylevel)) {
					$this->maxarraylevel = 0;
				}
				$this->maxarraylevel = max($this->arraylevel, $this->maxarraylevel);
				$this->varIsArray($var);
				$this->arraylevel--;
				if($this->arraylevel == 0) $this->maxarraylevel = 0;
				break;
			case "boolean":
				$this->varIsArray($var);
				break;
			default:
				$this->varIsArray($var);
				break;
				$var=($var==="") ? "[empty string]" : $var;
				$this->out .= "\n\n<table cellspacing=0><tr>\n<td>".$var."</td>\n</tr>\n</table>\n\n";
				break;
		}
	}
	
	//if variable is a boolean type
	function varIsBoolean($var) {
		$var=($var==1) ? "[TRUE]" : "[FALSE]";
		$this->out .= $var;
	}
			
	//if variable is an array type
	function varIsArray($var) {
		$var_orig = $var;
		$var_ser = $this->ddserialize($var);
		array_push($this->arrHistory, $var_ser);
		$this->arraydim = max($this->arraydim, count($this->arrHistory));
		
		if(is_array($var)) $this->makeTableHeader("array", '^°^°^°'.$this->arraylevel.'^°^°^°');
		elseif(is_bool($var)) {
			$this->makeTableHeader("object","bool");
			$var=($var==1) ? "TRUE" : "FALSE";
		}
		elseif(is_double($var)) {
			$this->makeTableHeader("object","double");
		}
		elseif(is_int($var)) {
			$this->makeTableHeader("object","integer");
		}
		elseif(is_null($var)) {
			$this->makeTableHeader("object","NULL");
		}
		else {
			$length = strlen($var);
			$this->makeTableHeader("object","string [$length]");
		}
		if(is_array($var)) {
			foreach($var as $key=>$value) {
				$this->name=$key;
				$this->makeTDHeader("array",$key);
				
				//check for recursion
				if(is_array($value)) {
					$var_ser = $this->ddserialize($value);
					if(in_array($var_ser, $this->arrHistory, TRUE))
						$value = "*RECURSION*";
				}
				
				if(in_array(gettype($value),$this->arrType)) {
					$this->checkType($value);
				}
				else {
					if(is_bool($value)) {
						$value=($value==1) ? "[TRUE]" : "[FALSE]";
					}
					if(is_null($value)) {
						$value = '[NULL]';
					}
					$value=(($value)==="") ? "[empty string]" : $value;
					if(strpos($value, 'http')===0) $value = "<a href='$value'>$value</a>";
					else $value = nl2br(htmlspecialchars($value));
					$this->out .= $value;
				}
				$this->out .= $this->closeTDRow();
			} # end foreach
			$arraydim = $this->maxarraylevel - $this->arraylevel + 1;
			$this->out = str_replace('^°^°^°'.$this->arraylevel.'^°^°^°', 'array, '.$arraydim.'-dim', $this->out);
		}
		else {
			if(is_null($var)) {
				$var = '[NULL]';
			}
			$this->out .= "<tr>\n<td>".nl2br(htmlspecialchars($var)).$this->closeTDRow();
			if(is_string($var_orig) && strlen($var_orig)<=$GLOBALS['DD']['maxstringforhex']) {
				$hex = "";
				for($i=0;$i<strlen($var_orig);$i++) {
					$hex .= sprintf("%02X ",ord($var_orig{$i}));
				}
				$this->out .= "<tr>\n<td>HEX: $hex" . $this->closeTDRow();
			}
		}
		array_pop($this->arrHistory);
		$this->out .= "</table>\n\n";
	}
	
	//if variable is an object type
	function varIsObject($var) {
		$class = get_class($var);
		$var_ser = $this->ddserialize($var);
		array_push($this->arrHistory, $var_ser);
		$this->makeTableHeader("object", "object of class $class");
		
		if(is_object($var)) {
			$arrObjVars=get_object_vars($var);
			foreach($arrObjVars as $key=>$value) {

				$this->name=$key;
				$this->makeTDHeader("object",$key);
				
				//check for recursion
				if(is_object($value)||is_array($value)) {
					$var_ser = $this->ddserialize($value);
					if(in_array($var_ser, $this->arrHistory, TRUE)) {
						$value = (is_object($value)) ? "*RECURSION* -> $".get_class($value) : "*RECURSION*";

					}
				}
				if(in_array(gettype($value),$this->arrType)) {
					$this->checkType($value);
				}
				else {
					if(is_bool($value)) {
						$value=($value==1) ? "[TRUE]" : "[FALSE]";
					}
					if(is_null($value)) {
						$value = '[NULL]';
					}
					$value=(($value)==="") ? "[empty string]" : $value;
					if(strpos($value, 'http')===0) $value = "<a href='$value'>$value</a>";
					else $value = nl2br(htmlspecialchars($value));
					$this->out .= $value;
				}
				$this->out .= $this->closeTDRow();
			}
#			$arrObjMethods=get_class_methods(get_class($var));
#			foreach($arrObjMethods as $key=>$value) {
#				$this->makeTDHeader("object",$value);
#				$this->out .= "[function]".$this->closeTDRow();
#			}
		}
		else $this->out .= "<tr>\n<td>".$this->error("object").$this->closeTDRow();
		array_pop($this->arrHistory);
		$this->out .= "</table>\n\n";
	}

	//if variable is a resource type
	function varIsResource($var) {
		$this->makeTableHeader("resourceC","resource",1);
		$this->out .= "<tr>\n<td>\n";
		switch(get_resource_type($var)) {
			case "fbsql result":
			case "mssql result":
			case "msql query":
			case "pgsql result":
			case "sybase-db result":
			case "sybase-ct result":
			case "mysql result":
				$tmp = explode(" ",get_resource_type($var));
				$db=current($tmp);
				$this->varIsDBResource($var,$db);
				break;
			case "gd":
				$this->varIsGDResource($var);
				break;
			case "xml":
				$this->varIsXmlResource($var);
				break;
			default:
				$this->out .= get_resource_type($var).$this->closeTDRow();
				break;
		}
		$this->out .= $this->closeTDRow()."</table>\n\n";
	}

	//if variable is a database resource type
	function varIsDBResource($var,$db="mysql") {
		if($db == "pgsql")
			$db = "pg";
		if($db == "sybase-db" || $db == "sybase-ct")
			$db = "sybase";
		$arrFields = array("name","type","flags");	
		$numrows=call_user_func($db."_num_rows",$var);
		$numfields=call_user_func($db."_num_fields",$var);
		$this->makeTableHeader("resource",$db." result",$numfields+1);
		$this->out .= "<tr><td class=\"dBug_resourceKey\">&nbsp;</td>";
		for($i=0;$i<$numfields;$i++) {
			$field_header = "";
			for($j=0; $j<count($arrFields); $j++) {
				$db_func = $db."_field_".$arrFields[$j];
				if(function_exists($db_func)) {
					$fheader = call_user_func($db_func, $var, $i). " ";
					if($j==0)
						$field_name = $fheader;
					else
						$field_header .= $fheader;
				}
			}
			$field[$i]=call_user_func($db."_fetch_field",$var,$i);
			$this->out .= "\n<td class=\"dBug_resourceKey\" title=\"".$field_header."\">".$field_name."</td>";
		}
		$this->out .= "</tr>";
		for($i=0;$i<$numrows;$i++) {
			$row=call_user_func($db."_fetch_array",$var,constant(strtoupper($db)."_ASSOC"));
			$this->out .= "<tr>\n";
			$this->out .= "\n<td class=\"dBug_resourceKey\">".($i+1)."</td>"; 
			for($k=0;$k<$numfields;$k++) {
				$tempField=$field[$k]->name;
				$fieldrow=$row[($field[$k]->name)];
				$fieldrow=($fieldrow==="") ? "[empty string]" : $fieldrow;
				$this->out .= "\n<td>".$fieldrow."</td>\n";
			}
			$this->out .= "</tr>\n";
		}
		$this->out .= "</table>\n\n";
		if($numrows>0)
			call_user_func($db."_data_seek",$var,0);
	}
	
	//if variable is an image/gd resource type
	function varIsGDResource($var) {
		$this->makeTableHeader("resource","gd",2);
		$this->makeTDHeader("resource","Width");
		$this->out .= imagesx($var).$this->closeTDRow();
		$this->makeTDHeader("resource","Height");
		$this->out .= imagesy($var).$this->closeTDRow();
		$this->makeTDHeader("resource","Colors");
		$this->out .= imagecolorstotal($var).$this->closeTDRow();
		$this->out .= "</table>\n\n";
	}
	
	//if variable is an xml type
	function varIsXml($var) {
		$this->varIsXmlResource($var);
	}
	
	//if variable is an xml resource type
	function varIsXmlResource($var) {
		$xml_parser=xml_parser_create();
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0); 
		xml_set_element_handler($xml_parser,array(&$this,"xmlStartElement"),array(&$this,"xmlEndElement")); 
		xml_set_character_data_handler($xml_parser,array(&$this,"xmlCharacterData"));
		xml_set_default_handler($xml_parser,array(&$this,"xmlDefaultHandler")); 
		
		$this->makeTableHeader("xml","xml document",2);
		$this->makeTDHeader("xml","xmlRoot");
		
		//attempt to open xml file
		$bFile=(!($fp=@fopen($var,"r"))) ? false : true;
		
		//read xml file
		if($bFile) {
			while($data=str_replace("\n","",fread($fp,4096)))
				$this->xmlParse($xml_parser,$data,feof($fp));
		}
		//if xml is not a file, attempt to read it as a string
		else {
			if(!is_string($var)) {
				$this->out .= $this->error("xml").$this->closeTDRow()."</table>\n\n";
				return;
			}
			$data=$var;
			$this->xmlParse($xml_parser,$data,1);
		}
		
		$this->out .= $this->closeTDRow()."</table>\n\n";
		
	}
	
	//parse xml
	function xmlParse($xml_parser,$data,$bFinal) {
		if (!xml_parse($xml_parser,$data,$bFinal)) { 
				   die(sprintf("XML error: %s at line %d\n", 
							   xml_error_string(xml_get_error_code($xml_parser)), 
							   xml_get_current_line_number($xml_parser)));
		}
	}
	
	//xml: inititiated when a start tag is encountered
	function xmlStartElement($parser,$name,$attribs) {
		$this->xmlAttrib[$this->xmlCount]=$attribs;
		$this->xmlName[$this->xmlCount]=$name;
		$this->xmlSData[$this->xmlCount]='$this->makeTableHeader("xml","xml element",2);';
		$this->xmlSData[$this->xmlCount].='$this->makeTDHeader("xml","xmlName");';
		$this->xmlSData[$this->xmlCount].='$this->out .= "<strong>'.$this->xmlName[$this->xmlCount].'</strong>".$this->closeTDRow();';
		$this->xmlSData[$this->xmlCount].='$this->makeTDHeader("xml","xmlAttributes");';
		if(count($attribs)>0)
			$this->xmlSData[$this->xmlCount].='$this->varIsArray($this->xmlAttrib['.$this->xmlCount.']);';
		else
			$this->xmlSData[$this->xmlCount].='$this->out .= "&nbsp;";';
		$this->xmlSData[$this->xmlCount].='$this->out .= $this->closeTDRow();';
		$this->xmlCount++;
	} 
	
	//xml: initiated when an end tag is encountered
	function xmlEndElement($parser,$name) {
		for($i=0;$i<$this->xmlCount;$i++) {
			eval($this->xmlSData[$i]);
			$this->makeTDHeader("xml","xmlText");
			$this->out .= (!empty($this->xmlCData[$i])) ? $this->xmlCData[$i] : "&nbsp;";
			$this->out .= $this->closeTDRow();
			$this->makeTDHeader("xml","xmlComment");
			$this->out .= (!empty($this->xmlDData[$i])) ? $this->xmlDData[$i] : "&nbsp;";
			$this->out .= $this->closeTDRow();
			$this->makeTDHeader("xml","xmlChildren");
			unset($this->xmlCData[$i],$this->xmlDData[$i]);
		}
		$this->out .= $this->closeTDRow();
		$this->out .= "</table>\n\n";
		$this->xmlCount=0;
	} 
	
	//xml: initiated when text between tags is encountered
	function xmlCharacterData($parser,$data) {
		$count=$this->xmlCount-1;
		if(!empty($this->xmlCData[$count]))
			$this->xmlCData[$count].=$data;
		else
			$this->xmlCData[$count]=$data;
	} 
	
	//xml: initiated when a comment or other miscellaneous texts is encountered
	function xmlDefaultHandler($parser,$data) {
		//strip '<!--' and '-->' off comments
		$data=str_replace(array("&lt;!--","--&gt;"),"",htmlspecialchars($data));
		$count=$this->xmlCount-1;
		if(!empty($this->xmlDData[$count]))
			$this->xmlDData[$count].=$data;
		else
			$this->xmlDData[$count]=$data;
	}

	function initJSandCSS() {
		$out = 
<<<SCRIPTS
<style type="text/css">
table.dBug_array,table.dBug_object,table.dBug_resource,table.dBug_resourceC,table.dBug_xml {
	font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; font-size:8pt; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=88)"; filter:alpha(opacity=88); opacity:0.88; 
}
table.dBug_array td,table.dBug_object td,table.dBug_resource td,table.dBug_resourceC td,table.dBug_xml td {
	font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; font-size:7pt;
}
.dBug_arrayHeader,
.dBug_objectHeader,
.dBug_resourceHeader,
.dBug_resourceCHeader,
.dBug_xmlHeader 
	{ font-weight:bold; color:#FFFFFF; cursor:pointer; }
.dBug_arrayKey,
.dBug_objectKey,
.dBug_xmlKey 
	{ cursor:pointer; }
/* array */
table.dBug_array { background-color:#00A000; margin-top:8px; position:relative; z-index:99999999;}
table.dBug_array td { background-color:#FFFFFF;  padding-left:3px; font-size:8pt; }
table.dBug_array td.dBug_arrayHeader { background-color:#90FF90; }
table.dBug_array td.dBug_arrayKey { background-color:#CCFFCC; text-align:right; padding-right:5px; }
/* object */
table.dBug_object { background-color:#4040FF; margin-top:8px; position:relative; z-index:99999999;}
table.dBug_object td { background-color:#FFFFFF;  font-size:8pt; }
table.dBug_object td.dBug_objectHeader { background-color:#C0C0FF; }
table.dBug_object td.dBug_objectKey { background-color:#CCDDFF; text-align:right; padding-right:5px; }
/* resource */
table.dBug_resourceC { background-color:#884488; margin-top:8px; position:relative; z-index:99999999;}
table.dBug_resourceC td { background-color:#FFFFFF;  font-size:8pt; }
table.dBug_resourceC td.dBug_resourceCHeader { background-color:#AA66AA; }
table.dBug_resourceC td.dBug_resourceCKey { background-color:#FFDDFF; text-align:right; padding-right:5px; }
/* resource */
table.dBug_resource { background-color:#884488; margin-top:8px; position:relative; z-index:99999999;}
table.dBug_resource td { background-color:#FFFFFF;  font-size:8pt; }
table.dBug_resource td.dBug_resourceHeader { background-color:#AA66AA; }
table.dBug_resource td.dBug_resourceKey { background-color:#FFDDFF; text-align:right; padding-right:5px; }
/* xml */
table.dBug_xml { background-color:#888888; position:relative; z-index:99999999;}
table.dBug_xml td { background-color:#FFFFFF;  font-size:8pt; }
table.dBug_xml td.dBug_xmlHeader { background-color:#AAAAAA; }
table.dBug_xml td.dBug_xmlKey { background-color:#DDDDDD; text-align:right; padding-right:5px; }
</style>


SCRIPTS;
		$out = str_replace("\n", ' ', $out);
		$out = str_replace("\r", ' ', $out);
		return $out;
	}

}

//variable debug ends	

	
	
//library stops
?>