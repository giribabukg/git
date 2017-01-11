<?php
/**
 * Ajx: Controller
 *
 * Description
 *
 * @package    AJX
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14617 $
 * @date $Date: 2016-06-19 00:01:17 +0200 (Sun, 19 Jun 2016) $
 * @author $Author: gemmans $
 */
class CInc_Ajx_Cnt extends CCor_Cnt {

	public function __construct(ICor_Req $aReq, $aMod, $aAct){
		parent::__construct($aReq, $aMod, $aAct);
	}

	protected function actStd(){
		$lVie = new CAjx_List();
		$this->render($lVie);
	}

	protected function actSub(){
		$lDir = $this->getReq('dir');
		$lPrf = $this->getReq('prf');
		$lVie = new CAjx_List($lDir, $lPrf);
		echo $lVie->getContent();
	}

	public function actPrf(){
		$lKey = $this->getReq('key');
		$lVal = $this->getReq('val');
		if(! empty($lKey)){
			$lUsr = CCor_Usr::getInstance();
			$lUsr->setPref($lKey, $lVal);
		}
		echo 'ok';
	}

	protected function actTog(){
		$lKey = $this->getReq('key');
		if(! empty($lKey)){
			$lUsr = CCor_Usr::getInstance();
			$lVal = ($lUsr->getPref($lKey, 0)) ? 0 : 1;
			$lUsr->setPref($lKey, $lVal);
		}
		echo 'ok ' . $lVal;
	}

// 	protected function actChoice(){
// 		$lVal = addslashes(trim($this->getReq('val')));
// 		$lDom = addslashes(trim($this->getReq('dom')));

// 		$lRet = '';
// 		$lRet .= '<ul>';
// 		$lSql = 'SELECT id,val FROM al_fie_choice WHERE alias="' . addslashes($lDom) . '" AND mand=' . MID;
// 		if(! empty($lVal)){
// 			$lSql .= ' AND val LIKE ("%' . addslashes($lVal) . '%") ';
// 		}
// 		$lSql .= ' ORDER BY val LIMIT 10';
// 		$lQry = new CCor_Qry($lSql);
// 		$lRet .= '<li>' . $this->getReq('val') . '</li>';
// 		$lCls = '<li>';
// 		foreach($lQry as $lRow){
// 			$lRet .= $lCls;
// 			$lRet .= '<span class="informal" style="position:absolute; right:1px;" onclick="Flow.Std.delChoice(' . intval($lRow['id']) . ')">'.img('img/ico/9/del.gif').'&nbsp;</span>';
// 			$lItm = $lRow['val'];
// 			$lItm = preg_replace('/' . $lVal . '/i', '<b>${0}</b>', $lItm);
// 			$lRet .= $lItm;
// 			$lRet .= '</li>';
// 			$lCls = ('<li>' == $lCls) ? '<li class="alt">' : '<li>';
// 		}
// 		$lRet .= '</ul>';
// 		echo $lRet;
// 	}


	protected function actChoice(){
	  $lVal = addslashes(trim($this->getReq('term'))); //entered value
	  $lDom = addslashes(trim($this->getReq('dom'))); //learnTableName
	  //Überflüssige leerzeichen entfernen
	  $lVal = preg_replace("/\s+/", " ", $lVal);
	  $lDom = preg_replace("/\s+/", " ", $lDom);

	  $json_row = array();
	  $ret_json = array();

	  //SQL
	  $sql = 'SELECT id,val FROM al_fie_choice WHERE alias="' . addslashes($lDom) . '" AND mand=' . MID;
	  if(!empty($lVal)) {
	    $sql .= ' AND val LIKE ("%' . addslashes($lVal) . '%") ';
	  }
	  $sql .= ' ORDER BY val LIMIT 15';
	  $qry = new CCor_Qry($sql);

	  //Transform to json
	  foreach($qry as $row) {
	    $json_row["value"] = $row["val"];
	    $json_row["label"] = $row["id"];
	    array_push($ret_json, $json_row);
	  }

	  //Ausgabe
	  print Zend_Json::encode($ret_json);
	}

	protected function actNative(){
	  $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
	  if('mop' == $lWriter){
	    $this->getMopNative();
	  }
	  $lVal = trim($this->getReq('term'));

	  $lArr = CCor_Res::get('native');

	  $json_row = array();
	  $ret_json = array();

	  //Transform to json
	  foreach($lArr as $lRow) {
	    $lAli = $lRow['alias'];
	    if(FALSE === strpos($lAli, $lVal)) {
	      continue;
	    }
	    $json_row["label"] = $lAli;
	    $json_row["value"] = $lRow['native'];
	    array_push($ret_json, $json_row);
	  }

	  //Ausgabe
	  print Zend_Json::encode($ret_json);
	}

	protected function getMopNative(){
		$lVal = addslashes(trim($this->getReq('val')));
		$lSql = 'SELECT name_en,native,desc_en FROM al_fie_ref WHERE ';
		$lSql .= 'name_en LIKE "%' . $lVal . '%" ';
		$lSql .= 'OR alias LIKE "%' . $lVal . '%" ';
		$lSql .= 'OR native LIKE "%' . $lVal . '%" ';

		$lRet = '';
		$lRet .= '<ul>';
		$lQry = new CCor_Qry($lSql);
		foreach($lQry as $lRow){
			$lRet .= '<li>';
			$lRet .= '<span class="informal b">';
			$lRet .= htm($lRow['name_en']);
			$lRet .= '&nbsp;</span>';
			$lRet .= htm($lRow['native']);
			$lRet .= '<span class="informal i">';
			$lRet .= BR . nl2br(htm($lRow['desc_en']));
			$lRet .= '</span>';
			$lRet .= '</li>';
		}
		$lRet .= '</ul>';
		echo $lRet;
	}

	protected function actDelchoice(){
		$lId = $this->getInt('id');
		CCor_Qry::exec('DELETE FROM al_fie_choice WHERE id=' . $lId);
	}

	protected function actAddbm(){
		$lSrc = $this->getReq('src');
		$lJid = $this->getReq('jid');
		$lKey = $this->getReq('key');

		$lUsr = CCor_Usr::getInstance();
		$lUsr->addBookmark($lSrc, $lJid, $lKey);
		$lMen = new CJob_Bookmarks($lSrc, $lJid, $lKey);
		$lMen->render();
		exit();
	}

	protected function actRemovebm(){
		$lSrc = $this->getReq('src');
		$lJid = $this->getReq('jid');
		$lKey = $this->getReq('key');

		$lUsr = CCor_Usr::getInstance();
		$lUsr->removeBookmark($lSrc, $lJid, $lKey);
		$lMen = new CJob_Bookmarks($lSrc, $lJid, $lKey);
		$lMen->render();
		exit();
	}

	protected function actCrptip(){
		$lId = $this->getInt('id');
		$lKey = 'crp_' . LAN . '_' . $lId;
		$lCache = CCor_Cache::getInstance('tooltip');
		$lRet = $lCache->get($lKey);
		if(! $lRet){
			$lVie = new CAjx_Tip_Crp($lId);
			$lRet = $lVie->getContent();
			$lCache->set($lKey, $lRet);
		}
		echo $lRet;
	}

	protected function actHistip(){
      $lId = $this->getInt('id');
      $lName = $this->getVal('name');
      $lVie = new CAjx_Tip_His($lId, $lName);
      $lRet = $lVie->getContent();
      echo $lRet;
	}

	protected function actStptip(){
		$lId = $this->getInt('id');
		$lKey = 'stp_' . LAN . '_' . $lId;
		$lCache = CCor_Cache::getInstance('tooltip');
		$lRet = $lCache->get($lKey);
		if(! $lRet){
			$lVie = new CAjx_Tip_Stp($lId);
			$lRet = $lVie->getContent();
			$lCache->set($lKey, $lRet);
		}
		echo $lRet;
	}

	protected function actFieldtip(){
		$lId = $this->getInt('id');
		$lKey = 'field_' . LAN . '_' . $lId;
		$lCache = CCor_Cache::getInstance('tooltip');
		$lRet = $lCache->get($lKey);
		if(! $lRet){
			$lVie = new CAjx_Tip_Field($lId, LAN);
			$lRet = $lVie->getContent();
			$lCache->set($lKey, $lRet);
		}
		echo $lRet;
	}

	protected function actChktip(){
	  $lId = $this -> getInt('id');
	  $lKey = 'chk_' . LAN . '_' . $lId;
	  $lCache = CCor_Cache::getInstance('tooltip');
	  $lRet = $lCache -> get($lKey);
	  if (!$lRet) {
	    $lVie = new CAjx_Tip_Chk($lId, LAN);
	    $lRet = $lVie -> getContent();
	    $lCache -> set($lKey, $lRet);
	  }
	  echo $lRet;
	}

  protected function actUsremail() {
    $lVal = addslashes(trim($this -> getReq('term')));
    $lDom = addslashes(trim($this -> getReq('dom')));

    $json_row = array();
    $ret_json = array();

    $lSql = 'SELECT DISTINCT p.firstname,p.lastname,p.email';
    $lSql.= ' FROM al_usr AS p, al_usr_mand AS q';
    $lSql.= ' WHERE p.id=q.uid';
	$lSql.= ' AND q.mand IN (0,'.MID.')';
    $lSql.= ' AND p.del="N"';
    $lSql.= ' AND p.email<>""';
    $lSql.= ' AND (p.firstname LIKE "'.$lVal.'%" OR p.lastname LIKE "'.$lVal.'%")';
    $lSql.= ' ORDER BY p.lastname';
    $lSql.= ' LIMIT 10;';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lItm = $lRow['lastname'].', '.$lRow['firstname'];
      $lItm = $lItm;
      $lItm = preg_replace('/'.$lVal.'/i', '<b>${0}</b>', $lItm);
      $lItm.= htm(' <'.$lRow['email'].'>');
      $json_row["label"] = $lItm;
      $json_row["value"] = $lRow['lastname'].', '.$lRow['firstname']. ' <' .$lRow['email'] . '>';
      array_push($ret_json, $json_row);
    }
    print Zend_Json::encode($ret_json);
  }

protected function actCrpImg(){
  $lArrDir = Array(); // All DIR in which "img/crp/big" is existing.

  $json_row = array();
  $ret_json = array();

  // Default CRP Icons order
  $lDefaultDir = (THEME === 'default' ? 'img' . DS : 'img' . DS . THEME . DS);

  $lImgDir = array(
      $lDefaultDir,
      CUST_PATH_IMG,
      MAND_PATH_IMG
  );
  // Override icons with mandant icons.
  foreach($lImgDir as $lKey){
    if(file_exists($lKey . 'crp/big/')){
      $lArrDir[] = $lKey . 'crp/big/';
    }
  }

  foreach($lArrDir as $lRow){
    $lTemp = Array();
    $lIte = new DirectoryIterator($lRow);
    foreach($lIte as $lLin){
      if(! $lIte->isFile())
        continue;
      $lNam = $lIte->getFilename();
      if(FALSE !== strpos($lNam, 'h') && THEME == 'default') continue;
      // Only '.gif' icons to show
      if(substr($lNam, - 3) != 'gif') continue;
      $lTemp['name'] = intval($lNam);
      $lTemp['src'] = $lRow . $lNam;
      $lArrIcons[$lTemp['name']] = $lTemp;
    }
  }

  // Show Icons in the list.
  foreach($lArrIcons as $lRow){
    $json_row["label"] = $lRow["src"];
    $json_row["value"] = $lRow["name"];

    array_push($ret_json, $json_row);
  }
  //Ausgabe
  print Zend_Json::encode($ret_json);
}

	protected function actFlagImg(){
	  $json_row = array();
	  $ret_json = array();

	  $lDir = 'img/flag/';
	  $lIte = new DirectoryIterator($lDir);
	  foreach($lIte as $lLin){
	    if(! $lIte->isFile())
	      continue;
	    $lNam = $lIte->getFilename();
	    $lName = strval($lNam);
	    $lLoops = 1; // str_replace erwartet die Anzahl der Ersetzungen als Variable!
	    $lName = str_replace('.gif', '', $lName, $lLoops);

	    $json_row["label"] = $lDir . $lNam;
	    $lNam = str_replace(".gif", "", $lNam);
	    $json_row["value"] = $lNam;

	    array_push($ret_json, $json_row);
	  }
	  //Ausgabe
	  print Zend_Json::encode($ret_json);
	}

	protected function getCascade($aSearch = NULL){
		$lCol = $this->getVal('_column');
		$lDom = $this->getVal('_picklist');

		$lCols = array();
		$lHtbs = array();
		$lSql = 'SELECT alias,col,htb FROM al_pck_columns WHERE domain="' . addslashes($lDom) . '"';
		$lSql .= 'AND mand IN (0,"' . MID . '")';
		$lQry = new CCor_Qry($lSql);
		foreach($lQry as $lRow){
			$lCols[$lRow['alias']] = $lRow['col'];
			$lHtbs[$lRow['alias']] = $lRow['htb'];
		}
		if(! empty($lHtbs[$lCol])){
			$lHtb = $lHtbs[$lCol];
		}

		$lArr = array();

		$lAll = $this->mReq->getAll();
		$lFil = array();
		foreach($lAll as $lKey => $lVal){
			if(substr($lKey, 0, 7) == 'filter_'){
				$lAlias = substr($lKey, 7);
				// cho $lAlias;
				if(isset($lCols[$lAlias])){
					$lFil['col' . $lCols[$lAlias]] = $lVal;
				}
			}
		}

		$lArr = array();
		$lResCol = 'col' . $lCols[$lCol];
		$lSql = 'SELECT DISTINCT(' . $lResCol . ') AS val FROM al_pck_items ';
		$lSql .= 'WHERE domain="' . addslashes($lDom) . '" ';
		//$lSql .= 'AND mand="' . MID . '" ';
		$lSql .= 'AND mand IN (0,"' . MID . '") ';
		if(! empty($lFil))
			foreach($lFil as $lKey => $lVal){
				if('' == $lVal)
					continue;
				$lSql .= 'AND ' . $lKey . '="' . addslashes($lVal) . '" ';
			}
		if(! empty($aSearch)){
			$lSql .= 'AND ' . $lResCol . ' LIKE "%' . addslashes($aSearch) . '%" ';
		}
		$lSql .= 'ORDER BY ' . $lResCol;
		// echo $lSql;

		$lQry->query($lSql);
		if($lHtb){
			$lArrHtb = CCor_Res::get('htb', $lHtb);
			foreach($lQry as $lRow){
				$lKey = $lRow['val'];
				$lVal = (isset($lArrHtb[$lKey])) ? $lArrHtb[$lKey] : $lKey;
				$lArr[$lKey] = $lVal;
			}
		} else{
			foreach($lQry as $lRow){
				$lArr[$lRow['val']] = $lRow['val'];
			}
		}
		return $lArr;
	}

	protected function actCselect(){
		$lRet = array();
		$lRet[] = array('', ' ', false);
		$lOld = $this->getReq('_old');
		$lOld = html_entity_decode($lOld, ENT_QUOTES, 'UTF-8');
		$lArr = $this->getCascade();
		if(!empty($lArr)){
			foreach($lArr as $lKey => $lVal){
			    $lKey = trim($lKey);
			    $lVal = trim($lVal);
				$lSelected = ((string) $lOld == (string) $lKey);
				$lRet[] = array($lKey, $lVal,	$lSelected);
				//$lRet[] = array($lKey, $lVal, $lSelected);
			}
		}
		echo Zend_Json::encode($lRet);
	}

	protected function actCcomplete(){
		$lArr = $this->getCascade($this->getVal('val'));
		$lRet = '<ul>';
		if(! empty($lArr))
			foreach($lArr as $lVal){
				$lVal = htm($lVal);
				$lRet .= '<li>' . htm($lVal) . '</li>';
			}
		$lRet .= '</ul>';
		echo $lRet;
	}

	private function getLanguages($aCountries){
		if(empty($aCountries))
			return '';
		$lCtr = explode(',', $aCountries);
		$lCtr = array_unique($lCtr);
		$lSql = 'SELECT col2 FROM al_pck_items WHERE domain="ctr2lan" AND mand=' . MID . ' AND col1 IN(';
		foreach($lCtr as $lVal){
			$lSql .= '"' . addslashes($lVal) . '",';
		}
		$lSql = strip($lSql) . ');';
		$lRet = array();
		$lQry = new CCor_Qry($lSql);
		foreach($lQry as $lRow){
			$lArr = explode(',', $lRow['col2']);
			foreach($lArr as $lItem){
				$lRet[] = $lItem;
			}
		}
		$lRet = array_unique($lRet);
		sort($lRet);
		return implode(',', $lRet);
	}

	protected function actRegionSelect(){
		$lRegion = $this->getVal('value');
		$lSql = 'SELECT col2 FROM al_pck_items WHERE domain="regctr" AND mand=' . MID . ' AND col1="' . addslashes($lRegion) . '";';
		$lRes = CCor_Qry::getStr($lSql);
		$lRet = array(
				'country' => '',
				'language' => ''
		);
		if($lRes){
			$lRet['country'] = $lRes;
			$lRet['language'] = $this->getLanguages($lRes);
		}
		echo Zend_Json::encode($lRet);
	}

	protected function actCountrySelect(){
		$lCountries = $this->getVal('value');
		$lRet = array();
		$lRet['language'] = $this->getLanguages($lCountries);
		echo Zend_Json::encode($lRet);
	}

	protected function actGetfunctionmembers(){
		$lFunc = $this->getInt('func');
		$lRet = CCor_Res::extract('id', 'fullname', 'usr', array(
				'gru' => $lFunc
		));
		echo Zend_Json::encode($lRet);
	}

	protected function actGselect(){
		$lGru = $this->getInt('gru');

		$lSql = 'SELECT id,name FROM al_gru WHERE parent_id=' . $lGru;
		$lQry = new CCor_Qry($lSql);
		foreach($lQry as $lRow){
			$lGrp[$lRow['id']] = $lRow['name'];
		}

		$lRet = array();
		foreach($lGrp as $lGid => $lName){
			$lRet[$lGid] = $lName;
		}

		echo Zend_Json::encode($lRet);
	}

	protected function actUselect(){
		$lGru1 = $this->getInt('gru1');
		$lGru2 = $this->getInt('gru2');
		$lNoPreselect = $this->getInt('nopre');

		$lSql = 'SELECT id,firstname,lastname FROM al_usr u WHERE u.id IN ';
		$lSql .= '(SELECT m1.uid FROM al_usr_mem m1 ';
		$lSql .= 'WHERE m1.gid=' . $lGru1 . ' AND m1.uid IN ';
		$lSql .= '(SELECT m2.uid FROM al_usr_mem m2 WHERE m2.gid=' . $lGru2 . '))';

		/*
		 * $lSql = 'SELECT u.id,u.firstname,u.lastname '; $lSql.= 'FROM al_usr u, al_usr_mem m1, al_usr_mem m2 '; $lSql.= 'WHERE u.id=m1.uid AND u.id=m2.uid '; $lSql.= 'AND m1.gid='.$lGru1.' '; $lSql.= 'AND m1.mand IN (0,'.MID.') '; $lSql.= 'AND m2.gid='.$lGru2.' '; $lSql.= 'AND m2.mand IN (0,'.MID.') ';
		 */

		$lQry = new CCor_Qry($lSql);
		foreach($lQry as $lRow){
			$lUsr[$lRow['id']] = cat($lRow['lastname'], $lRow['firstname'], ', ');
		}

		$lRet = array();
		$lRet[''] = array(
				' ',
				false
		);
		$lSel = (count($lUsr) == 1); // if only one, select the first one
		if(1 == $lNoPreselect)
			$lSel = false;
		foreach($lUsr as $lUid => $lName){
			$lRet[$lUid] = array(
					$lName,
					$lSel
			);
			$lSel = false;
		}
		echo Zend_Json::encode($lRet);
	}

	protected function actUselect2(){
		$lTeam = $this->getInt('team');
		$lObs = $this->getReq('upd');

		$lQry = new CCor_Qry();

		$lTeamWhere = '';
		if(! empty($lTeam)){
			$lTeamMembers = array();
			$lSql = 'SELECT q.uid FROM al_usr AS p, al_usr_mem AS q WHERE p.id=q.uid AND p.del="N" AND q.gid=' . $lTeam;
			$lQry->query($lSql);
			foreach($lQry as $lRow){
				$lTeamMembers[$lRow['uid']] = 1;
			}
			if(! empty($lTeamMembers)){
				$lTeamUids = implode(',', array_keys($lTeamMembers));
				$lTeamWhere = 'AND uid IN (' . $lTeamUids . ')';
			}
		}

		$lGroupIds = array_unique($lObs);
		$lUsers = array();
		foreach($lGroupIds as $lGru){
			$lSql = 'SELECT q.uid FROM al_usr AS p, al_usr_mem AS q WHERE p.id=q.uid AND p.del="N" AND q.gid=' . $lGru . ' ' . $lTeamWhere;
			$lQry->query($lSql);
			foreach($lQry as $lRow){
				$lUid = $lRow['uid'];
				$lTeam2[$lGru][$lUid] = $lUid;
				$lUsers[$lUid] = 1;
			}
		}
		$lUids = implode(',', array_keys($lUsers));
		$lSql = 'SELECT id,firstname,lastname FROM al_usr ';
		$lSql .= 'WHERE del="N" AND id IN (' . $lUids . ') ';
		$lSql .= 'ORDER BY lastname,firstname';
		$lQry->query($lSql);
		foreach($lQry as $lRow){
			$lUserNames[$lRow['id']] = cat($lRow['lastname'], $lRow['firstname'], ', ');
		}
		$lRet = array();
		foreach($lObs as $lField => $lGid){
			$lMem = array();
			if(! empty($lTeam2[$lGid])){
				foreach($lTeam2[$lGid] as $lUid){
					if(isset($lUserNames[$lUid])){
						$lMem[$lUid] = $lUserNames[$lUid];
					}
				}
			}
			$lRet[$lField] = $lMem;
		}
		echo Zend_Json::encode($lRet);
	}

    protected function actGetLan() {
        $lLan = $this->getReq("term");

        echo lan($lLan);
    }

	protected function actGetmembers(){
		$lGid = $this->getInt('gid');
		$lMembers = CCor_Res::extract('id', 'fullname', 'usr', array(
				'gru' => $lGid
		));
		sort($lMembers);
		foreach($lMembers as $lName){
			echo htm($lName) . BR;
		}
		exit();
	}

	protected function actGetthumbnail() {
	  $lJid = $this->getInt('jid');

	  $lSvcWecInst = CSvc_Wec::getInstance();
	  $lDynamics = $lSvcWecInst->getDynamics($lJid);
	  $lFilename = $lDynamics['thumbnail_dir'] . $lDynamics['thumbnail_file'];
	  if(file_exists($lFilename)){
	    echo $lFilename;
	  } else{
	    echo 'na';
	  }
	  exit();
	}

	/**
	 * Array column
	 *
	 * @return array
	 */
	public static function array_col(array $aArray, $aKey) {
	  return array_map(function($aArray) use ($aKey) {return $aArray[$aKey];}, $aArray);
	}

	/**
	 * Array search
	 *
	 * @return array|boolean
	 */
	public static function array_search(array $aArray, $aKey, $aValue) {
	  foreach ($aArray as $lKey => $lValue) {
	    if ($lValue[$aKey] === $aValue) {
	      return $lValue;
	    }
	  }

	  return false;
	}

  protected function actUpdatethumbnail() {
    $lSrc = $this -> getReq('src');
    $lJobId = $this -> getReq('jobid');

    $lWecAvailable = CCor_Cfg::get('wec.available', FALSE);
    $lDalimAvailable = CCor_Cfg::get('dalim.available', FALSE);

    if ($lWecAvailable == TRUE && $lDalimAvailable == FALSE) {
      $lWectns = new CSvc_Wectns();
      $Parameters = $lWectns -> downloadImages(array('jobid' => $lJobId, 'src' => $lSrc));

      $lImage = new CJob_Image();
      $lOriginalSizedImage = $lImage -> getBase64EncodedImage($Parameters['img']);
      $lSmallSizedImage = $lImage -> getBase64EncodedImage($Parameters['thb']);

      $lStd = array('img' => $lOriginalSizedImage, 'thb' => $lSmallSizedImage, 'msg' => $Parameters['msg']);
      echo json_encode($lStd);
      exit;
    } else {
      $lSvcWecInst = CSvc_Wec::getInstance();
      $lStatics = $lSvcWecInst -> getStatics();
      $lDynamics = $lSvcWecInst -> getDynamics($lJobId);

      $lImgDir = $lDynamics['image_dir'].$lDynamics['image_file'];
      $lThbDir = $lDynamics['thumbnail_dir'].$lDynamics['thumbnail_file'];

      $lDoc = CCor_Qry::getStr('SELECT filename FROM al_job_files WHERE mand='.MID.' AND jobid='.esc($lJobId).' AND sub="dalim" ORDER BY DateLastChange DESC LIMIT 1;');
      if (!$lDoc) {
        $lStd = array('err' => lan('lib.dialoguedoc.not.found'));
        echo json_encode($lStd);
        exit;
      }

      $lLan = '';

      $lUtil = new CApi_Dalim_Utils();

      $lThb = $lUtil -> getThumbnail($lJobId.'/'.$lDoc);
      if ($lThb) {
        @mkdir($lDynamics['thumbnail_dir'], 0755, TRUE);
        $lCount = file_put_contents($lThbDir, $lThb);
        $lLan.= lan('lib.thumbnail.created').", ";
      } else {
        $lLan.= lan('lib.thumbnail.not.created').", ";
      }

      $lImg = $lUtil -> getLowRes($lJobId.'/'.$lDoc);
      if ($lImg) {
        @mkdir($lDynamics['image_dir'], 0755, TRUE);
        $lCount = file_put_contents($lImgDir, $lImg);
        $lLan.= lan('lib.image.created');
      } else {
        $lLan.= lan('lib.image.not.created');
      }

      $lStd = array('img' => $lImgDir, 'thb' => $lThbDir, 'msg' => $lLan);
      echo json_encode($lStd);
      exit;
    }
  }

	protected function actUsrdet(){
		$lUid = $this->getInt('id');
		$lKey = 'usr_' . LAN . '_' . $lUid;
		$lCache = CCor_Cache::getInstance('tooltip');
		$lRet = $lCache->get($lKey);
		if(! $lRet){
			$lVie = new CAjx_Tip_Usr($lUid);
			$lRet = $lVie->getContent();
			$lCache->set($lKey, $lRet);
		}

		echo $lRet;
	}

	protected function actGrpmem(){
		$lGid = $this->getInt('id');
		$lKey = 'grp_' . LAN . '_' . $lGid;
		$lCache = CCor_Cache::getInstance('tooltip');
		$lRet = $lCache->get($lKey);
		if(! $lRet){
			$lVie = new CAjx_Tip_Grp($lGid);
			$lRet = $lVie->getContent();
			$lCache->set($lKey, $lRet);
		}

		echo $lRet;
	}

	protected function actCheckCredentials() {
	  $lUser = $this->getReq('user');
	  $lPass = $this->getReq('pass');
	  $lEnc = CApp_Pwd::encryptPassword($lPass);
	  $lSql = 'SELECT id FROM al_usr u WHERE user='.esc($lUser).' AND pass='.esc($lEnc);
	  $lUid = CCor_Qry::getInt($lSql);

	  $lMe = CCor_Usr::getAuthId();
	  if ($lUid == $lMe) {
	    echo "ok";
	  } else {
	    echo "error";
	  }
	  exit;
	}

  protected function actGetJobTypes() {
    $lInt = array(); // interim result
    $lRet = array(); // result

    $lActiveJobTypes = CCor_Cfg::get('menu-aktivejobs');
    $lArchivedJobTypes = CCor_Cfg::get('menu-archivjobs');
    $lJobTypes = array_merge($lActiveJobTypes, $lArchivedJobTypes);

    foreach ($lJobTypes as $lKey => $lValue) {
      $lValue = strtolower($lValue);
      $lValue = ltrim($lValue, 'job-');

      if (!isset($lInt[$lValue]) && $lValue != 'all') {
        $lInt[$lValue] = 1;
        $lRet[] = array('code' => $lValue, 'name' => lan('job-'.$lValue.'.menu'));
      }
    }

    echo Zend_Json::encode($lRet);
  }

  protected function actGetWebstatus() {
    $lRet = array();

    $lQry = new CCor_Qry();
    $lQry -> query('SELECT p.code AS code,q.`status` AS `status`,q.name_'.LAN.' AS name FROM al_crp_master AS p, al_crp_status AS q WHERE p.id=q.crp_id AND p.mand=q.mand AND p.mand='.MID.' ORDER BY p.code,q.`status` ASC');
    foreach ($lQry as $lRow) {
        $lRet[$lRow['code']][$lRow['status']] = $lRow['name'];
//        $lRet[][$lRow['code']] = array('status' => $lRow['status'], 'name' => $lRow['name']);
    }

// error_log(print_r($lRet, true), 3, "D:/AMH/tmp.txt");

    echo Zend_Json::encode($lRet);
  }

  protected function actGetFileSources() {
    $lRet = array();

    $lDir = new RecursiveDirectoryIterator('./inc/job/fil/src/');
    $lIte = new RecursiveIteratorIterator($lDir);
    while ($lIte -> valid()) {
      if ($lIte -> isFile() && $lIte -> getExtension() == 'php') {
        $lFilename = $lIte -> getBasename('.php');
        $lRet[] = array('code' => $lFilename, 'name' => ucfirst($lFilename));
      }

      $lIte -> next();
    }

    echo Zend_Json::encode($lRet);
  }

  protected function actGetchartdata(){
    $lPeriod = $this->getVal('per');
    $lTyp = $this->getVal('typ');
    $lDays = $this->getVal('dtInc');
    $lData = array('series'=>array(), 'categories'=>array());

    $lMands = array();
    $lQry = new CCor_Qry();
    $lQry -> query("SELECT * FROM al_sys_mand");
    foreach ($lQry as $lRow) {
      $lMands[$lRow['id']] = $lRow['name_en'];
    }

    switch($lPeriod){
      case 'Monthly':
        $lMonths = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
        $lMonthReached = false;# once true it indicates the foreach has arrived at the current month
        $lPastTwelveMonths = array();# our final array of months

        foreach($lMonths as $lKey => $lMonth){
          if($lMonthReached) $lPastTwelveMonths[] = array('month' => $lKey, 'name' => ucfirst($lMonth), 'year' => date('Y')-1);
          if($lKey+1 == date('n')) $lMonthReached = true;
        }

        if(date('n') != 12){
          foreach($lMonths as $lKey => $lMonth){
            $lPastTwelveMonths[] = array('month' => $lKey, 'name' => ucfirst($lMonth), 'year' => date('Y'));
            if(count($lPastTwelveMonths) == 12) break;
          }
        }

        foreach($lPastTwelveMonths as $lKey => $lMonth){
          $lMonth['month'] = intval($lMonth['month'])+1;
          $lMon = strlen($lMonth['month']) > 1 ? $lMonth['month'] : "0".$lMonth['month'];
          $lSql = "SELECT * FROM al_links_log WHERE datum LIKE '".$lMonth['year']."-".$lMon."%'";
          if($lTyp == 'Unique')
            $lSql.= " GROUP BY uid, mand";
          $lSql.= " ORDER BY mand ASC;";

          $lQry -> query($lSql);
          foreach ($lQry as $lRow) {
            $lMand = $lMands[$lRow['mand']];
            $lName = $lMonth['name'];

            if(!in_array($lName, $lData['categories']))
              $lData['categories'][] = $lName;

            $lData['series'][$lMand][$lName] = intval($lData['series'][$lMand][$lName])+1;
          }
        }

        break;
      case 'Weekly':

        for($i=13; $i > 0; $i--){
          $lWeekNum = date("W", strtotime("-".($i-1)." weeks"));
          $lYear = date("Y", strtotime("-".($i-1)." weeks"));
          list($lBeginWeekDay, $lEndWeekDay) = $this -> firstDayofWeek($lWeekNum, $lYear);

          $lSql = "SELECT * FROM al_links_log WHERE datum >= '".$lBeginWeekDay."' AND datum < '".$lEndWeekDay."'";
          if($lTyp == 'Unique')
            $lSql.= " GROUP BY uid, mand";
          $lSql.= " ORDER BY mand, datum ASC;";

          $lQry -> query($lSql);
          foreach ($lQry as $lRow) {
            $lMand = $lMands[$lRow['mand']];
            $lWeekNum = "Week ".date("W", strtotime($lBeginWeekDay));

            if(!in_array($lWeekNum, $lData['categories']))
              $lData['categories'][] = $lWeekNum;

            $lData['series'][$lMand][$lWeekNum] = intval($lData['series'][$lMand][$lWeekNum])+1;
          }
        }

        break;
      case 'Daily':
        for($i=7; $i > 0; $i--){
            $lDate = date("Y-m-d", strtotime("-".($i-1)." days"));

            $lSql = "SELECT * FROM al_links_log WHERE datum LIKE '".$lDate."%'";
            if($lTyp == 'Unique')
              $lSql.= " GROUP BY uid, mand";
            $lSql.= " ORDER BY mand, datum ASC;";

            $lQry -> query($lSql);
            foreach ($lQry as $lRow) {
              $lMand = $lMands[$lRow['mand']];

              if(!in_array($lDate, $lData['categories']))
                $lData['categories'][] = $lDate;

              $lData['series'][$lMand][$lDate] = intval($lData['series'][$lMand][$lDate])+1;
            }

            foreach($lData['series'] as $lMand=>$lDat){
              if(!array_key_exists($lDate, $lDat))
                $lData['series'][$lMand][$lDate] = 0;
            }
          }
          break;
        case 'Hourly':
          $lHrNow = ($lDays < 0 ? 24 : intval(date("H"))+1);
          for($i=0; $i < $lHrNow; $i++){
              $lHour = (strlen($i) == 1 ? "0".$i : strval($i));
              $lDate = date("Y-m-d", strtotime($lDays." days")) . " " . $lHour;

              $lSql = "SELECT * FROM al_links_log WHERE datum LIKE '".$lDate."%'";
              if($lTyp == 'Unique')
                $lSql.= " GROUP BY uid, mand";
              $lSql.= " ORDER BY mand, datum ASC;";

              $lQry -> query($lSql);
              foreach ($lQry as $lRow) {
                $lMand = $lMands[$lRow['mand']];
                $lHr = strval(date("H", strtotime($lRow['datum']))).":00";

                $lData['series'][$lMand][$lHr] = intval($lData['series'][$lMand][$lHr])+1;
              }

              if(!in_array($lHour.":00", $lData['categories']))
                $lData['categories'][] = $lHour.":00";
           }

           for($j=0; $j < $lHrNow; $j++){
             $lHour = (strlen($j) == 1 ? "0".$j : strval($j)).":00";
             foreach($lData['series'] as $lMand=>$lDat){
               if(!array_key_exists($lHour, $lDat))
                 $lData['series'][$lMand][$lHour] = 0;
             }
           }

           foreach($lData['series'] as $lMand=>$lDat){
               ksort($lData['series'][$lMand]);
           }
           break;
    }

    echo Zend_Json::encode($lData);
  }

  protected function firstDayofWeek($lWeek, $lYear) {
    $lTime = mktime ( 0, 0, 0, 1, 1, $lYear );
    if ($lWeek == 1) {
      while (date("W", $lTime) < 2) {
        $lTime += (60 * 60 * 24);
      }
      $lTime -= (60 * 60 * 24 * 8);
    } else {
      while (date("W", $lTime) < $lWeek) {
        $lTime += (60 * 60 * 24);
      }
      $lTime -= (60 * 60 * 24);
    }
    $lTime += (60 * 60 * 24); //add one day to have start of week being a monday
    $lTime = date("Y-m-d", $lTime);
    $lEndOfWeek = date("Y-m-d", strtotime($lTime . " +7 days"));

    return array($lTime, $lEndOfWeek);
  }

  public function actDefineroles(){
    $lTyp = $this->getVal('typ');
    $lTyp = ($lTyp == 'User') ? 'uselect' : 'gselect';
    $lOld = $this->getVal('val');

    $lFie = CCor_Res::get('fie');
    $lRet = array('' => '');
    foreach ($lFie as $lRow) {
      if ($lRow['typ'] == $lTyp) {
        $lKey = $lRow['alias'];
        $lVal = $lRow['name_'.LAN];

        $lSelected = ((string) $lOld == (string) $lKey);
        $lRet[$lKey] = array(
            $lVal,
            $lSelected
        );
      }
    }

    echo Zend_Json::encode($lRet);
  }

	protected function actGetImage(){
	    $lSrc = $this->getVal('src');
	    $lAttr = $this->getVal('attr');
	    $lHtm = $this->getVal('htm');

	    $lPath = getImgPath($lSrc);
	    $lImg = ($lHtm == 'true' ? img($lPath, $lAttr) : $lPath);

	    echo $lImg;
	}

	protected function actApl() {
	  $lId  = $this->getInt('event_id');
	  $lJid = $this->getVal('jid');
	  $lPrefix = $this->getReq('prefix');
	  $lCanDeselect = $this->getReq('canDeselect');
	  $lObj = new CCust_Job_Apl_Preview($lJid);
	  $lUsr = CCor_Usr::getInstance();
	  $lJob = $lUsr->getPref('apl.job');
	  try {
	    $lJob = unserialize($lJob);
	    $lObj->setJob($lJob);
	  } catch (Exception $lExc) {

	  }
	  $lObj->setEventId($lId);
	  $lObj->loadActions();
	  $lObj->saveToSession($lPrefix);
	  if ($lCanDeselect) $lObj->setCheck(TRUE);
	  echo $lObj->getContent();
	  exit;
	}

	protected function actAddapluser() {
	  $lJid = $this->getReq('jid');
	  $lPrefix = $this->getReq('prefix');
	  $lObj = new CCust_Job_Apl_Preview($lJid);
	  $lObj->loadFromSession($lPrefix);
	  $lSrc = $this->getReq('mod');
	  $lSource = $this->getReq('src');
	  $lPos = $this->getReq('pos');
	  $lDays = $this->getInt('days');
	  $lCanDeselect = $this->getReq('canDeselect');
	  $lSrc = substr($lSrc, strpos($lSrc, "-") + 1);
	  $lFac = new CJob_Fac($lSrc, $lJid);
	  $lJob = $lFac -> getDat();
	  $lObj -> setJob($lJob);

	  $lUids = array();
	  $lGids = array();

	  foreach ($lSource as $lId) {
	    if ($lId < 0) {
	      $lGids[] = -$lId;
	    } else {
	      $lUids[] = $lId;
	    }
	  }

	  if (!empty($lUids)) {
	    $lObj->addUsers($lPrefix, $lUids, $lPos, $lDays, 'email_usr');
	  }
	  if (!empty($lGids)) {
	    $lObj->addUsers($lPrefix, $lGids, $lPos, $lDays, 'email_gru');
	  }
	  $lObj->saveToSession($lPrefix);
	  if ($lCanDeselect) $lObj->setCheck(TRUE);
	  #$lObj->loadFromSession($lPrefix);

	  echo $lObj->getContent();
	  exit;
	}

	protected function actUpdateapl() {
	  $lJid = $this->getReq('jid');
	  $lPrefix = $this->getReq('prefix');
	  $lObj = new CCust_Job_Apl_Preview($lJid);
	  $lObj->loadFromSession($lPrefix);

	  echo $lObj->getContent();
	  exit;
	}

	protected function actSetMail() {
	  $lRet = array();
    $lMsg = "success";
	  $lEmailId = $this->getVal('emailid');
	  $lStatus = $this->getVal('status');
	  if($lStatus == '0'){
	  	$lSql = 'UPDATE al_sys_mails SET mail_status="1" WHERE id ='.$lEmailId;
	  	$lQry = new CCor_Qry($lSql);
	  }
	  if($lStatus == '1'){
	  	$lSql = 'UPDATE al_sys_mails SET mail_status="0" WHERE id ='.$lEmailId;
	  	$lQry = new CCor_Qry($lSql);
	  }
    $lRet = array($lMsg, $lQry);
	  echo json_encode($lRet);
	}

	protected function actSetActive() {
	  $lRet = array();
    $lMsg = "success";
	  $lEmailId = $this->getVal('emailid');
	  $lStatus = $this->getVal('status');
	  if($lStatus == '0'){
	  	$lSql = 'UPDATE al_sys_mails SET mail_active="1" WHERE id ='.$lEmailId;
	  	$lQry = new CCor_Qry($lSql);
	  }
	  if($lStatus == '1'){
	  	$lSql = 'UPDATE al_sys_mails SET mail_active="0" WHERE id ='.$lEmailId;
	  	$lQry = new CCor_Qry($lSql);
	  }

    $lRet = array($lMsg, $lQry);
	  echo json_encode($lRet);
	}
  protected function actSaveJobQuestion() {
    $lId = mysql_real_escape_string($this->getReq("id"));
    global $gNum;
    $gNum = 1000 + intval($lId); //Set Global Var for generating new unique id numbers
    $lMenu = new CHtm_Menu(lan('lib.ok'));
    $lAnswer = mysql_real_escape_string($this->getReq("msg"));
    $lId = mysql_real_escape_string($this->getReq("id"));
    $lSrc = mysql_real_escape_string($this->getReq("src"));
    $lJobId = mysql_real_escape_string($this->getReq("jobid"));
    $lStatus = mysql_real_escape_string($this->getReq('state'));
    $lQuest = mysql_real_escape_string($this->getReq('quest'));
    $lUsr = CCor_Usr::getInstance();
    $lDate = date('Y-m-d G:i:s');
    $lMsg = "success";
    $lCls = 'border-radius:5px;padding:5px;';
    $lIco = "";
    $lErr = 0;

    $lSql = "SELECT * FROM al_job_questions_".MID." WHERE id = ". $lId;
    $lQry = new CCor_Qry($lSql);
    $lOld = $lQry->getAssoc();

    //GOING TO RED ALLOWED IF: Lower Right is given and OLD Status IS NOT 3 OR HIGHER RIGHT is given
    if($lStatus === "1" && (($lUsr->canEdit("questions-yellow") && $lOld["status"] < 3) || ($lUsr->canEdit("questions-green")))) { //RED
      $lIco = 'ico-w16-flag-01';
    }
    else if($lStatus === "2" && (($lUsr->canEdit("questions-yellow") && $lOld["status"] < 3) || ($lUsr->canEdit("questions-green")))) { //YELLOW
      $lIco = 'ico-w16-flag-02';
    }
    else if($lStatus === "3") { //GREEN
      $lIco = 'ico-w16-flag-03';
    }
    else {
      $lIco = 'ico-w16-ml-1';
      $lErr = 1;
    }

    //Build Menu
    if(($lUsr->canEdit("questions-yellow") && $lStatus < 3) || ($lUsr->canEdit("questions-green") && $lStatus < 3)) { //GREEN BUTTON
       $lMenu->addJsItem('Flow.job.saveJobQuestion(\'' . $lId . '\', \'3\',\'' . $lSrc . '\',\'' . $lJobId . '\',\'' . $lAnswer . '\')', lan('questions.3'), '<i class="ico-w16 ico-w16-flag-03"></i>');
    }
    if(($lUsr->canEdit("questions-yellow") && $lStatus < 3) || $lUsr->canEdit("questions-green")) { //RED BUTTON
       $lMenu->addJsItem('Flow.job.saveJobQuestion(\'' . $lId . '\', \'1\',\'' . $lSrc . '\',\'' . $lJobId . '\',\'' . $lAnswer . '\')', lan('questions.1'), '<i class="ico-w16 ico-w16-flag-01"></i>');
    }

    //Update DB
    $lQry = "UPDATE `al_job_questions_".MID."` SET `answer`='".$lAnswer."', `status`='".$lStatus."', `datum`='".$lDate."', `usr_id`='".$lUsr->getId()."'  WHERE  `id`=".$lId.";";
    if($lUsr ->canEdit('questions-job') && $lErr != 1) {
      CCor_Qry::exec($lQry);
      $lCls .= 'background-color:#c3d600;';
      $lRetMsg = lan("lib.change").': '.$lUsr->getFullName().' '.lan('lib.on').' '.htm($lDate);
    }
    else {
      $lMsg = "error";
      $lCls .= 'background-color:#F44336;';
      $lIco = 'ico-w16-ml-1';
      $lRetMsg = lan("questions.notSaved");
    }

    //Update Job His
    $lHis = new CJob_His($lSrc, $lJobId);
    $lAdd = serialize(array("JobQuest" => array("quest" => $lQuest, 'answ' => $lAnswer, 'state' => $lStatus)));
    $lHis ->add(htAnsweredQuestion, lan('job.his.typ.2048'), $lAnswer, $lAdd);

    //Return Values to JS
    $ret_array = array($lMsg, '<span style="'.$lCls.'">'.$lRetMsg.'</span>' ,$lIco, $lStatus, $lMenu->getContent());
    echo json_encode($ret_array);
  }

	protected function actSvalid() {
		/*
		if we only want to check updated fields, we could use something on the lines of
		    $lJid = $this -> getReq('jobid');
		    $lSrc = $this -> getReq('src');
        $lObj = 'CJob_'.$lSrc.'_Mod';
        $lMod = new $lObj($lJid);
        $lOld = $this -> getReq('old');
        $lMod -> getPost($this -> mReq, !empty($lOld));
        $lVal = $lMod->getUpdate();
    */
		$lVal = $this->getReq('val');
		$lFie = CCor_Res::get('fie');
		$lValidate = new CApp_Validate();
		$lValidate ->setRulesFromFields($lFie);
		$lOk = $lValidate->isValid($lVal);

		$lRet = array();
		$lRet['status'] = $lOk ? 'ok' : 'error';
		if (!$lOk) {
			$lRet['errors'] = $lValidate->getAllErrors();
		}
		echo Zend_Json::encode($lRet);
		exit;
	}

  protected function actGetChildGroups() {
    $lMsg = "success";
    $lPid = mysql_real_escape_string($this->getReq("pid"));
    $lLvl = mysql_real_escape_string($this->getReq("lvl"));
    $lCls = mysql_real_escape_string($this->getReq("pcls"));
    $lStart = $this->getReq("start") ? mysql_real_escape_string($this->getReq("start")) : 0;
    $lUntil = $this->getReq("until") ? mysql_real_escape_string($this->getReq("until")) : 3000;

    $lList = new CGru_List($lPid,1 ,$lStart ,$lUntil);
    $lRet = $lList->getOnlyRows($lLvl,$lCls);
    //Return Values to JS
    $ret_array = array($lMsg, $lRet);
    echo json_encode($ret_array);
  }

	protected function actGetvalidationfor() {
		$lAlias = $this->getReq('alias');
		$lFilter = array('map' => 'core.xml', 'alias' => $lAlias);
		$lRes = CCor_Res::extract('alias', 'validate_rule', 'fiemap', $lFilter);
		$lRet = '';
		if (!empty($lRes)) {
			$lRet = $lRes[$lAlias];
		}
		echo $lRet;
	}
}
