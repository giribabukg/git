<?php
class CInc_Utl_Page extends CCor_Tpl {

  public function __construct($aTpl = NULL) {
    $lFile = (NULL === $aTpl) ? $this -> getProjectFilename('tool.htm') : $aTpl;
    $this -> open($lFile);
    //CSS
    $this -> setPat('pg.utilcss', $this -> getProjectFilename('css'.DS.'util.css'));
    $this -> setPat('pg.stylecss', $this -> getProjectFilename('css'.DS.'style.css'));
    //JS
    $this -> setPat('pg.jqueryjs', 'js'.DS.'jquery'.DS.'jquery.min.js');
    $this -> setPat('pg.jqueryuijs', 'js'.DS.'jquery'.DS.'jquery-ui.min.js');
    $this -> setPat('pg.prototypejs', 'js'.DS.'prototype.js');
    $this -> setPat('pg.phrasejs', 'js'.DS.'phrase.js');
    $this -> setPat('pg.flowjs', 'js'.DS.'flow.js');
    $this -> setPat('pg.stdjs', 'js'.DS.'std.js');
    $this -> setPat('pg.js', '');
    
    $this -> setPat('pg.lib.wait',  htm(lan('lib.wait')));

    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lNam = (isset($lArr[MID])) ? $lArr[MID] : lan('lib.unknown');
    $this -> setPat('pg.mand.name', $lNam);
  }

}