<?php
session_start();
$page_index = true;
include_once('fonction.inc.php');
if(empty($page_fonction)) // si le chargement merde
	die('SECURITY ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');

if(!empty($_FILES['old-repo']) and $_FILES['old-repo']['error']==0 and $_FILES['old-repo']['size']<=1048576)
	$old = loader(switch_utf8_ascii(file_get_contents($_FILES['old-repo']['tmp_name']), 'UTF-8', 'ISO-8859-1//TRANSLIT'));
elseif(!empty($_POST))
{
	$old['const'] = !empty($_POST['const'])? switch_utf8_ascii(switch_utf8_ascii($_POST['const']), 'UTF-8', 'ISO-8859-1//TRANSLIT') : null;
	$old['data'] = !empty($_POST['data'])? switch_utf8_ascii(switch_utf8_ascii(sort_array($_POST['data'])), 'UTF-8', 'ISO-8859-1//TRANSLIT') : null;
	$old['remember'] = !empty($_POST['remember'])? $_POST['remember'] : null;
}
/**elseif(!empty($_COOKIE['old']))
	$old = $_COOKIE['old'];**/

//var_dump($_SESSION);die;
if(!empty($_POST['const']) and empty($_FILES['old-repo']['size']))
{
	$manga_array['const'] = $_POST['const'];
	if(($manga_array['data'] = sort_array($_POST['data'])))
	{
		if( ($ressource = parser_to_ressource(switch_utf8_ascii($manga_array))) )
		{
			/**if(!empty($_POST['remember']))
			{
				$manga_array['remember'] = true;
				set_cookie(switch_utf8_ascii($ressource, 'UTF-8', 'ISO-8859-1//TRANSLIT'),'old');
			}
			elseif(!empty($_COOKIE['old']))
				set_cookie($_COOKIE['old'], 'old', 0);**/
			set_download($ressource);
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Config-Gen</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" media="screen" type="text/css" title="style" href="design.css" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
</head>
<body>
	<div id="conten">
		<div id="header_img">
			<p><a href="index.php">Mavy</a></p>
		</div>
		<div id="corps">
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
		<h1>Fichier</h1>
		<p>
			<label>Votre fichier <kbd>rakshata-manga-2</kbd> actuel : <span class="help" title="Si vous avez d&eacute;j&agrave; un d&eacute;p&ocirc;t, chargez ici votre fichier 'rakshata-manga-2' (ou 'rakshata-manga-1') pour pr&eacute;-remplir le formulaire.">(?)</span></label>
			<br />
			<input type="file" name="old-repo" />
			<br />
			<input type="submit" value="charger" />
		</p>
		<h1>D&eacute;p&ocirc;t</h1>
		<p>
		<label>Nom long de votre d&eacute;p&ocirc;t (&lt; 26 caract&egrave;res) : <span class="red">*</span></label>
		<br />
		<input type="text" name="const[LONG_MANAGER_NAME]" <?php if(isset($old['const']['LONG_MANAGER_NAME']))echo 'value="'.$old['const']['LONG_MANAGER_NAME'].'"';?>/>
		<br />
		<label>Nom cours de votre d&eacute;p&ocirc;t (&lt; 6 caract&egrave;res) : <span class="red">*</span></label>
		<br />
		<input type="text" name="const[SHORT_MANAGER_NAME]" <?php if(isset($old['const']['SHORT_MANAGER_NAME']))echo 'value="'.$old['const']['SHORT_MANAGER_NAME'].'"';?>/>
		</p>
		<div class="separator"></div>
		<h1>S&eacute;rie</h1>
		<div id="list_serie">
		<?php
		$i = 0;
		do
		{
			?>
			<p id="line_<?php echo $i; ?>" class="hr">
			<a href="#" class="delet" onclick="delet_line('line_<?php echo $i; ?>'); return false;">(Supprimer)</a>
			<label>Nom long de votre s&eacute;rie (&lt; 51 caract&egrave;res) : <span class="red">*</span></label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][LONG_PROJECT_NAME]" <?php if(isset($old['data'][$i]['LONG_PROJECT_NAME']))echo 'value="'.$old['data'][$i]['LONG_PROJECT_NAME'].'"';?>/>
			<br />
			<label>Nom cours de votre s&eacute;rie (&lt; 11 caract&egrave;res) : <span class="red">*</span></label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][SHORT_PROJECT_NAME]" <?php if(isset($old['data'][$i]['SHORT_PROJECT_NAME']))echo 'value="'.$old['data'][$i]['SHORT_PROJECT_NAME'].'"';?>/>
			<br/>
			<label>Premier chapitre (vide si non-sorti) : </label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][FIRST_CHAPTER]" <?php if(isset($old['data'][$i]['FIRST_CHAPTER']) && $old['data'][$i]['FIRST_CHAPTER']>=0)echo 'value="'.$old['data'][$i]['FIRST_CHAPTER'].'"';?>/>
			<br/>
			<label>Dernier chapitre : </label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][LAST_CHAPTER]" <?php if(isset($old['data'][$i]['LAST_CHAPTER']) && $old['data'][$i]['LAST_CHAPTER']>=0)echo 'value="'.$old['data'][$i]['LAST_CHAPTER'].'"';?>/>
			<br/>
			<label>Premier tome (vide si non-sorti) :</label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][FIRST_TOME]" <?php if(isset($old['data'][$i]['FIRST_TOME']) && $old['data'][$i]['FIRST_TOME']>=0)echo 'value="'.$old['data'][$i]['FIRST_TOME'].'"';?>/>
			<br/>
			<label>Dernier tome : </label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][LAST_TOME]" <?php if(isset($old['data'][$i]['LAST_TOME']) && $old['data'][$i]['LAST_TOME']>=0)echo 'value="'.$old['data'][$i]['LAST_TOME'].'"';?>/>
			<br/>
			<label>&Eacute;tat de la s&eacute;rie : <span class="red">*</span></label>
			<br />
			<select name="data[<?php echo $i;?>][STATE]" >
				<option value="1" <?php if(!empty($old['data'][$i]['STATE']) && $old['data'][$i]['STATE']==1)echo 'selected="selected"';?>>En cours</option>
				<option value="2" <?php if(!empty($old['data'][$i]['STATE']) && $old['data'][$i]['STATE']==2)echo 'selected="selected"';?>>Suspendu</option>
				<option value="3" <?php if(!empty($old['data'][$i]['STATE']) && $old['data'][$i]['STATE']==3)echo 'selected="selected"';?>>Termin&eacute;</option>
			</select>
			<br/>
			<label>Type de la s&eacute;rie : <span class="red">*</span></label>
			<br />
			<select name="data[<?php echo $i;?>][GENDER]" >
				<option value="1" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==1)echo 'selected="selected"';?>>Shonen</option>
				<option value="2" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==2)echo 'selected="selected"';?>>Shojo</option>
				<option value="3" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==3)echo 'selected="selected"';?>>Seinen</option>
				<option value="4" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==4)echo 'selected="selected"';?>>Hentai (-16/-18)</option>
			</select>
			<br/>
			<label>Page d'information : <span class="help" title="Utilisez-vous une page 'info.png' pour cette s&eacute;rie ?">(?)</span></label>
			<br />
			<input type="checkbox" name="data[<?php echo $i;?>][INFOPNG]" <?php if(!empty($old['data'][$i]['INFOPNG']))echo 'checked="checked"';?>/><label>Oui</label>
			<br/>
			<label>Nombre de chapitre sp&eacute;ciaux : <span class="help" title="Avez-vous des inter-chapitre de type '10.5' ? Donnez le nombre de ces chapitres">(?)</span></label>
			<br />
			<input type="text" name="data[<?php echo $i;?>][CHAPTER_SPECIALS]" <?php if(isset($old['data'][$i]['CHAPTER_SPECIALS']))echo 'value="'.$old['data'][$i]['CHAPTER_SPECIALS'].'"';?>/>
			<br/>
			</p>
			<?php
			$i++;
		}
		while(isset($old['data'][$i]));
		echo "<script type=\"text/javascript\"><!--\n var i = ".$i.'; //--></script>';
		unset($i);
		?>
		</div>
		<p>
		<br />
		<input type="button" value="ajouter une s&eacute;rie" onclick="i = add_ligne(i);return false;"/>
		<br />
		<br />
		<?php /**<input type="checkbox" name="remember" id="remember" <?php echo !empty($old['remember'])? 'checked="checked"':'';?> /><label for="remember">Se souvenir </label> 
		<br />**/?>
		<input type="submit" value="cr&eacute;er" />
		</p>
		</form>
		</div>
		<div id="footer_img">
			<p>
				<a href="http://www.mozilla-europe.org/fr/firefox"><img src="get_firefox.png" alt="get firefox" title="Utitlisez Firefox" /></a>
				<a href="http://www.google.com/chrome"><img src="get_chrome.png" alt="get chrome" title="Utitlisez Chrome" /></a>
				<a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="checked_css.png" alt="W3C : CSS valide" title="W3C : CSS valide" /></a>
				<a href="http://validator.w3.org/check?uri=referer"> <img src="checked_xhtml.png" alt="W3C : XHtml valide" title="W3C : XHtml valide" /></a>
			</p>
		</div>
	</div>
<script type="text/javascript">
<!--
function add_ligne(i)
{
	var p = document.createElement("p");
	p.innerHTML="\t\t\t<a href=\"#\" class=\"delet\" onclick=\"delet_line('line_"+i+"'); return false;\">(Supprimer)</a>\n\t\t\t<label>Nom long de votre s&eacute;rie (&lt; 51 caract&egrave;res) : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][LONG_PROJECT_NAME]\" />\n\t\t\t<br />\n\t\t\t<label>Nom cours de votre s&eacute;rie (&lt; 11 caract&egrave;res) : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][SHORT_PROJECT_NAME]\" />\n\t\t\t<br/>\n\t\t\t<label>Premier chapitre (vide si non-sorti) : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][FIRST_CHAPTER]\" />\n\t\t\t<br/>\n\t\t\t<label>Dernier chapitre : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][LAST_CHAPTER]\" />\n\t\t\t<br/>\n\t\t\t<label>Premier tome (vide si non-sorti) :</label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][FIRST_TOME]\" />\n\t\t\t<br/>\n\t\t\t<label>Dernier tome : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][LAST_TOME]\" />\n\t\t\t<br/>\n\t\t\t<label>&Eacute;tat de la s&eacute;rie : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<select name=\"data["+i+"][STATE]\" >\n\t\t\t\t<option value=\"1\">En cours</option>\n\t\t\t\t<option value=\"2\">Suspendu</option>\n\t\t\t\t<option value=\"3\">Termin&eacute;</option>\n\t\t\t</select>\n\t\t\t<br/>\n\t\t\t<label>Type de la s&eacute;rie : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<select name=\"data["+i+"][GENDER]\" >\n\t\t\t\t<option value=\"1\">Shonen</option>\n\t\t\t\t<option value=\"2\">Shojo</option>\n\t\t\t\t<option value=\"3\">Seinen</option>\n\t\t\t\t<option value=\"4\">Hentai (-16/-18)</option>\n\t\t\t</select>\n\t\t\t<br/>\n\t\t\t<label>Page d'information : <span class=\"help\" title=\"Utilisez-vous une page 'info.png' pour cette s&eacute;rie ?\">(?)</span></label>\n\t\t\t<br />\n\t\t\t<input type=\"checkbox\" name=\"data["+i+"][INFOPNG]\" /><label>Oui</label>\n\t\t\t<br/>\n\t\t\t<label>Nombre de chapitre sp&eacute;ciaux : <span class=\"help\" title=\"Avez-vous des inter-chapitre de type '10.5' ? Donnez le nombre de ces chapitres\">(?)</span></label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data["+i+"][CHAPTER_SPECIALS]\" />\n\t\t\t<br/>\n\t\t\t";
	// l'ajoute à la fin
	p.className = 'hr';
	p.id = 'line_'+i;
	i++;
	document.getElementById("list_serie").appendChild(p);
	return i;
	
}
function delet_line(line_id)
{
	var list_serie = document.getElementById("list_serie");
	var old = document.getElementById(line_id);

	list_serie.removeChild(old);
}
//-->
</script>
</body>
</html>
<?php
echo '<pre>';
var_dump($_SESSION['error']);
echo '</pre>';
unset($_SESSION['error']);
?>
