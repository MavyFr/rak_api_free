<?php
session_start();
$curent_version = '0.9 [beta]';
$page_index = true;
include_once('fonction.inc.php');
if(empty($page_fonction)) // si le chargement merde
	die('SECURITY ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');

if(empty($_SESSION['hello']))
	log_f('hello', 'from `'.$_SERVER['HTTP_USER_AGENT'].'`');
$_SESSION['hello'] = true;

if (get_magic_quotes_gpc())
	mq_stripslashes(); // on vire l effet magic-quote

if(!empty($_FILES['old-repo']) and $_FILES['old-repo']['error']==0 and $_FILES['old-repo']['size']<=1048576)
{
	log_f('`loader`', 'from file '.$_FILES['old-repo']['size'].'o / '.substr_count(file_get_contents($_FILES['old-repo']['tmp_name']), "\n").' `\n`');
	$old = loader(switch_utf8_ascii(file_get_contents($_FILES['old-repo']['tmp_name']), 'UTF-8', 'ISO-8859-1//TRANSLIT'));
}
elseif(!empty($_POST))
{
	$old['const'] = !empty($_POST['const'])? switch_utf8_ascii(switch_utf8_ascii($_POST['const']), 'UTF-8', 'ISO-8859-1//TRANSLIT') : null;
	$old['data'] = !empty($_POST['data'])? switch_utf8_ascii(switch_utf8_ascii(sort_array($_POST['data'])), 'UTF-8', 'ISO-8859-1//TRANSLIT') : null;
	$old['remember'] = !empty($_POST['remember'])? $_POST['remember'] : null;
}
elseif(!empty($_COOKIE['old']))
{
	$old = $_COOKIE['old'];
	log_f('`loader`', 'from COOKIE');
}
// si on a un envois, on lance la procedure de set download
if(!empty($_POST['const']) and !empty($_POST['data']) and empty($_FILES['old-repo']['size']))
{
	$manga_array['const'] = $_POST['const'];
	if(($manga_array['data'] = sort_array($_POST['data'])))
	{
		if( ($ressource = parser(switch_utf8_ascii($manga_array))) )
		{
			if(empty($_SESSION['error']))
			{
				if(!empty($_POST['remember']))
				{
					$manga_array['remember'] = true;
					log_f('`set_download`', 'with option `remember`');
					set_cookie(switch_utf8_ascii(switch_utf8_ascii($manga_array), 'UTF-8', 'ISO-8859-1//TRANSLIT'),'old');
				}
				elseif(!empty($_COOKIE['old']))
					set_cookie($_COOKIE['old'], 'old', 0);
				set_download($ressource);
			}
			else
				log_f('ERROR DATA', 'can\'t set_download');
		}
		else
			log_f('ERROR DATA', 'can\'t set_download');
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Generateur d'index pour depot gratuit Rakshata: v<?php echo $curent_version;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" media="screen" type="text/css" title="style" href="design.css" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
</head>
<body>
	<div id="conten">
		<p id="header_img">
			<a href="index.php">Mavy</a>
		</p>
		<div id="corps">
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
		<h1>Fichier</h1>
		<p>
			<label for="old-repo">Votre fichier de configuration <kbd>rakshata-manga-2</kbd> actuel : </label>
				<?php help("Si vous avez d&eacute;j&agrave; un d&eacute;p&ocirc;t, chargez ici votre fichier 'rakshata-manga-2' (ou 'rakshata-manga-1') pour pr&eacute;-remplir le formulaire.");?>
			<br />
			<input type="file" name="old-repo" id="old-repo" />
			<br />
			<input type="submit" value="charger" />
		</p>
		<h1>D&eacute;p&ocirc;t</h1>
		<p id="depot">
			<?php if(!empty($_SESSION['error']['LONG_MANAGER_NAME'])) show_error($_SESSION['error']['LONG_MANAGER_NAME']);?>
			<label for="LONG_MANAGER_NAME">Nom long de votre d&eacute;p&ocirc;t (25 caract&egrave;res max) : <span class="red">*</span></label>
			<br />
			<input type="text" id="LONG_MANAGER_NAME" name="const[LONG_MANAGER_NAME]" <?php 
				if(isset($old['const']['LONG_MANAGER_NAME']))echo 'value="'.$old['const']['LONG_MANAGER_NAME'].'"';?>/>
			<br />
			<?php if(!empty($_SESSION['error']['SHORT_MANAGER_NAME'])) show_error($_SESSION['error']['SHORT_MANAGER_NAME']);?>
			<label for="SHORT_MANAGER_NAME">Nom court de votre d&eacute;p&ocirc;t (10 caract&egrave;res max) : <span class="red">*</span></label>
			<br />
			<input type="text" id="SHORT_MANAGER_NAME" name="const[SHORT_MANAGER_NAME]" <?php 
				if(isset($old['const']['SHORT_MANAGER_NAME']))echo 'value="'.$old['const']['SHORT_MANAGER_NAME'].'"';?>/>
		</p>
		<div class="separator"></div>
		<h1>S&eacute;rie</h1>
		<div id="list_serie">
		<?php
		if(!empty($_SESSION['error']['data'])){ echo '<p>'; show_error($_SESSION['error']['data']); echo '</p>';}
		$i = 0;
		do
		{
			?>
			<div id="line_<?php echo $i;?>" class="hr">
			<h2 class="tree_head" onclick="show(this, <?php echo $i;?>);"><span class="tree" ><?php
				if(!empty($old['data'][$i]['LONG_PROJECT_NAME']) and empty($_SESSION['error'][$i])) echo '+|';else echo '-|';?></span><?php 
				if(isset($old['data'][$i]['LONG_PROJECT_NAME']))echo $old['data'][$i]['LONG_PROJECT_NAME'];?></h2>
			<p class="delet">
				<a href="#" onclick="delet_line('line_<?php echo $i;?>'); return false;">Supprimer</a>
			</p>
			<?php if(!empty($_SESSION['error'][$i]['champs'])) show_error($_SESSION['error'][$i]['champs']);?>
			<?php if(!empty($_SESSION['error'][$i]['LONG_PROJECT_NAME'])) show_error($_SESSION['error'][$i]['LONG_PROJECT_NAME']);?>
			<p class="break_float" id="show_<?php echo $i;?>" <?php
			 if(!empty($old['data'][$i]['LONG_PROJECT_NAME']) and empty($_SESSION['error'][$i])) echo 'style="display:none;"';?> >
				<span class="tree" >&nbsp;|</span>
				<label for="LONG_PROJECT_NAME_<?php echo $i;?>">
					Nom long de votre s&eacute;rie (50 caract&egrave;res max) : <span class="red">*</span></label>
				<input type="text" id="LONG_PROJECT_NAME_<?php echo $i;?>" name="data[<?php echo $i;?>][LONG_PROJECT_NAME]" 
					onchange="refresh(this, <?php echo $i;?>)"<?php 
					if(isset($old['data'][$i]['LONG_PROJECT_NAME']))echo 'value="'.$old['data'][$i]['LONG_PROJECT_NAME'].'"';?>/>
				<br />
				<?php if(!empty($_SESSION['error'][$i]['SHORT_PROJECT_NAME'])) show_error($_SESSION['error'][$i]['SHORT_PROJECT_NAME']);?>
				<span class="tree" >&nbsp;|</span>
				<label for="SHORT_PROJECT_NAME_<?php echo $i;?>">
					Nom court de votre s&eacute;rie (10 caract&egrave;res max) : <span class="red">*</span></label>
				<input type="text" id="SHORT_PROJECT_NAME_<?php echo $i;?>" name="data[<?php echo $i;?>][SHORT_PROJECT_NAME]" <?php 
					if(isset($old['data'][$i]['SHORT_PROJECT_NAME']))echo 'value="'.$old['data'][$i]['SHORT_PROJECT_NAME'].'"';?>/>
				<br/>
				<?php if(!empty($_SESSION['error'][$i]['sortie'])) show_error($_SESSION['error'][$i]['sortie']);?>
				<?php if(!empty($_SESSION['error'][$i]['chapitre'])) show_error($_SESSION['error'][$i]['chapitre']);?>
				<?php if(!empty($_SESSION['error'][$i]['FIRST_CHAPTER'])) show_error($_SESSION['error'][$i]['FIRST_CHAPTER']);?>
				<span class="tree" >&nbsp;|</span>
				<label for="FIRST_CHAPTER_<?php echo $i;?>">Premier chapitre sorti (vide si non-sorti) : </label>
				<input type="text" id="FIRST_CHAPTER_<?php echo $i;?>" name="data[<?php echo $i;?>][FIRST_CHAPTER]" <?php 
					if(isset($old['data'][$i]['FIRST_CHAPTER']) && $old['data'][$i]['FIRST_CHAPTER']>=0)
						echo 'value="'.$old['data'][$i]['FIRST_CHAPTER'].'"';?>/>
				<br/>
				<?php if(!empty($_SESSION['error'][$i]['LAST_CHAPTER'])) show_error($_SESSION['error'][$i]['LAST_CHAPTER']);?>
				<span class="tree" >&nbsp;|</span>
				<label for="LAST_CHAPTER_<?php echo $i;?>">Dernier chapitre sorti : </label>
				<input type="text" id="LAST_CHAPTER_<?php echo $i;?>" name="data[<?php echo $i;?>][LAST_CHAPTER]" <?php 
					if(isset($old['data'][$i]['LAST_CHAPTER']) && $old['data'][$i]['LAST_CHAPTER']>=0)
						echo 'value="'.$old['data'][$i]['LAST_CHAPTER'].'"';?>/>
				<br/>
				<?php if(!empty($_SESSION['error'][$i]['tome'])) show_error($_SESSION['error'][$i]['tome']);?>
				<?php if(!empty($_SESSION['error'][$i]['FIRST_TOME'])) show_error($_SESSION['error'][$i]['FIRST_TOME']);?>
				<span class="tree" >&nbsp;|</span>
				<label for="FIRST_TOME_<?php echo $i;?>">Premier tome sorti (vide si non-sorti) :</label>
				<input type="text" id="FIRST_TOME_<?php echo $i;?>" name="data[<?php echo $i;?>][FIRST_TOME]" <?php 
					if(isset($old['data'][$i]['FIRST_TOME']) && $old['data'][$i]['FIRST_TOME']>=0)
						echo 'value="'.$old['data'][$i]['FIRST_TOME'].'"';?>/>
				<br/>
				<?php if(!empty($_SESSION['error'][$i]['LAST_TOME'])) show_error($_SESSION['error'][$i]['LAST_TOME']);?>
				<span class="tree" >&nbsp;|</span>
				<label for="LAST_TOME_<?php echo $i;?>">Dernier tome sorti : </label>
				<input type="text" id="LAST_TOME_<?php echo $i;?>" name="data[<?php echo $i;?>][LAST_TOME]" <?php 
					if(isset($old['data'][$i]['LAST_TOME']) && $old['data'][$i]['LAST_TOME']>=0)
						echo 'value="'.$old['data'][$i]['LAST_TOME'].'"';?>/>
				<br/>
				<span class="tree" >&nbsp;|</span>
				<label for="STATE_<?php echo $i;?>">&Eacute;tat de la s&eacute;rie : <span class="red">*</span></label>
				<select id="STATE_<?php echo $i;?>" name="data[<?php echo $i;?>][STATE]" >
					<option value="1" <?php if(!empty($old['data'][$i]['STATE']) && $old['data'][$i]['STATE']==1)
						echo 'selected="selected"';?>>En cours</option>
					<option value="2" <?php if(!empty($old['data'][$i]['STATE']) && $old['data'][$i]['STATE']==2)
						echo 'selected="selected"';?>>Suspendu</option>
					<option value="3" <?php if(!empty($old['data'][$i]['STATE']) && $old['data'][$i]['STATE']==3)
						echo 'selected="selected"';?>>Termin&eacute;</option>
				</select>
				<br/>
				<span class="tree" >&nbsp;|</span>
				<label for="GENDER_<?php echo $i;?>">Type de la s&eacute;rie : <span class="red">*</span></label>
				<select id="GENDER_<?php echo $i;?>" name="data[<?php echo $i;?>][GENDER]" >
					<option value="1" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==1)
						echo 'selected="selected"';?>>Shonen</option>
					<option value="2" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==2)
						echo 'selected="selected"';?>>Shojo</option>
					<option value="3" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==3)
						echo 'selected="selected"';?>>Seinen</option>
					<option value="4" <?php if(!empty($old['data'][$i]['GENDER']) && $old['data'][$i]['GENDER']==4)
						echo 'selected="selected"';?>>Hentai (-16/-18)</option>
				</select>
				<br/>
				<span class="tree" >&nbsp;|</span>
				<label>Page d'information : </label><?php help("Utilisez-vous une page 'info.png' pour cette s&eacute;rie ?");?>
				<input type="checkbox" id="INFOPNG_<?php echo $i;?>" name="data[<?php echo $i;?>][INFOPNG]" <?php 
					if(!empty($old['data'][$i]['INFOPNG']))echo 'checked="checked"';?>/>
					<label for="INFOPNG_<?php echo $i;?>">Oui</label>
				<br/>
				<?php if(!empty($_SESSION['error'][$i]['CHAPTER_SPECIALS'])) show_error($_SESSION['error'][$i]['CHAPTER_SPECIALS']);?>
				<span class="tree" >&nbsp;|</span>
				<label for="CHAPTER_SPECIALS_<?php echo $i;?>">Nombre de chapitre sp&eacute;ciaux : </label>
					<?php help("Avez-vous des inter-chapitres de type '10.5' ? Donnez le nombre de ces chapitres");?>
				<input type="text" id="CHAPTER_SPECIALS_<?php echo $i;?>" name="data[<?php echo $i;?>][CHAPTER_SPECIALS]" <?php 
					if(isset($old['data'][$i]['CHAPTER_SPECIALS']))
						echo 'value="'.$old['data'][$i]['CHAPTER_SPECIALS'].'"';?>/>
			</p>
			</div>
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
		<input type="checkbox" name="remember" id="remember" <?php if(!empty($old['remember']))echo'checked="checked"';?> />
			<label for="remember">Se souvenir de moi ! </label><?php 
			help("Les informations de ce d&eacute;p&ocirc;t serons retenues par votre ordinateur pour pr&eacute;-remplir le formulaire &agrave; votre prochaine visite.");?>
			(<a href="#" onclick="forget(); return false;">m'oublier</a>)
		<br />
		<input type="submit" value="cr&eacute;er" />
		</p>
		</form>
		</div>
		<p id="footer_img">
			Copyright Mavy <a href="http://www.mozilla-europe.org/fr/firefox"><img src="get_firefox.png" alt="get firefox" title="Site optimis&eacute; pour Firefox" /></a> Conception par Blag
		</p>
	</div>
<script type="text/javascript">
<!--
autofocus();
///////////////
function forget()
{
	var array_cookie = document.cookie.split(';'); 
	var today = new Date();
	for (var id in array_cookie)
	{
		var obj_cookie = array_cookie[id].split('=');
		if(obj_cookie[0] != 'PHPSESSID')
			document.cookie = obj_cookie[0]+"="+null+";expires=" + today.toGMTString();
		document.getElementById("remember").checked = null;
	}
}
function show(obj, id)
{
	var show = document.getElementById("show_"+id);
	var long_name = document.getElementById("LONG_PROJECT_NAME_"+id);
	if(obj.getElementsByClassName('tree')[0].innerHTML.charAt(0)=='+')
	{
		obj.innerHTML='<span class="tree" >-|</span>'+long_name.value;
		show.style.display = '';
	}
	else
	{
		obj.innerHTML='<span class="tree" >+|</span>'+long_name.value;
		show.style.display = 'none';
	}
}
function refresh(obj, id)
{
	document.getElementById("line_"+id).getElementsByClassName('tree_head')[0].innerHTML='<span class="tree" >-|</span>'+obj.value;
}
function autofocus()
{
	var a = true;
	var elem = new Array('LONG_PROJECT_NAME_', 'SHORT_PROJECT_NAME_', 'FIRST_CHAPTER_', 'LAST_CHAPTER_', 'FIRST_TOME_', 
				'LAST_TOME_', 'STATE_', 'GENDER_', 'INFOPNG_', 'CHAPTER_SPECIALS_');
	for(var i = 0; i < document.getElementById('list_serie').getElementsByClassName('hr').length && a; i++)
	{
		if(document.getElementById('list_serie').getElementsByClassName('hr')[i].getElementsByClassName('error').length)
		{
			a = false;
			var tree = -1;
			for(var t=0; t < document.getElementById('list_serie').getElementsByClassName('hr')[i].getElementsByTagName('span').length; t++)
			{
				if(document.getElementById('list_serie').getElementsByClassName('hr')[i].getElementsByTagName('span')[t].className == 'tree')
					tree++;
				else if(document.getElementById('list_serie').getElementsByClassName('hr')[i].getElementsByTagName('span')[t].className == 'error')
				{
					document.getElementById(elem[tree]+i).focus()
					break;
				}
			}
		}
	}
}
function add_ligne(i)
{
	var conten = document.createElement('div');
		conten.className = 'hr';
		conten.id = 'line_'+i;
	
	var tree_head = document.createElement('h2');
		tree_head.className = 'tree_head';
		tree_head.setAttribute("onclick", "show(this, "+i+");");
	
	var tree_head_sub_tree = document.createElement('span');
		tree_head_sub_tree.className = 'tree';	
		tree_head_sub_tree.innerHTML = "-|";
	
	tree_head.appendChild(tree_head_sub_tree);
	conten.appendChild(tree_head);
	
	var delet = document.createElement('p');
		delet.className = 'delet';
	
	var a = document.createElement('a');
		a.href = "#";
		a.setAttribute("onclick", "delet_line('line_"+i+"'); return false;");
		a.innerHTML = "Supprimer";
	
	delet.appendChild(a);
	conten.appendChild(delet);
	
	var show = document.createElement("p");
		show.id = "show_"+i;
		show.className = 'break_float';
	
	conten.appendChild(show);
	
	var span_tree_LONG_PROJECT_NAME = document.createElement('span');
		span_tree_LONG_PROJECT_NAME.className = 'tree';
		span_tree_LONG_PROJECT_NAME.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_LONG_PROJECT_NAME);
	
	var label_LONG_PROJECT_NAME = document.createElement("label");
		label_LONG_PROJECT_NAME.setAttribute("for", "LONG_PROJECT_NAME_"+i);
		label_LONG_PROJECT_NAME.innerHTML = "Nom long de votre s&eacute;rie (50 caract&egrave;res max) : ";
	
	var span_LONG_PROJECT_NAME = document.createElement('span');
		span_LONG_PROJECT_NAME.className = 'red';
		span_LONG_PROJECT_NAME.innerHTML = "*";
	
	label_LONG_PROJECT_NAME.appendChild(span_LONG_PROJECT_NAME);
	show.appendChild(label_LONG_PROJECT_NAME);
	
	var input_LONG_PROJECT_NAME = document.createElement("input");
		input_LONG_PROJECT_NAME.type = "text";
		input_LONG_PROJECT_NAME.id = "LONG_PROJECT_NAME_"+i;
		input_LONG_PROJECT_NAME.name = "data["+i+"][LONG_PROJECT_NAME]";
		input_LONG_PROJECT_NAME.setAttribute("onchange", "refresh(this, "+i+");");
	
	show.appendChild(input_LONG_PROJECT_NAME);
	show.appendChild(document.createElement('br'));
	
	var span_tree_SHORT_PROJECT_NAME = document.createElement('span');
		span_tree_SHORT_PROJECT_NAME.className = 'tree';
		span_tree_SHORT_PROJECT_NAME.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_SHORT_PROJECT_NAME);
	
	var label_SHORT_PROJECT_NAME = document.createElement("label");
		label_SHORT_PROJECT_NAME.setAttribute("for", "SHORT_PROJECT_NAME_"+i);
		label_SHORT_PROJECT_NAME.innerHTML = "Nom court de votre s&eacute;rie (10 caract&egrave;res max) : ";
	
	var span_SHORT_PROJECT_NAME = document.createElement('span');
		span_SHORT_PROJECT_NAME.className = 'red';
		span_SHORT_PROJECT_NAME.innerHTML = "*";
	
	label_SHORT_PROJECT_NAME.appendChild(span_SHORT_PROJECT_NAME);
	show.appendChild(label_SHORT_PROJECT_NAME);
	
	var input_SHORT_PROJECT_NAME = document.createElement("input");
		input_SHORT_PROJECT_NAME.type = "text";
		input_SHORT_PROJECT_NAME.id = "SHORT_PROJECT_NAME_"+i;
		input_SHORT_PROJECT_NAME.name = "data["+i+"][SHORT_PROJECT_NAME]";
	
	show.appendChild(input_SHORT_PROJECT_NAME);
	show.appendChild(document.createElement('br'));
	
	var span_tree_FIRST_CHAPTER = document.createElement('span');
		span_tree_FIRST_CHAPTER.className = 'tree';
		span_tree_FIRST_CHAPTER.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_FIRST_CHAPTER);
	
	var label_FIRST_CHAPTER = document.createElement("label");
		label_FIRST_CHAPTER.setAttribute("for", "FIRST_CHAPTER_"+i);
		label_FIRST_CHAPTER.innerHTML = "Premier chapitre (vide si non-sorti) : ";
	
	show.appendChild(label_FIRST_CHAPTER);
	
	var input_FIRST_CHAPTER = document.createElement("input");
		input_FIRST_CHAPTER.type = "text";
		input_FIRST_CHAPTER.id = "FIRST_CHAPTER_"+i;
		input_FIRST_CHAPTER.name = "data["+i+"][FIRST_CHAPTER]";
	
	show.appendChild(input_FIRST_CHAPTER);
	show.appendChild(document.createElement('br'));
	
	var span_tree_LAST_CHAPTER = document.createElement('span');
		span_tree_LAST_CHAPTER.className = 'tree';
		span_tree_LAST_CHAPTER.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_LAST_CHAPTER);
	
	var label_LAST_CHAPTER = document.createElement("label");
		label_LAST_CHAPTER.setAttribute("for", "LAST_CHAPTER_"+i);
		label_LAST_CHAPTER.innerHTML = "Dernier chapitre : ";
	
	show.appendChild(label_LAST_CHAPTER);
	
	var input_LAST_CHAPTER = document.createElement("input");
		input_LAST_CHAPTER.type = "text";
		input_LAST_CHAPTER.id = "LAST_CHAPTER_"+i;
		input_LAST_CHAPTER.name = "data["+i+"][LAST_CHAPTER]";
	
	show.appendChild(input_LAST_CHAPTER);
	show.appendChild(document.createElement('br'));
	
	var span_tree_FIRST_TOME = document.createElement('span');
		span_tree_FIRST_TOME.className = 'tree';
		span_tree_FIRST_TOME.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_FIRST_TOME);
	
	var label_FIRST_TOME = document.createElement("label");
		label_FIRST_TOME.setAttribute("for", "FIRST_TOME_"+i);
		label_FIRST_TOME.innerHTML = "Premier tome (vide si non-sorti) : ";
	
	show.appendChild(label_FIRST_TOME);
	
	var input_FIRST_TOME = document.createElement("input");
		input_FIRST_TOME.type = "text";
		input_FIRST_TOME.id = "FIRST_TOME_"+i;
		input_FIRST_TOME.name = "data["+i+"][FIRST_TOME]";
	
	show.appendChild(input_FIRST_TOME);
	show.appendChild(document.createElement('br'));
	
	var span_tree_LAST_TOME = document.createElement('span');
		span_tree_LAST_TOME.className = 'tree';
		span_tree_LAST_TOME.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_LAST_TOME);
	
	var label_LAST_TOME = document.createElement("label");
		label_LAST_TOME.setAttribute("for", "LAST_TOME_"+i);
		label_LAST_TOME.innerHTML = "Dernier tome : ";
	
	show.appendChild(label_LAST_TOME);
	
	var input_LAST_TOME = document.createElement("input");
		input_LAST_TOME.type = "text";
		input_LAST_TOME.id = "LAST_TOME_"+i;
		input_LAST_TOME.name = "data["+i+"][LAST_TOME]";
	
	show.appendChild(input_LAST_TOME);
	show.appendChild(document.createElement('br'));
	
	var span_tree_STATE = document.createElement('span');
		span_tree_STATE.className = 'tree';
		span_tree_STATE.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_STATE);
	
	var label_STATE = document.createElement("label");
		label_STATE.setAttribute("for", "STATE_"+i);
		label_STATE.innerHTML = "&Eacute;tat de la s&eacute;rie : ";
	
	var span_STATE = document.createElement('span');
		span_STATE.className = 'red';
		span_STATE.innerHTML = "*";
	
	label_STATE.appendChild(span_STATE);
	show.appendChild(label_STATE);
	
	var select_STATE = document.createElement("select");
		select_STATE.id = "STATE_"+i;
		select_STATE.name = "data["+i+"][STATE]";
	
	show.appendChild(select_STATE);
	show.appendChild(document.createElement('br'));
	
	var option_STATE_1 = document.createElement("option");
		option_STATE_1.value = "1";
		option_STATE_1.innerHTML = "En cours";
	
	select_STATE.appendChild(option_STATE_1);
	
	var option_STATE_2 = document.createElement("option");
		option_STATE_2.value = "2";
		option_STATE_2.innerHTML = "Suspendu";
	
	select_STATE.appendChild(option_STATE_2);
	
	var option_STATE_3 = document.createElement("option");
		option_STATE_3.value = "3";
		option_STATE_3.innerHTML = "Termin&eacute;";
	
	select_STATE.appendChild(option_STATE_3);
	
	var span_tree_GENDER = document.createElement('span');
		span_tree_GENDER.className = 'tree';
		span_tree_GENDER.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_GENDER);
	
	var label_GENDER = document.createElement("label");
		label_GENDER.setAttribute("for", "GENDER_"+i);
		label_GENDER.innerHTML = "Type de la s&eacute;rie : ";
	
	var span_GENDER = document.createElement('span');
		span_GENDER.className = 'red';
		span_GENDER.innerHTML = "*";
	
	label_GENDER.appendChild(span_GENDER);
	show.appendChild(label_GENDER);
	
	var select_GENDER = document.createElement("select");
		select_GENDER.id = "GENDER_"+i;
		select_GENDER.name = "data["+i+"][GENDER]";
	
	show.appendChild(select_GENDER);
	show.appendChild(document.createElement('br'));
	
	var option_GENDER_1 = document.createElement("option");
		option_GENDER_1.value = "1";
		option_GENDER_1.innerHTML = "Shonen";
	
	select_GENDER.appendChild(option_GENDER_1);
	
	var option_GENDER_2 = document.createElement("option");
		option_GENDER_2.value = "2";
		option_GENDER_2.innerHTML = "Shojo";
	
	select_GENDER.appendChild(option_GENDER_2);
	
	var option_GENDER_3 = document.createElement("option");
		option_GENDER_3.value = "3";
		option_GENDER_3.innerHTML = "Seinen";
	
	select_GENDER.appendChild(option_GENDER_3);
	
	var option_GENDER_4 = document.createElement("option");
		option_GENDER_4.value = "4";
		option_GENDER_4.innerHTML = "Hentai (-16/-18)";
	
	select_GENDER.appendChild(option_GENDER_4);
	
	var span_tree_info = document.createElement('span');
		span_tree_info.className = 'tree';
		span_tree_info.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_info);
	
	var label_info = document.createElement("label");
		label_info.innerHTML = "Page d'information : ";
	
	show.appendChild(label_info);
	
	var span_help_INFOPNG = document.createElement('span');
		span_help_INFOPNG.className = 'help';
		span_help_INFOPNG.title = "Utilisez-vous une page 'info.png' pour cette série ?";
		span_help_INFOPNG.setAttribute("onclick", "alert('Utilisez-vous une page \\\'info.png\\\' pour cette série ?');");
		span_help_INFOPNG.innerHTML = "(?)";
	
	show.appendChild(span_help_INFOPNG);
	
	var input_INFOPNG = document.createElement("input");
		input_INFOPNG.type = "checkbox";
		input_INFOPNG.id = "INFOPNG_"+i;
		input_INFOPNG.name = "data["+i+"][INFOPNG]";
	
	show.appendChild(input_INFOPNG);
	
	var label_INFOPNG = document.createElement("label");
		label_INFOPNG.setAttribute("for", "INFOPNG_"+i);
		label_INFOPNG.innerHTML = "Oui";
	
	show.appendChild(label_INFOPNG);
	show.appendChild(document.createElement('br'));
	
	var span_tree_CHAPTER_SPECIALS = document.createElement('span');
		span_tree_CHAPTER_SPECIALS.className = 'tree';
		span_tree_CHAPTER_SPECIALS.innerHTML = "&nbsp;|";
	
	show.appendChild(span_tree_CHAPTER_SPECIALS);
	
	var label_CHAPTER_SPECIALS = document.createElement("label");
		label_CHAPTER_SPECIALS.setAttribute("for", "CHAPTER_SPECIALS_"+i);
		label_CHAPTER_SPECIALS.innerHTML = "Nombre de chapitre sp&eacute;ciaux : ";
	
	show.appendChild(label_CHAPTER_SPECIALS);
	
	var span_help_CHAPTER_SPECIALS = document.createElement('span');
		span_help_CHAPTER_SPECIALS.className = 'help';
		span_help_CHAPTER_SPECIALS.title = "Avez-vous des inter-chapitres de type '10.5' ? Donnez le nombre de ces chapitres";
		span_help_CHAPTER_SPECIALS.setAttribute("onclick", "alert('Avez-vous des inter-chapitres de type \\\'10.5\\\' ? Donnez le nombre de ces chapitres');");
		span_help_CHAPTER_SPECIALS.innerHTML = "(?)";
	
	show.appendChild(span_help_CHAPTER_SPECIALS);
	
	var input_CHAPTER_SPECIALS = document.createElement("input");
		input_CHAPTER_SPECIALS.type = "text";
		input_CHAPTER_SPECIALS.id = "CHAPTER_SPECIALS_"+i;
		input_CHAPTER_SPECIALS.name = "data["+i+"][CHAPTER_SPECIALS]";
	
	show.appendChild(input_CHAPTER_SPECIALS);
	
	// ajout de notre balise principale dans la page
	document.getElementById("list_serie").appendChild(conten);
	
	i++;
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
unset($_SESSION['error']);
?>
