<?php
/*
 * @version $Id: HEADER 15930 2017-05-14 09:00:00Z cds $
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   fogplugin
 @author    DRIEA / CSR-I / PAT HD team developper
 @copyright Copyright (c) 2016 - 2017 Fogplugin team
 @license   GPLv2+
            http://www.gnu.org/licenses/gpl.txt
 @link      http://www.glpi-project.org/
 @since     2017
 ---------------------------------------------------------------------- */

 class PluginFogpluginSession {

   /**
    * Get a parameter from the HTTP session
    *
    * @param $param  the parameter to get
    *
    * @return the param's value
   **/
   static function getParam($param) {

      if (!isset($_SESSION['fogplugin'][$param])) {
         return false;
      }
      if (in_array($param, array('results', 'error_lines'))) {
         $fic = $_SESSION['fogplugin'][$param];
         return file_get_contents(GLPI_DOC_DIR.'/_tmp/'.$fic);
      }
      return $_SESSION['fogplugin'][$param];
   }

   /**
    * Set a parameter in the HTTP session
    *
    * @param $param     the parameter
    * @param $results   the value to store
    *
    * @return nothing
   **/
   static function setParam($param,$results) {

      if (in_array($param, array('results', 'error_lines'))) {
         $fic = Session::getLoginUserID().'_'.$param.'_'.microtime(true);
         file_put_contents(GLPI_DOC_DIR.'/_tmp/'.$fic, $results);
         $_SESSION['fogplugin'][$param] = $fic;
      } else {
         $_SESSION['fogplugin'][$param] = $results;
      }
   }
   /**
    * Remove all parameters from the HTTP session
    *
    * @return nothing
    */
   static function removeParams() {

      if (isset($_SESSION['fogplugin']['results'])) {
         unlink(GLPI_DOC_DIR.'/_tmp/'.$_SESSION['fogplugin']['results']);
      }
      if (isset($_SESSION['fogplugin']['error_lines'])) {
         unlink(GLPI_DOC_DIR.'/_tmp/'.$_SESSION['fogplugin']['error_lines']);
      }
      unset($_SESSION['fogplugin']);
   }

}
?>