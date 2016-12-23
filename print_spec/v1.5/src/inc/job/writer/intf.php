<?php
interface IInc_Job_Writer_Intf {
  function __construct($aFields, $aSrc);
  function update($aJobId, $aValues);
  function insert($aValues);
}