<?php
function dispatch() {
  $lJid = (isset($_REQUEST['j'])) ? $_REQUEST['j'] : 0;
  header('Location: index.php?act=fachpack&jid='.$lJid);
}
dispatch();