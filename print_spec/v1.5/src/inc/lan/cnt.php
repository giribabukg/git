<?php
class CInc_Lan_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    if (0 == MID) {
      $this -> mTitle = lan('lang.menu');
      $this -> mMmKey = 'opt';

      // Ask If user has right for this page
      $lpn = 'lang';
      $lUsr = CCor_Usr::getInstance();
      if (!$lUsr -> canRead($lpn)) {
        $this -> setProtection('*', $lpn, rdNone);
      }

      $lAvailLang = array();
      $lSql = "SELECT * FROM `al_sys_languages`";
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lAvailLang[$lRow['code']] = $lRow['name_'.LAN];
      }
      $this -> mAvailLang = $lAvailLang;
      #echo '<pre>---list.php---'.get_class().'---';var_dump($lAvailLang,'#############');echo '</pre>';
    } else {
      $this -> redirect('index.php?act=hom-wel');
    }

  }

  protected function actStd() {
    $lVie = new CLan_List($this -> mAvailLang);
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lCod = $this -> getReq('id');

    $lVie = new CLan_Form('lan.sedt', lan('lib.edit_item'), $this -> mAvailLang);
    $lVie -> load($lCod);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CLan_Mod($this -> mAvailLang);
    $lMod -> getPost($this -> mReq);
    if ($lMod -> hasChanged()) {
      $lMod -> update();

      $this -> redirect('index.php?act=sys-svc.DelCacheAll&name=cache&url='.urlencode($this -> getStdUrl()));

    }
    $this -> redirect();
  }

  #########################################################
  ###################   new LANGUAGES   ###################
  #########################################################

  protected function actNew() {
    $lVie = new CLan_Form('lan.newlang', lan('new_lang'), $this -> mAvailLang);
    $this -> render($lVie);
  }
  
  protected function actCpy() {
    $lCpyFrom = $this -> getReq('id');
  
    $lHdl = lan('lang.cpy').' '.$this -> mAvailLang[$lCpyFrom];
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lHdl,$this -> mAvailLang,'#############');echo '</pre>';
    $lVie = new CLan_Form('lan.newlang', $lHdl, $this -> mAvailLang);
    $lVie -> setParam('id', $lCpyFrom);
  
    $this -> render($lVie);
  }

  protected function actNewlang() {
    $lCpyFrom = $this -> getReq('id', '');
    $lVal     = $this -> getReq('val', '');
    $lNewCode = (isset($lVal['code'])) ? $lVal['code'] : '';
    $lNewName = (isset($lVal['new_name'])) ? $lVal['new_name'] : '';

    if (!empty($lNewCode) AND !empty($lNewName)) {
      $lNewLang = '_'.$lNewCode;
      if (!empty($lCpyFrom)) {
        $lCpyFrom = '_'.$lCpyFrom;
        $lWithCpy = true;
        $lHdl = 'SQL list to copy to a new language';
      } else {
        $lCpyFrom = '_en';
        $lWithCpy = false;
        $lHdl = 'SQL list to insert a new language';
      }

      $lSqls = array();
      $lDB_Name = CCor_Cfg::get('db.name');

      $lSql = 'SHOW TABLES FROM '.$lDB_Name.';';
      #echo '<pre>---list.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
      $lAllTables = new CCor_Qry($lSql);
      foreach ($lAllTables as $lRow) {
        $lTable = $lRow['Tables_in_'.$lDB_Name];
        $lTable = backtick($lTable);

        $lSql = 'SHOW COLUMNS FROM '.$lTable.' LIKE '.esc('%'.$lCpyFrom).';'; // "_en" sollte per default ueberall vorhanden sein!
        #echo '<pre>---list.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
        $lColumns = new CCor_Qry($lSql);
        foreach ($lColumns as $lCol) {

          $lField = explode($lCpyFrom, $lCol['Field']);
          #echo '<pre>---list.php---'.get_class().'---'.$lTable;var_dump($lCol,'' == $lCol['Default'],empty($lCol['Default']), isset($lCol['Default']),'#############');echo '</pre>';

          if ($lField[0] != $lCol['Field']) {
            $lOldColumn = $lCol['Field'];
            $lOldColumn = backtick($lOldColumn);
            $lNewColumn = $lField[0].$lNewLang;
            $lNewColumn = backtick($lNewColumn);

            if ('NO' == $lCol['Null']) {
              $lNull = ' NOT NULL';
            } else {
              $lNull = '';
            }
            if ('text' == $lCol['Type']) {
              $lDefault = "";
            } elseif (!isset($lCol['Default'])) {
              $lDefault = " DEFAULT NULL";
            } elseif (empty($lCol['Default'])) {
              $lDefault = " DEFAULT ''";
            }

            $lAlterSql = 'ALTER TABLE '.$lTable.' ADD '.$lNewColumn.' '.$lCol['Type'].$lNull.$lDefault.' AFTER '.$lOldColumn.';';
            $lSqls[] = $lAlterSql;
            #echo '<pre>---list.php---'.get_class().'---'.$lTable;var_dump($lAlterSql);echo '</pre>';
            if ($lWithCpy) {
              $lCopySql  = 'UPDATE '.$lTable.' SET '.$lNewColumn.' = '.$lOldColumn.';';
              $lSqls[] = $lCopySql;
              # echo '<pre>---list.php---'.get_class().'---'.$lTable;var_dump($lCopySql, '#############');echo '</pre>';
            }

          }//end_if ($lField[0] != $lCol['Field'])

        }//end_foreach ($lColumns as $lCol)
      }//end_ foreach ($lAllTables as $lRow)
      #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lSqls,'#############');echo '</pre>';

      if (count($lSqls) == 0) {
        $this -> redirect();
      } else {
        $lHiddenFields = array();
        $lHiddenFields['id']  = $lCpyFrom;
        foreach ($lVal as $lKey => $lK) {
          $lHiddenFields[$lKey]  = $lK;
        }
        $lVie = new CSys_Sql_List($lSqls, $lHdl,'lan.structchange','lan.std',$lHiddenFields);
        $this -> render($lVie);
      }

    } else {
      $this -> dbg('Code and new_name has to be filled!');
      $this -> redirect();
    }//end_if (!empty($lNewCode) AND !empty($lNewName))
  }

  protected function actStructChange() {
    $lCpyFrom = $this -> getReq('id', '');
    $lNewCode = $this -> getReq('code', '');
    $lNewName = $this -> getReq('new_name', '');
    $lVal     = $this -> getReq('val', '');

    #$lVal          = $this -> mReq -> getVal('val');
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($this -> mReq,'#############');echo '</pre>';
    if ( empty($lVal) OR empty($lNewCode) OR empty($lNewName) ) {
      $this -> redirect();
    }

    foreach ($lVal as $lKey => $lSql) {
      $lSql = str_replace('\"', '"', $lSql);
      $lSql = str_replace("\'", "'", $lSql);
      #echo '<pre>->--cnt.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
      
      CCor_Qry::exec($lSql);
    }
  
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($this -> mReq,'#############');echo '</pre>';
    $lMod = new CLan_Mod($this -> mAvailLang);

    $lMod -> setVal('code',     $lNewCode);
    $lMod -> setVal('new_name', $lNewName);
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lMod -> setVal('name_'.$lLang, $this -> getReq('name_'.$lLang, ''));
    }

    //$lMod -> setTestMode(TRUE);

    if ($lMod -> insert()) {

      $this -> redirect('index.php?act=sys-svc.DelCacheAll&name=cache&url='.urlencode($this -> getStdUrl()));

    }
    $this -> redirect();
  }

  #############################################################
  #######   DELETE new LANGUAGES   only for ADMIN-1 !!! #######
  #############################################################

  protected function actDel() {
    $lCod = $this -> getReq('id');

    $lDB_Name = CCor_Cfg::get('db.name');

    $lSql = 'SHOW TABLES FROM '.$lDB_Name.';';
    #echo '<pre>---list.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
    $lAllTables = new CCor_Qry($lSql);
    foreach ($lAllTables as $lRow) {
      $lTable = $lRow['Tables_in_'.$lDB_Name];
      $lTable = backtick($lTable);
  
      $lSql = 'SHOW COLUMNS FROM '.$lTable.' LIKE '.esc('%_'.$lCod).';';
      #echo '<pre>---list.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
      $lColumns = new CCor_Qry($lSql);
      foreach ($lColumns as $lCol) {
  
        $lOldColumn = $lCol['Field'];
        if (FALSE !== strpos($lOldColumn, '_'.$lCod)) {
          $lOldColumn = backtick($lOldColumn);

          $lSql = 'ALTER TABLE '.$lTable.' DROP '.$lOldColumn.';';
          CCor_Qry::exec($lSql);
        }
  
      }//end_foreach ($lColumns as $lCol)
    }//end_ foreach ($lAllTables as $lRow)
  
    $lSql = 'DELETE FROM `al_sys_languages` WHERE `code` = '.esc($lCod).';';
    CCor_Qry::exec($lSql);
  
    $this -> redirect('index.php?act=sys-svc.DelCacheAll&name=cache&url='.urlencode($this -> getStdUrl()));
    
  }

}