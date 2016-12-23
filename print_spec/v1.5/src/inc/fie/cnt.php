<?php
class CInc_Fie_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('fie.menu');
    $this -> mMmKey = 'opt';

    $lpn = 'fie';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CFie_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lQry = new CCor_Qry('SELECT * FROM al_fie WHERE id='.$lId.' AND `mand`='.MID);
    if ($lRec = $lQry -> getAssoc()) {
      $lFrm = new CFie_Form_Base('fie.sedt', lan('fie.edt'));
      $lFrm -> setParam('id', $lId);
      $lFrm -> setParam('val[id]', $lId);
      $lFrm -> setParam('old[id]', $lId);
      $lFrm -> assignVal($lRec);
      $this -> render($lFrm);
    } else {
      $this -> redirect();
    }
  }

  protected function actSedt() {
    $lMod = new CFie_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('native', ucfirst($lMod -> getVal('native')));
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CFie_Form_Base('fie.snew', lan('fie.new'));
    $lFrm -> setVal('src', 'pro');
    $lFrm -> setVal('typ', 'string');

    $lAllJobs = CCor_Cfg::get('all-jobs');
    # $lAllJobs[] = 'pro';
    # $lAllJobs[] = 'sub';
    $lAvail = 0;
    foreach ($lAllJobs as $ltyp) {
      $lTyp = ucfirst($ltyp);
      $lAvail += constant('fs'.$lTyp);//('fs'.$lTyp) are defined in const.php!
    }
    $lFrm -> setVal('flags', ffDefault);// Vorbelegungen, um sich das Leben zu erleichtern.
    $lFrm -> setVal('avail', $lAvail);
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CFie_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('native', ucfirst($lMod -> getVal('native')));
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lMod = new CFie_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }

  protected function actFlag() {
    $lQry = new CCor_Qry('SELECT id,alias,typ,flags FROM al_fie');
    foreach ($lQry as $lRow) {
      $lFid = $lRow['id'];
      $lTyp = $lRow['typ'];
      $lAli = $lRow['alias'];
      $lFla = intval($lRow['flags']);

      $lNew = ffDefault;
      if ($lTyp == 'date') {
        $lNew = unsetBit($lNew, ffSearch);
      }
      if ($lTyp == 'boolean') {
        $lNew = unsetBit($lNew, ffSearch);
      }
      if ($lTyp == 'memo') {
        $lNew = unsetBit($lNew, ffSearch);
        $lNew = unsetBit($lNew, ffList);
        $lNew = unsetBit($lNew, ffSort);
      }

      if ($lFla != $lNew) {
        $this -> dbg($lAli.', '.$lTyp.' '.$lNew);
        CCor_Qry::exec('UPDATE al_fie SET flags='.$lNew.' WHERE id='.$lFid.' AND `mand`='.MID.' LIMIT 1');
      }
    }
    $this -> redirect();
  }

  protected function actStructChange() {
    $lVal = $this -> mReq -> getVal('val');
    if (empty($lVal)) {
      $this -> redirect();
    }
    foreach ($lVal as $lKey => $lSql) {
      $lSql = str_replace('\"', '"', $lSql);
      $lSql = str_replace("\'", "'", $lSql);
      CCor_Qry::exec($lSql);
    }

    $this -> redirect();
  }

  protected function actStructProof() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    $lSqls = array();
    if (CCor_Cfg::get('extended.reporting')) {
    	$lSqls = $this -> updateExtendedReportTable();
    }
    $lJobs_PDB = CCor_Cfg::get('all-jobs_PDB');// Abfrage, ob pdb (PortalDB) gebraucht wird!
    $lUsr = CCor_Usr::getInstance();
    $lVal = $lUsr -> getPref($this -> mMod.'.fil'); // was über den Filter übertragen wird!

    if (empty($lVal) OR 'all' == $lVal['src']) {
      $lVal = array();
      $lVal['src'] = 'pro,sub,arc'; // Projects, Subitems/Subprojects, Archive
      if( !empty($lJobs_PDB) ) {
        $lVal['src'] .= ',pdb';
      }
    };

    $lAddCrpTimesToSql = '';
    $lSql = 'SELECT COUNT(id) as count FROM `al_crp_status` WHERE `mand`='.MID.' GROUP BY crp_id';
    $lQry = new CCor_Qry($lSql);
    $lMaxCrpStatus = 0;
    foreach($lQry as $lRow) {
      if ($lMaxCrpStatus < $lRow['count']) {
        $lMaxCrpStatus = $lRow['count'];
      }
    }
    for($i = 1; $i <= $lMaxCrpStatus; $i++) {
      $lAddCrpTimesToSql .= "
          `fti_".$i."` datetime NOT NULL default '0000-00-00 00:00:00',
          `lti_".$i."` datetime NOT NULL default '0000-00-00 00:00:00',";
    }

    if (!empty($lVal)) {
      $lTable_al_fie = new CCor_Qry('SELECT id,src,alias,typ,flags,maxlen FROM al_fie WHERE `mand`='.MID.' ORDER BY alias');

      $lAllJobs = CCor_Cfg::get('all-jobs');
      $lsource = explode(',',$lVal['src']);
      $lReg = new CHtm_Fie_Reg();
      $lFla = 0;
#      $lslst = array();
      foreach ($lsource as $ls) {
        $lAddToSql = '';
        if (in_array($ls,$lJobs_PDB)) $ls = 'pdb';
        if ('arc' == $ls) {
          $lFla = 1;
          $lJobId = 'jobid';//mache direkt die unterschiedlichen SQLs
          $lStrukturTyp = 'varchar(13) NOT NULL'; // The JobId by Archive and Portaldatabase has Typ "VARCHAR"
        } elseif ('pdb' == $ls) {
          $lJobId = 'jobid';//mache direkt die unterschiedlichen SQLs
          $lStrukturTyp = 'bigint(20) unsigned NOT NULL auto_increment';
          //diese Typen werden NUR in der PDB gespeichert vielleicht zwei ids, setze bei dem einen ein A davor?
        } else {
          $lJobId = 'id';
          $lStrukturTyp = 'bigint(20) unsigned NOT NULL auto_increment';
        }
        if ('sub' == $ls) {
          $lFla = 1;
          $lAddToSql .= "
          `is_master` enum('','X')  NOT NULL default '',
          `pro_id` bigint(20) unsigned NOT NULL default '0',
          `wiz_id` bigint(20) unsigned NOT NULL default '0',";
          foreach ($lAllJobs as $lTyp) {
            $lAddToSql .= "
          `jobid_".$lTyp."` varchar(13) NOT NULL default '',";
          }
        }
        if ('pro' == $ls OR 'sub' == $ls) {
          $lFla = 1;
          $lAddToSql .= $lAddCrpTimesToSql;
          $lAddToSql .= "
          `mas_state` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
          `del` enum('Y','N')  NOT NULL default 'N',";
          foreach ($lAllJobs as $lTyp) {
            $lAddToSql .= "
          `".$lTyp."_state` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',";
          }
        } else {
          $lAddToSql .= "
          `fti_1` datetime NOT NULL default '0000-00-00 00:00:00',
          `lti_1` datetime NOT NULL default '0000-00-00 00:00:00',
          ";
        }
        $ls.='_'.MID;
        $lWhichTable = 'al_job_'.$ls;
        $lTable_al_job = new CCor_Qry('SHOW COLUMNS FROM '.$lWhichTable);

        $lDbVersion = new CCor_Qry('SELECT VERSION() as version');
        $lVersion = $lDbVersion->getDat();
        $lVersion = substr($lVersion['version'], 0, 1);
        if ( 4 < $lVersion) {
          $lAddCharset = 'DEFAULT CHARSET=utf8'; // Funzt bei mysql 4.0 noch nicht
        } else {
          $lAddCharset = '';
        }
        #echo '<pre>---cnt.php---';var_dump($lVersion,$lAddToSql,'#############');echo '</pre>';

        $lcre_sql = '';
        if ($lWriter == 'portal' && $lFla == 0) {
          $lcre_sql = $this -> getPdbCreateCode($lWhichTable);
        } else {
        $lcre_sql =
        "CREATE TABLE IF NOT EXISTS `".$lWhichTable."` (
          `".$lJobId."` ".$lStrukturTyp.",
          `src` varchar(20) default NULL,
          `flags` bigint(20) unsigned NOT NULL default '0',
          `status` varchar(100) default NULL,
          `webstatus` int,
          `last_status_change` datetime NOT NULL default '0000-00-00 00:00:00',
          `apl` bigint(20) unsigned default NULL,
          `net_knr` varchar(25) default NULL,
          `keyword` varchar(100) default NULL,
          `stichw` varchar(100) default NULL,
          `wec_prj_id` varchar(20) default NULL,";
        $lcre_sql .= $lAddToSql;
        $lcre_sql .= "
           PRIMARY KEY (`".$lJobId."`)
         ) ENGINE=MyISAM ".$lAddCharset.";";
      }
        #echo '<pre>---cnt.php--StructProof-';print_r($lcre_sql);echo '#############</pre>';
        $lal_job_fields = array();//(if not bereits in Vorlage: unset(
        foreach ($lTable_al_job as $lRow) {
          #echo '<pre>---cnt.php--$lTable_al_job-';var_dump($lRow,'#############');echo '</pre>';
          $lcre_sql = ''; // wenn eine Table was liefert => existiert sie => Kein CREATE TABLE ...
          $lAli = $lRow['Field'];
          $lTyp = $lRow['Type'];
          $lal_job_fields[$lAli] = $lTyp;
           # echo "AL $ls: $Ali $lTyp <br>";
          // var_export($lTable_al_job->mVal);
          // echo "<br>";
        }

        if ($lcre_sql !== '') {
          $lSqls[] = $lcre_sql;
        };
        # echo "$lcre_sql <br>";
        # var_export($lal_job_fields);
        # pro ist in pde ist in mba,ist in tpl ist in pac enthalten
#        $lslst[] = $ls;
#        $lslst = array('pro','sub');//array('pro','art','rep','sec','adm','mis','sub');

#        $lx = implode('","', $lslst);

        #$lTable_al_fie = new CCor_Qry('SELECT id,src,alias,typ,flags FROM al_fie WHERE src IN ("'.$lx.'") ORDER BY alias');
###       $lTable_al_fie = new CCor_Qry('SELECT id,src,alias,typ,flags FROM al_fie WHERE `mand`='.MID.' ORDER BY alias');
        #echo "<pre>";
        foreach ($lTable_al_fie as $lRow) {//$lTable_al_fie = new CCor_Qry('SELECT id,src,alias,typ,flags FROM al_fie WHERE `mand`='.MID.' ORDER BY alias');
         # $lFid = $lRow['id'];
         # $lSrc = $lRow['src'];
          $lTyp = $lRow['typ'];
          $lAli = $lRow['alias'];
          $lFla = intval($lRow['flags']);
          $lMaxLen = intval($lRow['maxlen']);
          $lsqTyp = $lReg -> getSqlType($lTyp, $lMaxLen);
          $this->dbg($lAli.' => '.$lMaxLen.' '.$lsqTyp);
          if (empty($lal_job_fields[$lAli])) {
            $lalter = "ALTER TABLE $lWhichTable ADD `$lAli` $lsqTyp";
            $lSqls[] = $lalter;
          } else {
            #echo '<pre>---cnt.php---';var_dump($lal_job_fields[$lAli],$lsqTyp,$lal_job_fields[$lAli] == $lsqTyp,'#############');echo '</pre>';
            if ($lal_job_fields[$lAli] != $lsqTyp) {
              $lalter = "ALTER TABLE $lWhichTable CHANGE `$lAli` `$lAli` $lsqTyp";
              $lSqls[] = $lalter;
            }
          }
        }// end_foreach ($lTable_al_fie as $lRow)

      }// end_foreach ($lsource as $ls)

    }// end_if (!empty($lVal))




    /* Auftragsfelder im Shadow einfügen
     * denen Flags 'Reporting' eingesetzt ist.
     * */

    $lWhichTable = "al_job_shadow_".MID;
    $lcre_sql =
          "CREATE TABLE IF NOT EXISTS `".$lWhichTable."` (
 `id` bigint( 20  )  unsigned NOT  NULL  auto_increment ,
 `src` char( 3  )  NOT  NULL default  '',
 `jobid` varchar( 13  )  default NULL ,
 `flags` bigint( 20  )  NOT  NULL default  '0',
 `webstatus` int( 10  )  unsigned NOT  NULL default  '0',
 `wec_prj_id` varchar( 20  )  NOT  NULL default  '',
 `artikel` varchar( 255  )  NOT  NULL default  '',
 `marke` varchar( 100  )  default NULL ,
 `material_nr` varchar( 50  )  NOT  NULL default  '',
 `sorte` varchar( 100  )  default NULL ,
 `amend_internal` tinyint( 3 ) unsigned NOT NULL default '0',
 `amend_author` tinyint( 3 ) unsigned NOT NULL default '0',
 `amend_both` tinyint( 3 ) unsigned NOT NULL default '0',
 `amend_count` tinyint( 4 ) NOT NULL default '0',
 `stichw` varchar( 255  )  NOT  NULL default  '',";

    $lcre_sql.= $lAddCrpTimesToSql;

    $lcre_sql.= "
 PRIMARY  KEY (  `id`  ) ,
 UNIQUE  KEY  `src_2` (  `src` ,  `jobid`  ) ,
 KEY  `src` (  `src`  ) ,
 KEY  `jobid` (  `jobid`  )
 ) ENGINE=MyISAM AUTO_INCREMENT=1 ;";

    $lTable_al_job = new CCor_Qry('SHOW COLUMNS FROM '.$lWhichTable);


    foreach ($lTable_al_job as $lRow) {
      $lcre_sql = ''; // Datenbank ist schon vorhanden
      $lFid = $lRow['Field'];
      $lTyp = $lRow['Type'];
      $lal_job_fields[$lFid] = $lTyp;

    }

    if ($lcre_sql !== '') {
      $lSqls[] = $lcre_sql; // Datenbank ist nicht vorhanden und führt "CREATE TABLE IF...."
    }

    foreach ($lTable_al_fie as $lRow) {//$lTable_al_fie = new CCor_Qry('SELECT id,src,alias,typ,flags FROM al_fie WHERE `mand`='.MID.' ORDER BY alias');
      # $lFid = $lRow['id'];
      # $lSrc = $lRow['src'];
      $lTyp = $lRow['typ'];
      $lAli = $lRow['alias'];
      $lFla = intval($lRow['flags']);
      $lMaxLen = intval($lRow['maxlen']);
      $lsqTyp = $lReg -> getSqlType($lTyp, $lMaxLen);

      if (empty($lal_job_fields[$lAli]) AND bitSet($lFla, ffReport)) {
        $lalter = "ALTER TABLE $lWhichTable ADD $lAli $lsqTyp";
        $lSqls[] = $lalter;
      } else {
        if (isset($lal_job_fields[$lAli]) AND $lal_job_fields[$lAli] == $lsqTyp AND bitSet($lFla, ffReport)) {
          # okay
        } else {
          $lalter = "ALTER TABLE $lWhichTable CHANGE $lAli $lAli $lsqTyp";
          #$lSqls[] = $lalter;
        }
      }
    }// end_foreach ($lTable_al_fie as $lRow)




    #######################################################################
    /* Tabellen für 3-tier Zusätzliche Projektebene SKU (Stock Keeping Unit)
     * Im Einsatz bei Intouch
     */
    $lWhichTable = "al_job_sku_".MID;
    $lcre_sql =
  "CREATE TABLE IF NOT EXISTS `".$lWhichTable."` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `webstatus` BIGINT(20) NULL DEFAULT NULL,
    `last_status_change` DATETIME NULL,
    `stichw` VARCHAR(255) NULL DEFAULT '',
    PRIMARY KEY (`id`)
  );";
    $lTable_al_job = new CCor_Qry('SHOW COLUMNS FROM '.$lWhichTable);
    foreach ($lTable_al_job as $lRow) {
      $lcre_sql = ''; // => Datenbank ist schon vorhanden
     }
    if ($lcre_sql !== '') {
      $lSqls[] = $lcre_sql; // Datenbank ist nicht vorhanden und führt "CREATE TABLE IF...."
    }

    $lWhichTable = "al_job_sku_sub_".MID;
    $lcre_sql =
  "CREATE TABLE IF NOT EXISTS `".$lWhichTable."` (
    `sku_id` BIGINT(20) UNSIGNED NOT NULL,
    `job_id` VARCHAR(13) NOT NULL DEFAULT '',
    `src` CHAR(3) NULL DEFAULT ''
  );";
    $lTable_al_job = new CCor_Qry('SHOW COLUMNS FROM '.$lWhichTable);
    foreach ($lTable_al_job as $lRow) {
      $lcre_sql = ''; // => Datenbank ist schon vorhanden
     }
    if ($lcre_sql !== '') {
      $lSqls[] = $lcre_sql; // Datenbank ist nicht vorhanden und führt "CREATE TABLE IF...."
    }

    $lWhichTable = "al_job_sku_sur_".MID;
    $lcre_sql =
  "CREATE TABLE IF NOT EXISTS `".$lWhichTable."` (
    `pro_id` BIGINT(20) UNSIGNED NOT NULL,
    `sku_id` BIGINT(20) UNSIGNED NOT NULL
  );";
    $lTable_al_job = new CCor_Qry('SHOW COLUMNS FROM '.$lWhichTable);
    foreach ($lTable_al_job as $lRow) {
      $lcre_sql = ''; // => Datenbank ist schon vorhanden
     }
    if ($lcre_sql !== '') {
      $lSqls[] = $lcre_sql; // Datenbank ist nicht vorhanden und führt "CREATE TABLE IF...."
    }


    if (count($lSqls) == 0) {
      $this -> redirect();
    } else {
      $lVie = new CSys_Sql_List($lSqls,'SQL list for the structural adjustment','fie.StructChange','fie.std');
      $this -> render($lVie);
    }
  }

  protected function getPdbCreateCode($aTable) {
    $lSql =
    "CREATE TABLE IF NOT EXISTS `".$aTable."` (
        `id` bigint(20) unsigned NOT NULL auto_increment,
        `jobid` bigint(20) unsigned NOT NULL,
        `jobnr` varchar(25) default NULL,
        `src` varchar(20) default NULL,
        `webstatus` int,
        `last_status_change` datetime NOT NULL default '0000-00-00 00:00:00',
        `flags` bigint(20) unsigned NOT NULL default '0',
        `apl` bigint(20) unsigned default NULL,
        `keyword` varchar(100) default NULL,
        `stichw` varchar(100) default NULL,
        `is_master` enum('','X')  NOT NULL default '',
        `wec_prj_id` varchar(20) default NULL,";
    $lSql.= "PRIMARY KEY (`id`), UNIQUE  KEY  `jobid` (`jobid`)) ENGINE=MyISAM COLLATE='utf8_general_ci' AUTO_INCREMENT=1;";
    return $lSql;
/*    `create_stamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `del` enum('Y','N')  NOT NULL default 'N',
    `pro_id` bigint(20) unsigned NOT NULL default '0',*/
  }

  protected function updateExtendedReportTable() {
  	$lReportSqls = array();
  	$lDBName = CCor_Cfg::get('db.name');
  	// Create & Update the report table if it is not exist already.
  	$lSql = 'SHOW TABLES FROM `'.$lDBName.'` like "al_job_shadow_'.MID.'_report"';
  	$lReportTable = CCor_Qry::getStr($lSql);
  	if (empty($lReportTable)) {
  		CCor_Qry::exec('CREATE TABLE IF NOT EXISTS al_job_shadow_'.MID.'_report LIKE al_job_shadow_'.MID);
  		$lReportSqls[] = 'ALTER TABLE al_job_shadow_'.MID.'_report DROP INDEX src_2';
  		$lReportSqls[] = 'ALTER TABLE `al_job_shadow_'.MID.'_report` ADD COLUMN `row_id` INT(10) UNSIGNED NOT NULL DEFAULT "0" AFTER `jobid`';
  		$lSql = 'SHOW COLUMNS FROM `'.$lDBName.'`.`al_job_shadow_'.MID.'_report` WHERE field LIKE "fti_%" OR field LIKE "lti_%"';
  		$lQry = new CCor_Qry($lSql);
  		foreach ($lQry as $lRow) {
  			$lReportSqls[] = 'ALTER TABLE `al_job_shadow_'.MID.'_report` DROP COLUMN `'.$lRow['Field'].'`';
  		}
  	}
  	// Add specific rows to the report table depending on the 'report.map' array in the cfg file
  	foreach (CCor_Cfg::get('report.map') as $lKey => $lVal) {
  		if (!empty($lVal)) {
  			$lSql = 'SHOW COLUMNS FROM `'.$lDBName.'`.`al_job_shadow_'.MID.'_report` WHERE field LIKE "%'.$lVal.'"';
  			$lColumn = CCor_Qry::getStr($lSql);
  			if (empty($lColumn)) {
  				$lSql = 'ALTER TABLE `al_job_shadow_'.MID.'_report`';
  				$lSql.= ' ADD COLUMN `fti_cr_'.$lVal.'` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",';
  				$lSql.= ' ADD COLUMN `lti_cr_'.$lVal.'` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00" AFTER `fti_cr_'.$lVal.'`;';
  				$lReportSqls[] = $lSql;
  			}
  		}
  	}
  	return $lReportSqls;
  }

  protected function actPrunearc() {
    $lTbl = 'al_job_arc_'.MID;
    $lSql = 'SHOW COLUMNS FROM '.$lTbl;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lArc[$lRow['Field']] = 1;
    }
    $lFie = CCor_Res::extract('alias', 'name_de', 'fie');
    $lFie['jobid'] = 1;
    $lFie['src'] = 1;
    $lFie['webstatus'] = 1;

    $lRet = '<table class="tbl">';
    foreach ($lArc as $lAlias => $lDummy) {
      if (isset($lFie[$lAlias])) continue;
      $lRet.= '<tr>';
      #$lRet.= '<td class="td1">'.$lAlias.'</td>';
      $lRet.= '<td class="td1">ALTER TABLE '.$lTbl.' DROP '.$lAlias.';</td></tr>';
    }
    $lRet.= '</table>';
    $this->render($lRet);
  }

  protected function errLine($aMsg, $aField = null) {
    $lRet = '<tr>';
    $lRet.= '<td class="td1"><i class="ico-w16 ico-w16-ml-2"></i></td>';
    $lRet.= '<td class="td1">';
    if (!is_null($aField)) {
      $lRet.= '<a href="index.php?act=fie.edt&amp;id='.$aField['id'].'" class="db">';
      $lRet.= htm($aMsg);
      $lRet.= '</a>';
    } else {
      $lRet.= htm($aMsg);
    }
    $lRet.= '</td></tr>';
    return $lRet;
  }

  protected function actSanitycheck() {
    $lSql = 'SELECT * FROM al_fie WHERE mand='.MID;
    $lQry = new CCor_Qry($lSql);
    $lNatArr = array();
    $lAliArr = array();

    $lRet = '<table cellpadding="2" cellspacing="0" class="tbl">';
    $lRet.= '<tr><td class="th2 w16">&nbsp;</td><td class="th2">Issue</td></tr>';
    foreach ($lQry as $lRow) {
      $lNat = $lRow['native'];
      $lAli = $lRow['alias'];
      $lTyp = $lRow['typ'];
      if (empty($lNat)) {
        if ( !in_array($lTyp, array('image', 'file')) ) {
          $lRet.= $this->errLine('Empty native for field '.$lRow['alias'], $lRow);
        }
      } else if (isset($lNatArr[$lNat])) {
        $lRet.= $this->errLine('Alias '.$lAli.': native '.$lNat.' already in use for alias '.$lNatArr[$lNat], $lRow);
      } else {
        $lNatArr[$lNat] = $lAli;
      }

      if (empty($lAli)) {
        $lRet.= $this->errLine('Empty alias for field '.$lRow['name_en'].', ID '.$lRow['id'], $lRow);
      } else if (isset($lAliArr[$lAli])) {
        $lRet.= $this->errLine('Alias '.$lAli.' already in use for field '.$lAliArr[$lAli], $lRow);
      } else {
        $lAliArr[$lAli] = $lRow['name_en'];
      }
    }
    $lReq = array('src', 'jobnr', 'webstatus', 'apl', 'status', 'flags',
        'last_status_change');
    foreach ($lReq as $lAlias) {
      if (!isset($lAliArr[$lAlias])) {
        $lRet.= $this->errLine('Required Fields '.$lAlias.' does not exist');
      }
    }
    $lRet.= '</table>'.BR;
    $lRet.= '<a href="index.php?act=fie" class="nav">Back</a>';
    $this->render($lRet);
  }

  protected function actCreatezus() {
    $lId = $this->getInt('id');
    $lAlias = $this->getReq('alias');
    
    $lQry = new CApi_Alink_Query('createZusInfo');
    $lQry -> addParam('sid', MAND);
    $lQry -> addParam('name', 'H_'.$lAlias);
    $lRes = $lQry->query();
    $lNum = $lRes->getVal('infoid');
    if (!empty($lNum)) {
      
      $lSql = 'UPDATE al_fie SET native='.esc($lNum).' WHERE id='.$lId;
      CCor_Qry::exec($lSql);
    }
    $this->redirect();
  }

}