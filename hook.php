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

function plugin_fogplugin_giveItem($type,$ID,$data,$num) {
   $searchopt = &Search::getOptions($type);
   $table = $searchopt[$ID]["table"];
   $field = $searchopt[$ID]["field"];

   switch ("$table.$field") {
      case "glpi_plugin_fogplugin_fogplugins.name" :
         $out = "<a href='".Toolbox::getItemTypeFormURL('PluginFogpluginFogplugin')."?id=".$data['id']."'>";
         $out .= $data[$num][0]['name'];
         if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
            $out .= " (".$data["id"].")";
         }
         $out .= "</a>";
         return $out;
   }
   return "";
}

function plugin_fogplugin_displayConfigItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table     = $searchopt[$ID]["table"];
   $field     = $searchopt[$ID]["field"];
   switch ("$table.$field") {
      case "glpi_plugin_fogplugin_fogplugins.name" :
         return " style=\"background-color:#DDDDDD;\" ";
   }
   return "";
}

// Install process for plugin : need to return true if succeeded
function plugin_fogplugin_install() {
   global $DB;
  
   if (!TableExists("glpi_plugin_fogplugin_fogplugins")) {
      $query = "CREATE TABLE `glpi_plugin_fogplugin_fogplugins` (
                  `id` int(11) NOT NULL auto_increment,
                  `name` varchar(255) collate utf8_unicode_ci default NULL,
                  `fog_address` varchar(20) collate utf8_unicode_ci NOT NULL,
                  `user_db_fog` varchar(30) collate utf8_unicode_ci NOT NULL,
                  `pass_db_fog` varchar(30) collate utf8_unicode_ci NOT NULL,
                  `name_db_fog` varchar(255) collate utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

      $DB->query($query) or die("error creating glpi_plugin_fogplugin_fogplugins ". $DB->error());

   }
   return true;
}

// Uninstall process for plugin : need to return true if succeeded
function plugin_fogplugin_uninstall() {
   global $DB;
   
   // Current version tables
   if (TableExists("glpi_plugin_fogplugin_fogplugins")) {
      $query = "DROP TABLE `glpi_plugin_fogplugin_fogplugins`";
      $DB->query($query) or die("error deleting glpi_plugin_fogplugin_fogplugins");
   }
   return true;
}
?>
