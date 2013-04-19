<?php
session_start();
$page_index = true;
include_once('fonction.inc.php');
if(empty($page_fonction)) // si le chargement merde
	die('SECURITY ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');

if(!empty($_POST))
{
	$manga_array = $_POST;
	if(($sorted_array = sort_array($_POST['data'])))
	{
		$manga_array['data'] = $sorted_array;
		if( ($ressource = parser_to_ressource(utf8_to_ascii($manga_array))) )
			set_download($ressource);
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
	<script type="text/javascript">
		<!--
		function add_ligne()
		{
			// Crée un nouvel element
			var span = document.createElement("span");
			span.innerHTML="\t\t\t<hr />\n\t\t\t<label for=\"LONG_PROJECT_NAME\">Nom long de votre s&eacute;rie (&lt; 51 caract&egrave;res) : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[LONG_PROJECT_NAME][]\" id=\"LONG_PROJECT_NAME\" />\n\t\t\t<br />\n\t\t\t<label for=\"SHORT_PROJECT_NAME\">Nom cours de votre s&eacute;rie (&lt; 11 caract&egrave;res) : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[SHORT_PROJECT_NAME][]\" id=\"SHORT_PROJECT_NAME\" />\n\t\t\t<br/>\n\t\t\t<label for=\"FIRST_CHAPTER\">Premier chapitre (vide si non-sorti) : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[FIRST_CHAPTER][]\" id=\"FIRST_CHAPTER\" />\n\t\t\t<br/>\n\t\t\t<label for=\"LAST_CHAPTER\">Dernier chapitre : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[LAST_CHAPTER][]\" id=\"LAST_CHAPTER\" />\n\t\t\t<br/>\n\t\t\t<label for=\"FIRST_TOME\">Premier tome (vide si non-sorti) :</label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[FIRST_TOME][]\" id=\"FIRST_TOME\" />\n\t\t\t<br/>\n\t\t\t<label for=\"LAST_TOME\">Dernier tome : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[LAST_TOME][]\" id=\"LAST_TOME\" />\n\t\t\t<br/>\n\t\t\t<label for=\"STATE\">&Eacute;tat : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<select name=\"data[STATE][]\" id=\"STATE\" >\n\t\t\t\t<option value=\"1\">En cours</option>\n\t\t\t\t<option value=\"2\">Suspendu</option>\n\t\t\t\t<option value=\"3\">Termin&eacute;</option>\n\t\t\t</select>\n\t\t\t<br/>\n\t\t\t<label for=\"GENDER\">Type : <span class=\"red\">*</span></label>\n\t\t\t<br />\n\t\t\t<select name=\"data[GENDER][]\" id=\"GENDER\" >\n\t\t\t\t<option value=\"1\">Shonen</option>\n\t\t\t\t<option value=\"2\">Shojo</option>\n\t\t\t\t<option value=\"3\">Seinen</option>\n\t\t\t\t<option value=\"4\">Hentai (-16/-18)</option>\n\t\t\t</select>\n\t\t\t<br/>\n\t\t\tPage d'information : \n\t\t\t<br />\n\t\t\t<input type=\"checkbox\" name=\"data[INFOPNG][]\" id=\"INFOPNG\" /><label for=\"INFOPNG\">Oui</label>\n\t\t\t<br/>\n\t\t\t<label for=\"CHAPTER_SPECIALS\">Nombre de chapitre sp&eacute;ciaux (\"10.5\", \"20.1\", ...) : </label>\n\t\t\t<br />\n\t\t\t<input type=\"text\" name=\"data[CHAPTER_SPECIALS][]\" id=\"CHAPTER_SPECIALS\" />\n\t\t\t<br/>";
			// l'ajoute à la fin du document
			document.getElementById("list_serie").appendChild(span);
		}
		//-->
		</script>
</head>
<body>
	<div id="conten">
		<div id="header_img">
			<p><a href="index.php">Mavy</a></p>
		</div>
		<div id="corps">
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
		<p>
		<?php /*<em>Fichier</em>
		<br />
		<label for="old-repo">Votre fichier <kbd>rakshata-manga-2</kbd> actuel : </label>
		<br />
		<input id="old-repo" type="file" name="old-repo" />
		<br />
		<input type="submit" value="charger" />
		<br />*/?>
		<em>D&eacute;p&ocirc;t</em>
		<br />
		<label for="long-repo">Nom long de votre d&eacute;p&ocirc;t (&lt; 26 caract&egrave;res) : <span class="red">*</span></label>
		<br />
		<input type="text" name="const[NOM_MANAGER_LONG]" id="long-repo" />
		<br />
		<label for="short-repo">Nom cours de votre d&eacute;p&ocirc;t (&lt; 6 caract&egrave;res) : <span class="red">*</span></label>
		<br />
		<input type="text" name="const[NOM_MANAGER_SHORT]" id="short-repo" />
		<br />
		</p>
		<div class="separator"></div>
		<p>
		<em>S&eacute;rie</em>
		<br />
		<span id="list_serie">
			<span>
			<label for="LONG_PROJECT_NAME">Nom long de votre s&eacute;rie (&lt; 51 caract&egrave;res) : <span class="red">*</span></label>
			<br />
			<input type="text" name="data[LONG_PROJECT_NAME][]" id="LONG_PROJECT_NAME" />
			<br />
			<label for="SHORT_PROJECT_NAME">Nom cours de votre s&eacute;rie (&lt; 11 caract&egrave;res) : <span class="red">*</span></label>
			<br />
			<input type="text" name="data[SHORT_PROJECT_NAME][]" id="SHORT_PROJECT_NAME" />
			<br/>
			<label for="FIRST_CHAPTER">Premier chapitre (vide si non-sorti) : </label>
			<br />
			<input type="text" name="data[FIRST_CHAPTER][]" id="FIRST_CHAPTER" />
			<br/>
			<label for="LAST_CHAPTER">Dernier chapitre : </label>
			<br />
			<input type="text" name="data[LAST_CHAPTER][]" id="LAST_CHAPTER" />
			<br/>
			<label for="FIRST_TOME">Premier tome (vide si non-sorti) :</label>
			<br />
			<input type="text" name="data[FIRST_TOME][]" id="FIRST_TOME" />
			<br/>
			<label for="LAST_TOME">Dernier tome : </label>
			<br />
			<input type="text" name="data[LAST_TOME][]" id="LAST_TOME" />
			<br/>
			<label for="STATE">&Eacute;tat : <span class="red">*</span></label>
			<br />
			<select name="data[STATE][]" id="STATE" >
				<option value="1">En cours</option>
				<option value="2">Suspendu</option>
				<option value="3">Termin&eacute;</option>
			</select>
			<br/>
			<label for="GENDER">Type : <span class="red">*</span></label>
			<br />
			<select name="data[GENDER][]" id="GENDER" >
				<option value="1">Shonen</option>
				<option value="2">Shojo</option>
				<option value="3">Seinen</option>
				<option value="4">Hentai (-16/-18)</option>
			</select>
			<br/>
			Page d'information : 
			<br />
			<input type="checkbox" name="data[INFOPNG][]" id="INFOPNG" /><label for="INFOPNG">Oui</label>
			<br/>
			<label for="CHAPTER_SPECIALS">Nombre de chapitre sp&eacute;ciaux ("10.5", "20.1", ...) : </label>
			<br />
			<input type="text" name="data[CHAPTER_SPECIALS][]" id="CHAPTER_SPECIALS" />
			<br/>
			</span>
		</span>
		<br />
		<input type="button" value="ajouter une s&eacute;rie" onclick="add_ligne();return false;"/>
		<br />
		<br />
		<input type="submit" value="cr&eacute;er" />
		</p>
		</form>
		</div>
		<div id="footer_img">
			<p></p>
		</div>
	</div>
</body>
</html>

