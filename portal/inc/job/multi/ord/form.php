<?php
class CInc_Job_Multi_Ord_Form extends CCor_Ren {

  protected $mMultipleJobEditFlag = NULL;

  public function __construct() {
    $this -> mCap = lan('job.multiple-edit.ord');
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post" id="job-multi-ord-form">'.LF;
    $lRet.= '  <input type="hidden" name="act" value="job-multi-ord.sord" />'.LF;
    $lRet.= '  <input type="hidden" name="ord" id="job-multi-ord-val" />'.LF;
    $lRet.= '  <table cellpadding="2" cellspacing="0" class="tbl w600">'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="cap">'.htm($this -> mCap).'</td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="td1" id="job-multi-ord">'.LF;
    $lRet.= '      </td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), 'Flow.multiplejobs.saveJobFields();jQuery("#job-multi-ord-form").submit();', '<i class="ico-w16 ico-w16-ok"></i>', 'button').NB;
    $lRet.= btn(lan('lib.reset'), 'Flow.multiplejobs.initJobFields();this.form.reset()', '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '      </td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '  </table>'.LF;
    $lRet.= '</form>'.LF;
    $lRet.= $this -> getJS();
    return $lRet;
  }

  protected function getJS() {
    $lRet = '';
    $lRet = '<script type="text/javascript">'.LF;
    $lRet.= '    jQuery(function(){'.LF;
    $lRet.= '        Flow.multiplejobs.initJobFields();'.LF;
    $lRet.= '    });'.LF;
    $lRet.= '</script>'.LF;
    return $lRet;
  }
}