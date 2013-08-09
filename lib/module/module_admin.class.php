<?php
/**
 * Třída pro obsluhu vlastností mmodulu
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu vlastností modulu
 */

class Module_Admin extends Module
{
   protected $coreModule = true;  // vypíná aktualizace, protože ty jsou obsaženy v aktualizaci jádra, zatím nevyužito
}
