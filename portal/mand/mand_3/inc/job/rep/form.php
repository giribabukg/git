<?php
class CJob_Rep_Form extends CCust_Job_Rep_Form {

protected function preSet() {
  if (strpos($this -> mAct, '.snew')) {
    $this-> mJob['creationdate'] = date('d-m-Y');
  }
}

}