<?php
class CInc_Rep_Highcharts extends CCor_Ren {
  
  protected $mTyp;
  protected $mParams;
  protected $mTitleStart;
  protected $mTitleEnd;
  
  public function __construct($aTyp) {
    $this -> mTyp = $aTyp;
    $this -> mTitleStart = '';
    $this -> mTitleEnd = '';
    
    $this -> setParams();
  }
  
  public function setTitle($aTitle) {
    $this -> mTitleStart = $aTitle;
    $this -> mTitleEnd = '</div>';
  }
  
  public function setCategories($aCats) {
    $this -> mParams['xAxis']["categories"] = $aCats;
  }
  
  public function setSeries($aData) {
    $this -> mParams['series'] = $aData;
  }
  
  public function removeParam($aIndex) {
    if(array_key_exists($aIndex, $this -> mParams)){
      unset($this -> mParams[$aIndex]);
    }
  }
  
  public function setParams($aParams = array()) {
    if(empty($aParams)){
      $this -> mParams = array(
        "chart" => array("type" => $this -> mTyp),
        "title" => array("text" => ""),
        "plotOptions" => array( 
          $this -> mTyp => array( 
            "stacking" => "normal", 
            "tooltip" => array(
              "headerFormat" => "<span style=\"font-size: 14px\">{point.x}</span><br/>",
          	  "pointFormat" => "<span style=\"color:{series.color}\">{series.name}</span>: <b>{point.name:.0f}</b> ({point.percentage:.0f}%)<br/>",
    		),
          )
        ),
        "tooltip" => array("shared" => "true"),
        "credits" => array("enabled" => "false"), 
        "legend" => array("align" => "right", "verticalAlign" => "top", "layout" => "vertical")
      );
      
      if($this -> mTyp !== "pie"){
        $this -> mParams['xAxis'] = array(
          'labels' => array('rotation' => '-90'), 
          'title' => array("text" => ""), 
        );
      }
    } else {
      $this -> mParams = array_replace_recursive($this -> mParams, $aParams);
    }
  }
  
  public function getContent($lAttr = array()) {
    if(empty($lAttr)){
      $lAttr = array("class" => "fl w100p", "id" => "container");
    }
    
    $lRet = '<div class="'.$lAttr['class'].'">';
    $lRet.= $this -> mTitleStart;
    $lRet.= '<div id="'.$lAttr['id'].'" style="height:400;width:350;"></div>';
    $lRet.= '<script>';
    $lRet.= '  window.onload = function(){';
    $lRet.= 'jQuery("#'.$lAttr['id'].'").highcharts({';
    $lRet.= $this -> buildParams($this -> mParams);
    $lRet.= '});';
    $lRet.= '}
      jQuery(window).trigger("load");
      </script></div>';
    $lRet.= $this -> mTitleEnd;

    return $lRet;
  }
  
  protected function buildParams($aValues) {
    $lRet = '';
    $lBools = array('true','false');
    
    foreach($aValues as $lKey => $lVal){
      if(is_array($lVal)){
        
        if($lKey == 'categories'){
          $lRet.= $lKey.": [ ";
          foreach($lVal as $lSubkey => $lSubval) {
            $lRet.= '"'.$lSubval.'", ';
          }
          $lRet = substr($lRet, 0, strlen($lRet)-1);
          $lRet.= "], ";
        } else if($lKey == 'series'){
          $lRet.= $lKey.": [ ";
          foreach($lVal as $lSubkey => $lSubval){
            $lRet.= '{ name: "'.$lSubval['name'].'", ';
            $lRet.= (array_key_exists('type', $lSubval)) ? 'type: "'.$lSubval['type'].'", ' : '';
            $lRet.= (array_key_exists('color', $lSubval)) ? 'color: "'.$lSubval['color'].'", ' : '';
            $lRet.= (array_key_exists('yAxis', $lSubval)) ? 'yAxis: '.$lSubval['yAxis'].', ' : '';
            $lRet.= (array_key_exists('stack', $lSubval)) ? 'stack: "'.$lSubval['stack'].'", ' : '';
            $lRet.= 'data: ['; 
            foreach($lSubval['data'] as $lIdx => $lValue) { 
              if(is_array($lValue)){
                if($this -> mTyp == 'pie'){
                  $lRet .= " [ ";
                  foreach($lValue as $lDatakey => $lDataval){
                    $lRet.= (is_numeric($lDataval) || in_array($lDataval, $lBools)) ? $lDataval.', ' : '"'.addslashes($lDataval).'", ';
                  }
                  $lRet.= "], ";
                } else {
                  $lRet .= " { ";
                  foreach($lValue as $lDatakey => $lDataval){
                    $lRet.= $lDatakey.': ';
                    $lRet.= (is_numeric($lDataval) || in_array($lDataval, $lBools)) ? $lDataval.', ' : '"'.addslashes($lDataval).'", ';
                  }
                  $lRet.= "}, ";
                }
              } else {
                $lRet.= $lValue.',';
              }
            }
            $lRet = substr($lRet, 0, strlen($lRet)-1);
            $lRet.= '] }, ';
          }
          $lRet.= ' ], ';
        } else {
          $lRet.= $lKey.": { ";
          $lRet.= $this -> buildParams($lVal);
          $lRet.= "}, ";
        }
        
      } else {
        $lBools = array('true','false','null');
        if(is_numeric($lVal) || in_array($lVal, $lBools) || strpos($lVal, '[') === 0){
          $lRet.= $lKey.': '.$lVal.', ';
        } else {
          $lRet.= $lKey.': "'.addslashes($lVal).'", ';
        }
      }
    }

    return $lRet;
  }
  
}