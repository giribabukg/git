<?php
class CInc_Sys_Doc_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this->mTitle = lan('sys-doc.menu');
    $this->mMmKey = 'sys-doc';
    
    $this -> mfilePath = 'inc/sys/doc/';
    // Ask If user has right for this page
    $lpn = 'sys-doc';
    $lUsr = CCor_Usr::getInstance();
    if (! $lUsr->canRead($lpn)) {
      $this->setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $actual_link = $this->mReq->getVal('act');
    $path_parts = pathinfo($actual_link);
    $Currentfilename = urldecode($path_parts ["extension"]);
    $this->allconfig($Currentfilename);
    $this->render($this->getMenu());
  }

  protected function getMenu() {
    // $lMethods = get_class_methods ( get_class ( $this ) );
    $lMethods = $this->allcsvfilelist();
    natcasesort($lMethods);
    
    $lInherited = get_class_methods('CCor_Cnt');
    $lMenu = new CHtm_Vmenu(lan('sys-doc.menu'));
    foreach ( $lMethods as $lMethod ) {
      if (substr($lMethod, 0, 3) != 'act')
        continue;
        /*
       * if (substr ( $lMethod, 3, 1 ) == 'S')
       * continue;
       */
      if (in_array($lMethod, $lInherited))
        continue;
      
      $lAct = substr($lMethod, 3);
      $lMenu->addItem($lMethod, 'index.php?act=sys-doc.' . strtolower($lAct), $lAct);
    }
    
    $lMenu->setKey('act' . ucfirst($this->mAct));
    return $lMenu;
  }

  protected function renderMenu($aContent) {
    $this->render(CHtm_Wrap::wrap($this->getMenu(), $aContent));
  }

  protected function Allconfig($Currentfilename) {
    $filename = $this -> mfilePath . $Currentfilename . '.csv';
    $lRet = '';
    
    $row = 1;
    if (($handle = @fopen($filename, "r")) !== FALSE) {
      
      $lRet .= '<table cellpadding="2" class="tbl">';
      
      while ( ($data = fgetcsv($handle, 1000, ";")) !== FALSE ) {
        $data = array_map("utf8_encode", $data);
        $num = count($data);
        
        if ($row == 1) {
          $lRet .= '<thead><tr>';
        } else {
          $lRet .= '<tr>';
        }
        
        for($c = 0; $c < $num; $c ++) {
          if (empty($data [$c])) {
            $value = "&nbsp;";
          } else {
            $value = $data [$c];
          }
          if ($row == 1) {
            $lRet .= '<th class="th1">' . $value . '</th>';
          } 

          else {
            $lRet .= '<td>' . $value . '</td>';
          }
        }
        
        if ($row == 1) {
          $lRet .= '</tr></thead><tbody>';
        } else {
          $lRet .= '</tr>';
        }
        $row ++;
      }
      
      $lRet .= '</tbody></table>';
      fclose($handle);
    }
    $this->renderMenu($lRet);
  }

  protected function Allcsvfilelist() {
    $ext = '.csv';
    $Csvfilelist = array ();
    foreach ( glob($this -> mfilePath . '/*' . $ext) as $file ) {
      $file_name = ucfirst(basename($file, $ext));
      $Csvfilelist [] = 'act' . $file_name;
    }
    
    return $Csvfilelist;
  }
}