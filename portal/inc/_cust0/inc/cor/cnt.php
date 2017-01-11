<?php
/**
 * Customer - Core: Base controller
 *
 * Provides dispatch mechanism, Request convenience methods
 * and some common actions like sorting, paging and searching
 *
 * Base class for custom controllers
 * Provides often used standard actions like
 * sort order, page navigation and search in
 * lists.
 * The main routine dispatch will delegate the
 * request to a method according to the POST/GET
 * variable 'act'.
 *
 * @example index.php?act=usr.edt will create an instance of CUsr_Cnt and call its actEdt() method.
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CCust_Cor_Cnt extends CInc_Cor_Cnt {

}