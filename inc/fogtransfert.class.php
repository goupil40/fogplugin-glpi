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

class PluginFogpluginFogtransfert extends CommonDBTM {
    
    const STEP_UPLOAD  = 0;
    private $server;
    
// Should return the localized name of the type
   static function getTypeName($nb = 0) {
      return 'Fog Transfert';
   }
   
    static function getMenuName() {
      return __('Fog Transfert');
   }
   // permet d'afficher le plugin dans le menu et d'y accèder
   static function canView() {

      if (isset($_SESSION["glpi_plugin_fogplugin_profile"])) {
         return ($_SESSION["glpi_plugin_fogplugin_profile"]['fogplugin'] == 'w'
                 || $_SESSION["glpi_plugin_fogplugin_profile"]['fogplugin'] == 'r');
      }
      return false;
   }
  // affiche le menu déroulant et prépare la suite de l'affichage
   function showForm($ID, $options=array()) {
      global $CFG_GLPI;

      echo "<form method='post' name=form action='".Toolbox::getItemTypeFormURL(__CLASS__)."'".
            "enctype='multipart/form-data'>";
      echo "<div class='center'>";
      echo "<table class='tab_cadre_fixe'>";

      $serverfog = self::getServers();

      echo "<tr><th>" .__('Choix du serveur Fog', 'fogplugin') ."</th></tr>";

      echo "<tr class='tab_bg_1'>";
      if (count($serverfog) > 0) {
         echo "<td class='center'>".__('Serveur Fog')."&nbsp;";
         self::dropdown();
      } 
      echo "</td></tr></table><br>";
      echo "<span id='span_transfert' name='span_transfert'></span>";
      Html::closeForm();
      echo "</div>";
      if (PluginFogpluginSession::getParam('fogplugins_id')) {
         $p['fogplugins_id'] = PluginFogpluginSession::getParam('fogplugins_id');

         switch (PluginFogpluginSession::getParam('step')) {
            case self::STEP_UPLOAD :
               $url = $CFG_GLPI["root_doc"]."/plugins/fogplugin/ajax/dropdownSelectFog.php";
               Ajax::updateItem("span_transfert", $url, $p);
               break;
         }
      }
   }
   
    static function getServers() {
      global $DB;
      $servers = array ();
      $query = "SELECT *
                FROM `glpi_plugin_fogplugin_fogplugins` ";
      foreach ($DB->request($query) as $data) {
         $servers[] = $data;
      }
      return $servers;
   }
   // création menu déroulant dynamique
   static function dropdown($options=array()) {
      global $CFG_GLPI;

      $servers = self::getServers();
      $p      = array('fogplugins_id' => '__VALUE__');

      if (isset($_SESSION['fogplugin']['fogplugins_id'])) {
         $value = $_SESSION['fogplugin']['fogplugins_id'];
      } else {
         $value = 0;
      }
      $rand = mt_rand();
      echo "\n<select name='dropdown_servers' id='dropdown_servers$rand'>";
      $prev = -2;
      echo "\n<option value='0'>".Dropdown::EMPTY_VALUE."</option>";

      foreach ($servers as $server) {

         if ($server['id'] == $value) {
            $selected = "selected";
         } else {
            $selected = "";
         }
         if ($server['fog_address']) {
            $address = "title='".htmlentities($server['fog_address'], ENT_QUOTES, 'UTF-8')."'";
         } else {
            $address = "";
         }
         echo "\n<option value='".$server['id']."' $selected $address >".$server['name']."</option>";
      }
      if ($prev >= -1) {
         echo "</optgroup>";
      }
      echo "</select>";      
      $url = $CFG_GLPI["root_doc"]."/plugins/fogplugin/ajax/dropdownSelectFog.php";
      Ajax::updateItemOnSelectEvent("dropdown_servers$rand", "span_transfert", $url, $p);
   }
   // suite de l'affiche showForm en fonction du choix dropdown, affichage des postes informatique présents et à ajouter dans Fog
   static function showAdditionalInformationsForm(PluginFogpluginFogtransfert $server) {
        global $DB;
        $query = "SELECT * FROM glpi_plugin_fogplugin_fogplugins where `id`='".$_POST['fogplugins_id']."'";
	$result = $DB->query($query);
		while($row = $DB->fetch_assoc($result)) 
		{
                    $config['site_fog'] = $row['name'];
                    $config['fog_address'] = $row['fog_address'];
                    $config['user_db_fog'] = $row['user_db_fog'];
                    $config['pass_db_fog'] = $row['pass_db_fog'];
                    $config['name_db_fog'] = $row['name_db_fog'];
		}
        $mysqli_fog = new mysqli($config['fog_address'],$config['user_db_fog'],$config['pass_db_fog'],$config['name_db_fog']);
        $db_fog = mysql_connect($config['fog_address'], $config['user_db_fog'], $config['pass_db_fog']); 
        //test connexion à Fog
            if($mysqli_fog->connect_error)
            {
                 echo "<b>Erreur retournée :</b> <span class='mysql_error_green'>'".$mysqli_fog->connect_error."'</span><br><br>"."\n";   				
            }
            else {
         echo "<link rel='stylesheet' href='css/fogtransfert.css?v='".time()."' type='text/css'>";
         echo "<table class='tab_cadre_fixe'>";
         echo "<tr><th colspan='2'>".sprintf(__('tableau des ordinateurs', 'fogplugin'));
         echo "</th></tr>\n";
         echo "<tr class='tab_bg_1'>";
         echo "<td colspan='2'><img src=\"../pics/fog-logo.png\" alt=\"FOG\" style=\"margin: 0px 120px\"><br>";
         echo "</td></tr>";
         echo "<tr>";
         echo "<td colspan='2' class='center'>";
         echo "<center>";
         //////////////////////////////////// Affichage des postes informatique présents et à ajouter dans Fog ///////////////////////////////////
            $requete_pcs_glpi = "SELECT glpi_computers.name, glpi_networkports.mac FROM glpi_computers, glpi_networkports WHERE glpi_computers.id = glpi_networkports.items_id AND glpi_computers.is_deleted = 0 AND glpi_networkports.mac != '' AND glpi_networkports.logical_number = '1' ORDER BY glpi_computers.name";
            $query_pcs_glpi = $DB->query($requete_pcs_glpi);
            while($pcs_glpi = $query_pcs_glpi->fetch_array(MYSQLI_ASSOC))
            {
                    $glpi[] = $pcs_glpi;
            }

            mysql_select_db($config['name_db_fog'],$db_fog);
                    if (!$db_fog) {
                    echo "Unable to connect to DB: " . mysql_error();
                    exit;
                    }
                    if (!mysql_select_db($config['name_db_fog'])) {
                    echo "Unable to select mydbname: " . mysql_error();
                    exit;
                    }
            $requete_pcs_fog = "select hosts.hostname, hostMAC.hmMac from hosts, hostMAC where hosts.hostid = hostMAC.hmHostID order by hosts.hostname";
            $query_pcs_fog = mysql_query($requete_pcs_fog);
                    if (!$query_pcs_fog) {
                    echo "Could not successfully run query ($requete_pcs_fog) from DB: " . mysql_error();
                    exit;
                    }
                    if (mysql_num_rows($query_pcs_fog) == 0) {
                    echo "No rows found, nothing to print so am exiting";
                    exit;
                    }
            while($pcs_fog = mysql_fetch_assoc($query_pcs_fog))
            {
                    $fog_hostName[] = $pcs_fog['hostname'];
                    $fog_hostMAC[] = $pcs_fog['hmMac'];
            }
            mysql_close();
            // Compteurs
            $compteur_orange = 0;
            $compteur_red = 0;
            for($i = 0; $i < sizeof($glpi); $i++)
            {
                    if(array_search($glpi[$i]['name'], $fog_hostName) !== array_search($glpi[$i]['mac'], $fog_hostMAC))
                    {
                            $compteur_orange = $compteur_orange + 1;
                    }
            }
            for($i = 0; $i < sizeof($glpi); $i++)
            {
                    if(!array_search($glpi[$i]['name'], $fog_hostName) and !in_array($glpi[$i]['mac'], $fog_hostMAC))
                    {
                            $compteur_red = $compteur_red + 1;
                    }
            }
            $compteur_green = sizeof($glpi)-$compteur_orange-$compteur_red;
            // Compteurs
            if(sizeof($glpi) < 1)
            {
                    echo '<div id="fogbox" class="grey">
                    <br>&nbsp; &nbsp;<b>Actuellement dans FOG ('.sizeof($fog_hostName).')</b>&nbsp; &nbsp;<a href="#contenu_grey" onclick="contenu_grey()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
                    <div id="contenu_grey" style="display:block;">
                    <table border="0">'."\n";
                    for($i = 0; $i < sizeof($fog_hostName); $i++)
                    {
                            echo "<tr>"."\n";
                            echo '<td width="175">'.$fog_hostName[$i].'</td><td>adresse MAC '.strtoupper($fog_hostMAC[$i]).'<br></td>'."\n";
                            echo "</tr>"."\n";

                    }
                    echo '</table>
                    </div>
                    </div>'."\n";
            }
            else
            {
                    echo '<div id="fogbox" class="green">
                    <br>&nbsp; &nbsp;<b>Déjà présents dans FOG ('.$compteur_green.')</b>&nbsp; &nbsp;<a href="#contenu_green" onclick="contenu_green()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
                    <div id="contenu_green" style="display:none;">
                    <table border="0">'."\n";
                    for($i = 0; $i < sizeof($glpi); $i++)
                    {
                            //if(array_search($glpi[$i]['name'], $fog_hostName) and in_array($glpi[$i]['mac'], $fog_hostMAC))
                            if(in_array($glpi[$i]['name'], $fog_hostName))
                            {

                                     if(in_array($glpi[$i]['mac'], $fog_hostMAC))
                            {
                                    echo "<tr>"."\n";
                                    echo '<td width="175">'.$glpi[$i]['name'].'</td><td>adresse MAC '.strtoupper($glpi[$i]['mac']).'<br></td>'."\n";
                                    echo "</tr>"."\n";
                            }
                            }
                    }
                    if($compteur_orange > 0)
                    {
                            $display_orange = "block";
                    }
                    else
                    {
                            $display_orange = "none";
                    }
                    echo '</table>
                    </div>
                    </div>
                    <div id="fogbox" class="orange">
                    <br>&nbsp; &nbsp;<b>Requièrent votre attention ('.$compteur_orange.')</b>&nbsp; &nbsp;<a href="#contenu_orange" onclick="contenu_orange()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
                    <div id="contenu_orange" style="display:'.$display_orange.';">'."\n";
                    for($i = 0; $i < sizeof($glpi); $i++)
                    {
                            if(array_search($glpi[$i]['name'], $fog_hostName) !== array_search($glpi[$i]['mac'], $fog_hostMAC))
                            {
                                    if(array_search($glpi[$i]['name'], $fog_hostName) == null)
                                    {
                                            echo 'L\'adresse MAC '.strtoupper($glpi[$i]['mac']).' a été trouvée mais n\'est pas liée à '.substr($glpi[$i]['name'], 0, 16).'<br>'."\n";
                                    }
                                    elseif(array_search($glpi[$i]['mac'], $fog_hostMAC) == null)
                                    {
                                            echo $glpi[$i]['name'].' a été trouvé mais n\'est pas lié à l\'adresse MAC '.strtoupper($glpi[$i]['mac']).'<br>'."\n";
                                    }
                                    else
                                    {
                                            echo substr($glpi[$i]['name'], 0, 16).', adresse MAC '.$glpi[$i]['mac'].') - Erreur inconnue<br>'."\n";
                                    }
                            }
                    }
                    if($compteur_red > 0)
                    {
                            $display_red = "block";
                    }
                    else
                    {
                            $display_red = "none";
                    }
                    echo '</div>
                    </div>
                    <div id="fogbox" class="red">
                    <br>&nbsp; &nbsp;<b>Pouvant être ajoutés à FOG ('.$compteur_red.')</b>&nbsp; &nbsp;<a href="#contenu_red" onclick="contenu_red()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
                    <div id="contenu_red" style="display:'.$display_red.';">'."\n";
                    if($compteur_red > 0)
                    {
                        $idfog= $_POST['fogplugins_id'];
                            echo 'Les PCs suivants n\'ont pas été trouvés dans FOG, sélectionnez quels PCs vous souhaitez ajouter :<br><br>
                            <form action="fogtransfert.php" method="get">
                            <input type="hidden" name="fog_add_hosts[]" value="'.$idfog.'">
                            <table border="0">
                            <tr>
                            <td width = "360">';
                            echo "<br>";
                            echo "<a href=\"#contenu_red\" onclick=\"select_all();\" class=\"lien_afficher_masquer\">Sélectionner tout</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href=\"#contenu_red\" onclick=\"unselect_all();\" class=\"lien_afficher_masquer\">Déselectionner tout</a><br><br></td>";
                            echo '</tr>
                            '."\n";
                    }
                    for($i = 0; $i < sizeof($glpi); $i++)
                    {
                            if(!array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) and !in_array($glpi[$i]['mac'], $fog_hostMAC))
                            {
                                    echo "<tr>"."\n";
                                    echo '<td width="175"><input type="checkbox" name="checkbox[]" value="'.$glpi[$i]['name'].'||'.$glpi[$i]['mac'].'"> '.$glpi[$i]['name'].'</td><td>adresse MAC '.strtoupper($glpi[$i]['mac']).'</td>'."\n";
                                    echo "</tr>"."\n";
                            }
                    }
                    if($compteur_red > 0)
                    {
                            echo '</table>';
                            echo '<br><input class="fogsbmit" type="submit" value="Ajouter sélectionné(s)">
                            </form>'."\n";
                    }
                    echo '</div>
                    </div>'."\n";
            }                       
         ////////////////////////////////////////////
            echo "</center>";
                                echo "</td></tr>";
         echo "</table><br>";
        }

                    }

    static function cleanSessionVariables() {

      //Reset parameters stored in session
      PluginFogpluginSession::removeParams();
      PluginFogpluginSession::setParam('infos', array());
   }
}
?>