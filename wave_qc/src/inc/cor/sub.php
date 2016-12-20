<?php
/**
 * Core: Subject Observer
 *
 * Subject Interface for observer pattern
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */

/**
 * Subject Interface for observer pattern
 *
 */
interface ICor_Sub {

  /**
   * Add an observer that will be notified when the state of the subject changes
   *
   * @param ICor_Obs $aObserver
   */
  public function subscribe(ICor_Obs $aObserver);


  /**
   * Remove an observer from the list of objects that will be notfied when the
   * state of the subject changes (stop buggin' me with your update! ;-)
   *
   * @param ICor_Obs $aObserver
   */
  public function unsubscribe(ICor_Obs $aObserver);

}