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

// Récupération du fichier includes de GLPI, permet l'accès au cœur
include ('../../../inc/includes.php');

//Affichage de l'entête GLPI (fonction native GLPI)
if ($_SESSION["glpiactiveprofile"]["interface"] == "central") {
   Html::header("Plugin FOG", $_SERVER['PHP_SELF'],"plugins","pluginfogpluginfogplugin","Fogplugin");
} else {
   Html::helpHeader("Plugin", $_SERVER['PHP_SELF']);
}

Search::show('PluginFogpluginFogplugin');
   
//Affichage du pied de page GLPI (fonction native GLPI)
HTML::footer();  

?>