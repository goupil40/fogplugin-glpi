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

// Direct access to file
if (strpos($_SERVER['PHP_SELF'],"dropdownSelectFog.php")) {
   include ('../../../inc/includes.php');
   header("Content-Type: text/html; charset=UTF-8");
   Html::header_nocache();
}

Session::checkCentralAccess();

if (isset($_SESSION['fogplugin']['fogplugins_id'])
      && $_SESSION['fogplugin']['fogplugins_id']!=$_POST['fogplugins_id']) {
   PluginFogpluginFogtransfert::cleanSessionVariables();
}

$_SESSION['fogplugin']['step'] = PluginFogpluginFogtransfert::STEP_UPLOAD;
$server = new PluginFogpluginFogtransfert();
if ($_POST['fogplugins_id'] > 0)
 {
   PluginFogpluginFogtransfert::showAdditionalInformationsForm($server);
}

?>