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
   Html::header("Plugin FOG", $_SERVER['PHP_SELF'],"plugins","pluginfogpluginfogtransfert","Fogplugin");
} else {
   Html::helpHeader("Plugin", $_SERVER['PHP_SELF']);
}
$fogtransfert = new PluginFogpluginFogtransfert();

////////////////////// Transfert des postes informatique dans la base de Fog ////////////////////////////////
if(isset($_GET['fog_add_hosts']))
                        {
    $idfog = $_GET['fog_add_hosts'];
    
    // récupération information de connexion au serveur Fog
    $query = "SELECT * FROM glpi_plugin_fogplugin_fogplugins where `id`='".$idfog['0']."'";
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
        // récupération de l'utilisateur qui effectue le transfert
        $user = $_SESSION["glpirealname"];
        $user = $user." ";
        $user = $user.$_SESSION["glpifirstname"];
        
        echo "<div class='center'>";
        echo "<tr>";
         echo "<td colspan='2' class='center'>";
         // test la connexion au serveur Fog
            if($mysqli_fog->connect_error)
            {
                 echo "<b>Erreur retournée :</b> <span class='mysql_error_green'>'".$mysqli_fog->connect_error."'</span><br><br>"."\n";
            }
            else {
				if(isset($_GET['checkbox']))
				{
					$checkbox = $_GET['checkbox'];
                                        echo "<center>";
					echo '<div id="fogbox" class="green">
					<br>&nbsp; &nbsp;<b>Viennent d\'être exportés vers FOG ('.sizeof($checkbox).')</b><br><br>
					<div id="contenu_green">
					<table border="0">'."\n";
					mysql_select_db($config['name_db_fog'],$db_fog);
                                        mysql_query("SET NAMES UTF8");
				 	for($i = 0; $i < sizeof($checkbox); $i++)
					{
						$explode = explode('||', $checkbox[$i]);
						$name = $explode[0];
						$mac = $explode[1];
						mysql_query("INSERT INTO hosts (hostID, hostName, hostDesc, hostImage, hostBuilding, hostCreateDate, hostLastDeploy, hostCreateBy, hostSecTime, hostPingCode) VALUES ('','".$name."','importé depuis GLPI le ".date("d/m/Y à H:i:s", time())." par ".$user."','0','0', '".date("Y-m-d H:i:s", time())."', '0000-00-00 00:00:00', 'GLPI', '0000-00-00 00:00:00', '6')");
						$IDhost=mysql_insert_id();
						mysql_query("INSERT INTO hostMAC (hmID, hmHostID, hmMAC, hmPrimary, hmPending) VALUES ('', '".$IDhost."', '".$mac."','1', '')");
						if($mysqli_fog == true)
						{
							echo "<tr>"."\n";
							echo '<td width="175">'.$name.' id ' .$IDhost.';</td><td>adresse MAC '.strtoupper($mac).'</td>';
							echo "</tr>"."\n";
						}
					}
					mysql_close();
					echo '</table>
					<br>
					<input type="submit" onclick="location.replace(\'fogtransfert.php\');" value="c\'est envoyé !">
					</div>
					</div>';
                                        echo "</center>";
				}
				else
				{
					redirection("fogtransfert.php");
				}
                        }
                        echo "</tr>";
         echo "</td>";
         echo "</div>";
                        }
    ////////////////////////////////////////////////////////////////////
 else {
$fogtransfert->showForm();
 }                
//Affichage du pied de page GLPI (fonction native GLPI)
HTML::footer();  

?>