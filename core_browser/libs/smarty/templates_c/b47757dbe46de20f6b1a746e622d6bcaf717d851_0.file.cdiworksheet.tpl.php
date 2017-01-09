<?php
/* Smarty version 3.1.29, created on 2016-10-13 06:32:53
  from "/var/www/html/core/templates/cdiworksheet.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_57ff2a95806de1_58459074',
  'file_dependency' => 
  array (
    'b47757dbe46de20f6b1a746e622d6bcaf717d851' => 
    array (
      0 => '/var/www/html/core/templates/cdiworksheet.tpl',
      1 => 1474054345,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_57ff2a95806de1_58459074 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_regex_replace')) require_once '/var/www/html/core//libs/smarty/plugins/modifier.regex_replace.php';
?>
	<?php if ($_smarty_tpl->tpl_vars['errorMsg']->value != '') {?>
	<div class="page-header alert alert-danger">
		<h4><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['text']->value->__('ERROR_OCCURRED');?>
:</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['errorMsg']->value;?>
 (#<?php echo $_smarty_tpl->tpl_vars['errorCode']->value;?>
)</p>
	</div>
	<?php } else { ?>
	<div class="page-header"> <div class="row"> <div class="col-xs-8"> <h3><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_WORKSHEET_FOR_ORDER');?>
: <?php echo intval($_smarty_tpl->tpl_vars['serviceOrderHead']->value['ORDERID']);?>
</strong></h3> <h5><?php echo $_smarty_tpl->tpl_vars['text']->value->__($_smarty_tpl->tpl_vars['salesOrderItem']->value['USAGE']);?>
</h5> </div> <div class="col-xs-4"> <p class="text-right"><img src="<?php echo @constant('WWW_TOP');?>
/templates/images/schawk_logo.png" width="180" /></p> </div> </div> </div> <div class="help-block"><br/></div> <div id="orderhead"> <div class="row"> <div class="col-xs-6"> <table class="table table-condensed"> <tbody> <tr> <td class="col-xs-3 col-sm-4"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_AG');?>
</strong></td> <td class="col-xs-9 col-sm-8"><?php if ($_smarty_tpl->tpl_vars['contacts']->value['AG']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['AG']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['AG']['NAME'];
}
if ($_smarty_tpl->tpl_vars['contacts']->value['AG']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['contacts']->value['AG']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PARTNERS_ZN');?>
</strong></td> <td><?php if ($_smarty_tpl->tpl_vars['partners']->value['ZN']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['partners']->value['ZN']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['partners']->value['ZN']['NAME'];
}
if ($_smarty_tpl->tpl_vars['partners']->value['ZN']['ADRESSDATA'] != '') {?><br /><small><?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['partners']->value['ZN']['ADRESSDATA'],"/\s+/"," ");?>
</small><?php }?></td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('DESCRIPTION');?>
</strong></td> <td><?php echo $_smarty_tpl->tpl_vars['serviceOrderHead']->value['SHORT_TEXT'];?>
</td> </tr> </tbody> </table> </div> <div class="col-xs-6"> <table class="table table-condensed"> <tbody> <tr> <td class="col-xs-3 col-sm-4"><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('FINISH_DATE');?>
</strong></td> <td colspan="2" class="col-xs-9 col-sm-8"><?php echo $_smarty_tpl->tpl_vars['operationKey']->value['50300'][0]['end_ts'];?>
</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PERSONAL_NAME');?>
</strong></td> <td class="col-xs-5"><?php if ($_smarty_tpl->tpl_vars['operationKey']->value['50110'][0]['PERS_NAME'] != ',') {
echo $_smarty_tpl->tpl_vars['operationKey']->value['50110'][0]['PERS_NAME'];
}?></td> <td class="col-xs-4 underlined">&nbsp;</td> </tr> <tr> <td><strong><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CONTACTS_ZM');?>
</strong></td> <td class="col-xs-5"><?php if ($_smarty_tpl->tpl_vars['contacts']->value['ZM']['NAME_LIST'] != '') {
echo $_smarty_tpl->tpl_vars['contacts']->value['ZM']['NAME_LIST'];
} else {
echo $_smarty_tpl->tpl_vars['contacts']->value['ZM']['NAME'];
}?></td> <td class="col-xs-4 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless table-small"> <tbody> <tr> <td><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_NO_DIGITAL_PLATES');?>
:&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_NO_DIGITAL_PLATES'];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_NO_ANALOG_PLATES');?>
:&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_NO_ANALOG_PLATES'];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_FILM_EXPOSURE');?>
:&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['values']->value['ZLP_FP_FILM_EXPOSURE'];?>
</td> <td class="text-right"><?php if ($_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_PRINT_METHOD'] == '') {
echo $_smarty_tpl->tpl_vars['text']->value->__('FACE_PRINT');
} else {
echo $_smarty_tpl->tpl_vars['text']->value->__($_smarty_tpl->tpl_vars['values']->value['ZLP_AG_F_PRINT_METHOD']);
}?></td> </tr> </tbody> </table> </div> </div> <div class="row" id="cdiworksheet"> <div class="col-xs-12"> <table class="table table-bordered  table-condensed table-small"> <thead> <tr> <th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_AG_F_COLOR_01');?>
</th> <th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_LENFILE_NAME_01');?>
<br /><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_DO_RESOLUTION_01');?>
<br /><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_REMARKS_01');?>
</th> <th><br/><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_MT_RELIEF_DEPTH');?>
</th> <th><br/><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_ROTATION_01');?>
</th> <th><br/><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_OUTPUT_01');?>
</th> <th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_FP_NO_OF_PLATES_01');?>
<br/><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_FLAT_TOP_01');?>
</th> <th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_AG_F_PLATE_TYPE_01');?>
<br/><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_FP_DIGITAL_ANALOGUE_01');?>
</th> <th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_AG_F_PLATE_THICKNESS_01');?>
<br/><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_ZLP_GES_F_STAG_CUT_01');?>
</th> <th><?php echo $_smarty_tpl->tpl_vars['text']->value->__('PRODUCTION_NO');?>
</th> </tr> </thead><?php if ($_smarty_tpl->tpl_vars['colorCount']->value > 0) {?><tbody><?php
$_smarty_tpl->tpl_vars['cid'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['cid']->step = 1;$_smarty_tpl->tpl_vars['cid']->total = (int) ceil(($_smarty_tpl->tpl_vars['cid']->step > 0 ? $_smarty_tpl->tpl_vars['colorCount']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['colorCount']->value)+1)/abs($_smarty_tpl->tpl_vars['cid']->step));
if ($_smarty_tpl->tpl_vars['cid']->total > 0) {
for ($_smarty_tpl->tpl_vars['cid']->value = 1, $_smarty_tpl->tpl_vars['cid']->iteration = 1;$_smarty_tpl->tpl_vars['cid']->iteration <= $_smarty_tpl->tpl_vars['cid']->total;$_smarty_tpl->tpl_vars['cid']->value += $_smarty_tpl->tpl_vars['cid']->step, $_smarty_tpl->tpl_vars['cid']->iteration++) {
$_smarty_tpl->tpl_vars['cid']->first = $_smarty_tpl->tpl_vars['cid']->iteration == 1;$_smarty_tpl->tpl_vars['cid']->last = $_smarty_tpl->tpl_vars['cid']->iteration == $_smarty_tpl->tpl_vars['cid']->total;
ob_start();
echo str_pad($_smarty_tpl->tpl_vars['cid']->value,2,0,@constant('STR_PAD_LEFT'));
$_tmp1=ob_get_clean();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable($_tmp1, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'id', 0);
ob_start();
echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_NO_OF_PLATES_".((string)$_smarty_tpl->tpl_vars['id']->value)];
$_tmp2=ob_get_clean();
ob_start();
echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_NO_OF_PLATES_".((string)$_smarty_tpl->tpl_vars['id']->value)];
$_tmp3=ob_get_clean();
if ($_tmp2 != '' && $_tmp3 > 0) {?><tr> <td rowspan="3" style="height: 75px;"><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_COLOR_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td colspan="4" style="height: 25px;"><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_LENFILE_NAME_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_NO_OF_PLATES_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_TYPE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_PLATE_THICKNESS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td rowspan="3" class="col-xs-3 noborderright"></td> </tr> <tr> <td style="height: 25px;"><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_DO_RESOLUTION_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_MT_RELIEF_DEPTH_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_ROTATION_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_OUTPUT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_FLAT_TOP_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_DIGITAL_ANALOGUE_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> <td><?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_GES_F_STAG_CUT_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
</td> </tr> <tr> <td colspan="7" style="height: 25px;"><?php if ($_smarty_tpl->tpl_vars['values']->value["ZLP_FP_REMARKS_".((string)$_smarty_tpl->tpl_vars['id']->value)] == '' && $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_REMARK_".((string)$_smarty_tpl->tpl_vars['id']->value)] == '') {?>&nbsp;<?php } else {
echo $_smarty_tpl->tpl_vars['values']->value["ZLP_FP_REMARKS_".((string)$_smarty_tpl->tpl_vars['id']->value)];?>
 <?php echo $_smarty_tpl->tpl_vars['values']->value["ZLP_AG_F_REMARK_".((string)$_smarty_tpl->tpl_vars['id']->value)];
}?></td> </tr><?php }
}
}
?>
</tbody><?php }?></table> </div> </div> <!-- End row --> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-3">&nbsp;</td> <td class="col-xs-8">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BATCH_NO');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BOX_NO');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('TYPE');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BATCH_NO');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BOX_NO');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('TYPE');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BATCH_NO');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('BOX_NO');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('TYPE');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('CDI_IMAGING_OPERATOR');?>
:</td> <td class="col-xs-6 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('DATE');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('EXPOSURE_OPERATOR');?>
:</td> <td class="col-xs-6 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('DATE');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('QUALITY_CONTROL');?>
:</td> <td class="col-xs-6 underlined">&nbsp;</td> <td class="col-xs-1">&nbsp;</td> <td class="col-xs-1"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('DATE');?>
:</td> <td class="col-xs-2 underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> <div class="row"> <div class="col-xs-12"> <table class="table table-borderless"> <tbody> <tr> <td class="col-xs-2"><?php echo $_smarty_tpl->tpl_vars['text']->value->__('ZLP_FP_REMARKS_01');?>
:</td> <td class="col-xs-10 underlined">&nbsp;</td> </tr> <tr> <td></td> <td>&nbsp;</td> </tr> <tr> <td></td> <td class="underlined">&nbsp;</td> </tr> </tbody> </table> </div> </div> </div>
	<?php }
}
}
