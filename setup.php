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


// Init the hooks of the plugins -Needed
function plugin_init_fogplugin() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['fogplugin'] = true;
   
   // Display a menu entry ?
       if ($_SESSION["glpiactiveprofile"]["interface"]=="central"){
      $PLUGIN_HOOKS['menu_toadd']['fogplugin'] = array('plugins' => 'PluginFogpluginFogplugin',
                                                     'tools'   => 'PluginFogpluginFogtransfert');
      $PLUGIN_HOOKS["helpdesk_menu_entry"]['fogplugin'] = true;
   }
   
    $PLUGIN_HOOKS['change_profile']['fogplugin'] = 'plugin_change_profile_fogplugin';
    $PLUGIN_HOOKS['use_massive_action']['fogplugin'] = 1;
    
        // Css file
   $PLUGIN_HOOKS['add_css']['fogplugin'] = 'css/fogtransfert.css';

      // Javascript file
   $PLUGIN_HOOKS['add_javascript']['fogplugin'] = 'lib/fogtransfert.js';
}


// Get the name and the version of the plugin - Needed
function plugin_version_fogplugin() {

   return array('name'           => 'fogplugin',
                'version'        => '1.1',
                'author'         => 'DRIEA / CSR-I / PAT HD team developper',
                'license'        => 'GPLv2+',
                'homepage'       => '',
                'minGlpiVersion' => '0.85');// For compatibility / no install in version < 0.80
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_fogplugin_check_prerequisites() {

   // Strict version check (could be less strict, or could allow various version)
   if (version_compare(GLPI_VERSION,'0.85','lt') /*|| version_compare(GLPI_VERSION,'0.84','gt')*/) {
      echo "This plugin requires GLPI >= 0.85";
      return false;
   }
   return true;
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_fogplugin_check_config($verbose=false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      _e('Installed / not configured', 'fogplugin');
   }
   return false;
}
?>
