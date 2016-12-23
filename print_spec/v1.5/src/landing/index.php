<?php
function dispatch() {
  error_log('----------------------------'."\n", 3, 'c:/req.txt');
  $lRet = 'GET :'.var_export($_GET, true)."\n";
  error_log($lRet, 3, 'c:/req.txt');
  $lRet = 'POST :'.var_export($_POST, true)."\n";
  error_log($lRet, 3, 'c:/req.txt');
  $lRet = 'SERVER: '.var_export($_SERVER, true)."\n";
  error_log($lRet, 3, 'c:/req.txt');

  $scheme = 'http';
  $server = $_SERVER["SERVER_NAME"];
  $uri =    $_SERVER["REQUEST_URI"];
  $script = $_SERVER["SCRIPT_NAME"];
  $length = strlen($script) - 9;
  
  $part = substr($uri, $length);
  $add = '';
  if (false !== strpos($part, '?')) {
    list($part, $add) = explode('?', $part);
  }
  $exploded = explode('/', $part);
  #var_export($exploded);
  $i = 0;
  $partCount = count($exploded);
  $params = array();
  while ($i < $partCount-1) {
    $params[$exploded[$i]] = $exploded[$i+1];
    $i+= 2; 
  }
  
  $target = $scheme.'://'.$server.substr($script,0,-17).'index.php?';
  if (!empty($params)) {
    foreach ($params as $lKey => $lVal) {
      $target.= $lKey.'='.$lVal.'&';
    }
  }
  if (empty($add)) {
    $target = substr($target, 0, -1);
  } else {
    $target.= $add;
  }
  #echo $target;
  header('Location: '.$target);
}
dispatch();