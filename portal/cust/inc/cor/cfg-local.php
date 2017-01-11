<?php
    $this -> mVal['environment']      = 'fbnew';

    $this -> mVal['core.available']     = true;
    $this -> mVal['core.user']          = 'INT_WAVE_PD1'; // Basic auth for PO/PI Webservices
    $this -> mVal['core.pass']          = 'Sch@Wk2Pd!';
    $this -> mVal['core.wsdl']          = 'SalesOrderCreateLocalStub.wsdl';
    $this -> mVal['core.accept.system'] = 'ND1CLNT310,NQ1CLNT330,NP1CLNT300';

    $this -> mVal['validate.available'] = true;
    $this -> mVal['validate.global']    = true;

    $this -> mVal['composer.available']  = true;
    $this -> mVal['rabbit.available']    = true;
    $this -> mVal['rabbit.user']    = 'wave180';
    $this -> mVal['rabbit.pass']    = 'carrot';
    $this -> mVal['job-all.use-view']    = false;

    $this -> mVal['tpl.email']['pwd.fgt.activate'] = 553;

    $this -> mVal['cloudflow.available'] = true;

    $this -> mVal['log.master'] = '56712bd6c7fac393984f5545558f7a34';
