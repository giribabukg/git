<?php
class CInc_Api_Pixelboxx_Query_Deleteobject extends CApi_Pixelboxx_Query {
  
  protected function init() {
    $this->mMethod = 'deleteObject';
  }
  
  public function delete($aDoi) {
    $this->setParam('ObjectId', $aDoi);
    $lRes = $this->query($this->mMethod, $this->mParam);
    return $lRes;
  }

}