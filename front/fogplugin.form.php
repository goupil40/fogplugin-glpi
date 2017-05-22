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

include ('../../../inc/includes.php');

if ($_SESSION["glpiactiveprofile"]["interface"] == "central") {
   Html::header("FOG Plugin", $_SERVER['PHP_SELF'],"plugins","pluginfogpluginfogplugin","");
} else {
   Html::helpHeader("TITRE", $_SERVER['PHP_SELF']);
}
$fogplugin = new PluginFogpluginFogplugin();

/* add */
if (isset ($_POST["add"])) {
   $fogplugin->check(-1, UPDATE ,$_POST);
   $newID = $fogplugin->add($_POST);

   Html::redirect(Toolbox::getItemTypeFormURL('PluginFogpluginFogplugin')."?id=$newID");
   
/* update */
} else if (isset ($_POST["update"])) {
   //Update server Fog
   $fogplugin->check($_POST['id'], UPDATE);
   $fogplugin->update($_POST);
   Html::back();

}

$fogplugin->display(array('id' =>$_GET["id"]));

Html::footer();

?>