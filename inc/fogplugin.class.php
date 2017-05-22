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

// Class of the defined type
class PluginFogpluginFogplugin extends CommonDBTM {
   
   static $tags = '[FOGPLUGIN_ID]';
   static $rightname = 'entity';

   // Should return the localized name of the type
   static function getTypeName($nb = 0) {
      return 'Serveur Fog';
   }

   static function canCreate() {

       return Session::haveRight("entity", UPDATE);
         }
         
   static function canUpdate() {

       return Session::haveRight("entity", UPDATE);
   }

public static function canView()
   {
      return Session::haveRight("entity", UPDATE);
   }

   /**
    * @see CommonGLPI::getMenuName()
   **/
   static function getMenuName() {
      return __('Fogplugin');
   }
      
    function defineTabs($options = array()) {

      $ong = array();
      $this->addDefaultFormTab($ong);
      $this->addStandardTab(__CLASS__, $ong, $options);

      return $ong;
   }
   
   //formulaire saisie information de connexion Fog
    function showForm($ID, $options = array()) {
        
      global $DB;
      
      $this->initForm($ID, $options);
      $this->showFormHeader($options);
        
      echo '<table class="tab_cadre_fixe">
			<tr>
				<td>Site serveur FOG :</td><td><input type="text" name="name" value="'.$this->fields['name'].'"></td>
			</tr>
			<tr>
				<td>Adresse du serveur FOG :</td><td><input type="text" name="fog_address" value="'.$this->fields['fog_address'].'"></td>
			</tr>
			<tr>
				<td>Nom d\'utilisateur de la base de données de FOG : </td><td> <input type="text" name="user_db_fog" value="'.$this->fields['user_db_fog'].'"></td>
			</tr>
			<tr>			
				<td>Mot de passe de la base de données de FOG :</td><td> <input type="password" name="pass_db_fog" value="'.$this->fields['pass_db_fog'].'"></td>
			</tr>
			<tr>
				<td>Base de données de FOG :</td><td> <input type="text" name="name_db_fog" value="'.$this->fields['name_db_fog'].'"><br></td>
			</table>'."\n";     
      $this->showFormButtons($options);
      return true;
   }   
   
   function getSearchOptions() {

      $tab = array();
      $tab['common'] = "Modifer paramètre connexion Fog";

      $tab[1]['table']     = 'glpi_plugin_fogplugin_fogplugins';
      $tab[1]['field']     = 'name';
      $tab[1]['name']      = __('Nom du site');
      $tab[1]['massiveaction']   = true;

      $tab[2]['table']     = 'glpi_plugin_fogplugin_fogplugins';
      $tab[2]['field']     = 'fog_address';
      $tab[2]['name']      = __('Adresse serveur');
      $tab[2]['usehaving'] = true;
      $tab[2]['searchtype'] = 'equals';

      $tab[30]['table']     = 'glpi_plugin_fogplugin_fogplugins';
      $tab[30]['field']     = 'id';
      $tab[30]['name']      = __('ID');

      return $tab;
   }
   //////////////////////////////
////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////
   /**
    * @since version 0.85
    *
    * @see CommonDBTM::getSpecificMassiveActions()
   **/
   function getSpecificMassiveActions($checkitem=NULL) {

      $actions = parent::getSpecificMassiveActions($checkitem);

      $actions[__CLASS__.MassiveAction::CLASS_ACTION_SEPARATOR.'delete_fog'] =
                                        __('supprimer serveur fog', 'fogplugin');  // Specific one

      return $actions;
   }

   /**
    * @since version 0.85
    *
    * @see CommonDBTM::showMassiveActionsSubForm()
   **/
   static function showMassiveActionsSubForm(MassiveAction $ma) {

      switch ($ma->getAction()) {
         case 'delete_fog' :
            echo "&nbsp;".Html::submit(_x('button','Supprimer'), array('name' => 'massiveaction')).
                 " ".__('', 'fogplugin');
            return true;
    }
      return parent::showMassiveActionsSubForm($ma);
   }

   /**
    * @since version 0.85
    *
    * @see CommonDBTM::processMassiveActionsForOneItemtype()
   **/
   static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item, array $ids) {
      global $DB;

      switch ($ma->getAction()) {
         case 'delete_fog' :
            If ($item->getType() == 'PluginFogpluginFogplugin') {
               Session::addMessageAfterRedirect(__("serveur(s) supprimé(s) :", 'fogplugin'));
               foreach ($ids as $id) {
                  if ($item->getFromDB($id)) {
                     Session::addMessageAfterRedirect("- ".$item->getField("name"));
                     $query = "DELETE FROM `glpi_plugin_fogplugin_fogplugins` WHERE `id` = '".$id."'";
                     $DB->query($query) or die($DB->error());
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                  } else {
                     // Example for noright / Maybe do it with can function is better
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_KO);
                  }
               }
            } else {
               $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
            }
            Return;
      }
      parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
   }
   
   function prepareInputForAdd($input) {

      // Controle la saisie obligatoire :
      if(isset($input['name'])) {
         if (empty($input['name'])) {
            Session::addMessageAfterRedirect(__('Le nom du site est obligatoire', 'fogplugin'), false, ERROR);
            return array();
         }
         $input['name'] = addslashes($input['name']);
      }
      if(isset($input['fog_address'])) {
         if (empty($input['fog_address'])) {
            Session::addMessageAfterRedirect(__('L\'adesse du serveur est obligatoire' , 'fogplugin'), false, ERROR);
            return array();
         }
         $input['fog_address'] = addslashes($input['fog_address']);
      }
      if(isset($input['user_db_fog'])) {
         if (empty($input['user_db_fog'])) {
            Session::addMessageAfterRedirect(__('Le nom d\'utilisateur est obligatoire', 'fogplugin'), false, ERROR);
            return array();
         }
         $input['user_db_fog'] = addslashes($input['user_db_fog']);
      }
      if(isset($input['pass_db_fog'])) {
         if (empty($input['pass_db_fog'])) {
            Session::addMessageAfterRedirect(__('Le mot de passe est obligatoire', 'fogplugin'), false, ERROR);
            return array();
         }
         $input['pass_db_fog'] = addslashes($input['pass_db_fog']);
      }
      if(isset($input['name_db_fog'])) {
         if (empty($input['name_db_fog'])) {
            Session::addMessageAfterRedirect(__('Le nom de la base de données est obligatoire', 'fogplugin'), false, ERROR);
            return array();
         }
         $input['name_db_fog'] = addslashes($input['name_db_fog']);
      }
      return $input;
   }
   
   public function prepareInputForUpdate($input)
   {
         return $input;
   }

}
?>