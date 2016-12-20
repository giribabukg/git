<?php
/**
 * Create a Webcenter project
 *
 * This query can be used to create a Webcenter Project. A template can be
 * selected to seed the project with users, permissions etc.
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Query_Createproject extends CApi_Wec_Query {

  /**
   * Create a Webcenter project
   *
   * @param string $aProjectName The name of the Webcenter project
   * @param string $aTemplate Project template, optional but recommended
   * @param string $aDescription Project description
   * @return string The Webcenter Project ID on success
   */
  public function create($aProjectName, $aTemplate = '', $aDescription = '') {
    $lPre = CCor_Cfg::get('wec.prjprefix');
    if (!empty($lPre)) {
      $aProjectName = $lPre.$aProjectName;
    }

    $this -> dbg('Wec: '.$aProjectName.' template:'.$aTemplate);
    $lRet = $this -> getProjectidByName($aProjectName);
    if ($lRet !== False) return $lRet;

    $this -> setParam('projectname', $aProjectName);
    if (!empty($aTemplate)) {
      $this -> setParam('projecttemplatename', $aTemplate);
    }
    if (!empty($aDescription)) {
      $this -> setParam('projectdescription', $aDescription);
    }
    $lXml = $this -> query('CreateProject.jsp');
    #echo '<pre>---createproject.php---'.get_class().'---';var_dump($lXml,'#############');echo '</pre>';
    if (empty($lXml)) return false;
    # $this -> dbg(var_export($lXml, True));
    #$lXml = '<root>'.$lXml.'</root>';

    #$lHdl = fopen('inc/req/CreateProject'.date('Y-m-d-H-i-s').'.xml', 'w+');
    #fwrite($lHdl, $lXml);
    #fclose($lHdl);

    $lRes = new CApi_Wec_Response($lXml);
    if ($lRes -> isSuccess()) {
      $lDoc = $lRes -> getDoc();
      $lRet = (string)$lDoc -> projectID;
      if (empty($lRet)) {
        $lXml = '<root>'.$lXml.'</root>';
        $lRes = new CApi_Wec_Response($lXml);
        $lDoc = $lRes -> getDoc();
        $lRet = (string)$lDoc -> projectID;
      }
      return $lRet;
    } else {
      return false;
    }
  }

  /**
   * recall Webcenter project
   *
   * @param string $aProjectName The name of the Webcenter project
   * @return string The Webcenter Project ID on success
   */
  public function getProjectidByName($aProjectName) {
    $this -> setParam('projectname', $aProjectName);
    $lXml = $this -> query('GetDocumentList.jsp');
    $lPid = false;

    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return false;
    }
    $lDoc = $lRes -> getDoc();
    $lDatObj = new CCor_DateTime();
    $lRet = array();
    $lo = $lDoc['projectid'];

    foreach ($lDoc -> attributes() as $a => $b) {
      if ($a == 'projectid') {
        return (string)$b; // see line 58
      }
    }
    return false;
  }

}