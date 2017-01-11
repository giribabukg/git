<?php
class CJob_Art_Form extends CCust_Job_Art_Form {

protected function preSet() {
  if (strpos($this -> mAct, '.snew')) {
    $this-> mJob['creationdate'] = date('d-m-Y');
  }
}

}