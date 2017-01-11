<?php
class CInc_Api_Core_Query_Createsalesorder extends CApi_Core_Query {

    protected function init() {
        $this->mMethod = 'SI_SalesOrderCreate_Sync_OB';
        $this->setDefaults();
    }

    protected function setDefaults() {
    }

    public function create() {
        try {
            $lRes = $this->query($this->mMethod, $this->mParam);
        } catch (Exception $ex) {
            $this->msg($ex->getMessage(), mtApi, mlError);
            return false;
        }
        return $lRes;
    }

}
