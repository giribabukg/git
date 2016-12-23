<?php
class CInc_Svn_Tree extends CHtm_Tree_List {

  public function __construct($aFolder = null) {
    parent::__construct();
    $this->mRoot = $this->add('WAVE ROOT');
    $this->mRoot->setExpanded(true);
    $this->addJs();
  }

  protected function addJs() {
    $lJs = 'onFolder = function(aElem){'.LF;
    $lJs.= 'var dir = jQuery(aElem).attr("data-dir");'.LF;
    $lJs.= 'var root = jQuery(aElem).closest(".svn-wrap");'.LF;
    $lJs.= 'jQuery(root).find(".svn-dir").html(dir);'.LF;
    $lJs.= 'var url="index.php?act=svn.folder"; var data ={"dir":dir};'.LF;
    $lJs.= 'jQuery(root).find(".svn-content").load(url, data);'.LF;
    $lJs.= '};'.LF;

    $lJs.= 'svnLoad = function(aAct){'.LF;
    $lJs.= 'var root = jQuery(".svn-content").closest(".fl");'.LF;
    $lJs.= 'var dir = jQuery(root).find(".svn-dir").text();'.LF;
    $lJs.= 'var url="index.php?act=svn."+aAct; var data ={"dir":dir};'.LF;
    $lJs.= 'jQuery(root).find(".svn-content").load(url, data);'.LF;
    $lJs.= '};'.LF;

    $lJs.= 'svnCommit = function(aElem){'.LF;
    $lJs.= 'var root = jQuery(".svn-content").closest(".fl");'.LF;
    $lJs.= 'var form = jQuery(aElem).closest("form");'.LF;
    $lJs.= 'var msg = jQuery("textarea",form).val();'.LF;
    $lJs.= 'if ("" == msg) {alert("Please enter a comment"); return}'.LF;
    $lJs.= 'var dir = jQuery(form).find(".elem-dir").val();'.LF;
    $lJs.= 'var files = []; jQuery(":checked", form).each(function() { files.push(jQuery(this).val()); });';
    $lJs.= 'var url="index.php?act=svn.commit";';
    $lJs.= 'var data ={"dir":dir, "files":files, "msg":msg};'.LF;
    $lJs.= 'jQuery(root).find(".svn-content").load(url, data);'.LF;
    $lJs.= '};'.LF;

    $lJs.= 'svnAdd = function(aElem){'.LF;
    $lJs.= 'var root = jQuery(".svn-content").closest(".fl");'.LF;
    $lJs.= 'var form = jQuery(aElem).closest("form");'.LF;
    $lJs.= 'var dir = jQuery(form).find(".elem-dir").val();'.LF;
    $lJs.= 'var files = []; jQuery(":checked", form).each(function() { files.push(jQuery(this).val()); });';
    $lJs.= 'var url="index.php?act=svn.add";';
    $lJs.= 'var data ={"dir":dir, "files":files};'.LF;
    $lJs.= 'jQuery(root).find(".svn-content").load(url, data);'.LF;
    $lJs.= '};'.LF;

    $lJs.= 'svnRevert = function(aElem){'.LF;
    $lJs.= 'var root = jQuery(".svn-content").closest(".fl");'.LF;
    $lJs.= 'var form = jQuery(aElem).closest("form");'.LF;
    $lJs.= 'var dir = jQuery(form).find(".elem-dir").val();'.LF;
    $lJs.= 'var files = []; jQuery(":checked", form).each(function() { files.push(jQuery(this).val()); });';
    $lJs.= 'var url="index.php?act=svn.revert";';
    $lJs.= 'var data ={"dir":dir, "files":files};'.LF;
    $lJs.= 'jQuery(root).find(".svn-content").load(url, data);'.LF;
    $lJs.= '};'.LF;

    $lJs.= 'svnTogCheck = function(aFlag){'.LF;
    $lJs.= 'var root = jQuery(".svn-content").closest(".fl");'.LF;
    $lJs.= 'var check = (aFlag) ? "checked" :"";'.LF;
    $lJs.= 'jQuery(".svn-cb",root).each(function(){this.checked=aFlag;});'.LF;
    $lJs.= '};'.LF;

    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lJs);
  }

  public function addRoot($aFolder, $aName = '') {
    $lName = (empty($aName)) ? $aFolder : $aName;
    $lNode = $this->mRoot->add($lName, array('dir' => $aFolder));
    $lNode->setExpanded();
    $this->addFolder($lNode, $aFolder);
  }

  protected function addFolder($aNode, $aFolder) {
    $lIte = new DirectoryIterator($aFolder);
    foreach ($lIte as $lItem) {
      if ($lItem->isDot()) {
        continue;
      }
      if ($lItem->isFile()) {
        continue;
      }
      $lName = $lItem->getFilename();
      if ($lName[0] == '.') {
        continue;
      }
      $lFullName  = $aFolder.DS.$lName;
      $lNode = $aNode->add($lName, array('dir' => $lFullName));
      $this->addFolder($lNode, $lFullName);
    }
  }

  protected function getNodeSpan($aNode) {
    $lTag = new CHtm_Tag('span');
    $lTag -> setAtt('class', 'nav nw');
    $lTag -> setAtt('id', $aNode -> getId().'s');

    $lJs = '';
    if ($aNode -> hasChildren()) {
      $lJs.= '$(this).up(\'li\').down(\'ul\').toggle();';
    }
    $lDir = $aNode -> getVal('dir');
    if (!empty($lDir)) {
      $lTag -> setAtt('data-dir', $lDir);
      $lJs.= 'onFolder(this)';
    }
    if (!empty($lJs)) {
      $lTag -> setAtt('onclick', $lJs);
    }
    $lRet = $lTag -> getTag();
    $lRet.= htm($aNode -> getVal('caption'));
    $lRet.= '</span>';
    return $lRet;
  }
}