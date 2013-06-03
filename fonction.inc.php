<?php
// var traceur include
$page_fonction = true;
if(empty($page_index))
	die('SECURITY ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');
////////////////////////////////////////////////////////

/*************************************************/
// fonction cree un array de la lecture d une string
function loader($string=null)
{
	$string = str_replace("\n\n","\n", str_replace("\r","\n",$string)); // vire les \r et \n\n
	$out = null;
	$alloc_long = array(); // array d allocaltion Clef-NomLong pour les check de doublon
	$alloc_short = array(); // array d allocaltion Clef-Nomcourt pour les check de doublon

	if(!($partie_array = explode("#\n", $string)))
		return false;
	for($i=0; isset($partie_array[$i]); $i++)// on parcours les partie du fichier
	{
		if(!$i) // si premere ligne
		{
			if(!($ligne_array = explode("\n", $partie_array[$i])))
				return false;
			if(!($const = explode(' ', $ligne_array[0])))
				return false;

			unset($ligne_array[0]);
			// on charge les constantes du depot 
			$out['const']['LONG_MANAGER_NAME'] = (isset($const[0]))? str_replace('_',' ',$const[0]) : null;
			$out['const']['SHORT_MANAGER_NAME'] = (isset($const[1]))? str_replace('_',' ',$const[1]) : null;

			foreach($ligne_array as $ligne)
			{
				if($ligne != '#' and $ligne != null)
				{
					$elem = explode(' ', $ligne);
					//-2	longN	shortN	firstC	lastC	firstT	lastT	StatGnR	info	chapsp
					//	[0]	[1]	[2]	[3]	[4]	[5]	[6]	[7]	[8]
					//-1	longN	shortN	firstC	lastC	StatGnR	info
					$check_elem['LONG_PROJECT_NAME'] = isset($elem[0])? str_replace('_',' ',$elem[0]) : null;
					$check_elem['SHORT_PROJECT_NAME'] = isset($elem[1])? str_replace('_',' ',$elem[1]) : null;
					$check_elem['FIRST_CHAPTER'] = isset($elem[2])? $elem[2] : -1;
					$check_elem['LAST_CHAPTER'] = isset($elem[3])? $elem[3] : -1;
					$check_elem['FIRST_TOME'] = isset($elem[4], $elem[6], $elem[7])? $elem[4] : -1;
					$check_elem['LAST_TOME'] = isset($elem[5], $elem[6], $elem[7])? $elem[5] : -1;
						$elem['a'] = isset($elem[6])? str_split($elem[6]) : (isset($elem[4])? str_split($elem[4]) : array(1,1));
					$check_elem['STATE'] = isset($elem['a'][0])? $elem['a'][0] : 1;
					$check_elem['GENDER'] = isset($elem['a'][1])? $elem['a'][1] : 1;
					$check_elem['INFOPNG'] = isset($elem[7])? $elem[7] : (isset($elem[5])? $elem[5] : null);
					$check_elem['CHAPTER_SPECIALS'] = isset($elem[8])? $elem[8] : null;

					if	(	($check_elem['LONG_PROJECT_NAME'] == null 
							or !in_array($check_elem['LONG_PROJECT_NAME'], $alloc_long)
							)
						and 	($check_elem['SHORT_PROJECT_NAME'] == null 
							or !in_array($check_elem['SHORT_PROJECT_NAME'], $alloc_short)
							)
						) // si le projet est vide ou exsiste pas
					{
						$out['data'][] = $check_elem;
						$alloc_long[] = $check_elem['LONG_PROJECT_NAME'];
						$alloc_short[] = $check_elem['SHORT_PROJECT_NAME'];
					}

					unset($elem, $check_elem);
				}
			}
			unset($ligne, $ligne_array);
		}
		else
		{
			if(!($ligne_array = explode("\n", $partie_array[$i])))
				continue;
			if(!($head = explode(' ', $ligne_array[0]))) // si 1er ligne vide on passe a la partie next
				continue;

			unset($ligne_array[0]);
			$head[0]= str_replace('_',' ',$head[0]);

			if(!empty($alloc_long) and in_array($head[0], $alloc_long)) // si le projet exsite
			{
				$key_serie = array_keys($alloc_long, $head[0]); // la key serie dans l array master
				
				if($head[1] == 'C') // si c est un/des chapitre(s)
				{
					$chap_sp_array_tmp = explode(' ', $ligne_array[1]);
					natcasesort($chap_sp_array_tmp);
					foreach($chap_sp_array_tmp as $key=>$value)
					{
						if(intval($value))
							$chap_sp_array[] = intval($value) / 10;
					}
					$out['data'][$key_serie[0]]['array_chap_sp'] = $chap_sp_array;
					$out['data'][$key_serie[0]]['string_chap_sp'] = implode('; ', $chap_sp_array);
					$out['data'][$key_serie[0]]['CHAPTER_SPECIALS'] = count($chap_sp_array);
					
					unset($chap_sp_array_tmp, $chap_sp_array, $key, $value);
				}
				elseif($head[1] == 'T') // si c est des tomes
				{
					$tome_array = array();
					foreach($ligne_array as $value) // les ligne de tome
					{
						$tome_array_tmp = explode(' ', $value);
						if	(isset($tome_array_tmp[0]) 
							and ctype_digit($tome_array_tmp[0]) 
							and !array_key_exists(intval($tome_array_tmp[0]), $tome_array) 
							)
						{ // si ID + titre, que id est bien un int qui n est pas dans les deja ajoute
							if(isset($tome_array_tmp[1]) and $tome_array_tmp[1] != '_')
								$tome_array[intval($tome_array_tmp[0])]['NAME'] = str_replace('_',' ',$tome_array_tmp[1]);
							else
								$tome_array[intval($tome_array_tmp[0])]['NAME'] = null;

							if(isset($tome_array_tmp[2]) and $tome_array_tmp[2] != '_')
								$tome_array[intval($tome_array_tmp[0])]['DEF_LINE_1'] = str_replace('_',' ',$tome_array_tmp[2]);
							else
								$tome_array[intval($tome_array_tmp[0])]['DEF_LINE_1'] = null;

							if(isset($tome_array_tmp[3]) and $tome_array_tmp[3] != '_')
								$tome_array[intval($tome_array_tmp[0])]['DEF_LINE_2'] = str_replace('_',' ',$tome_array_tmp[3]);
							else
								$tome_array[intval($tome_array_tmp[0])]['DEF_LINE_2'] = null;
						}
						unset($tome_array_tmp);
					}
					if(!empty($tome_array))
					{
						ksort($tome_array);
						$out['data'][$key_serie[0]]['array_tome'] = $tome_array;
					}
					
					unset($tome_array, $value);
				}
				unset($key_serie);
			}
			unset($ligne_array, $head);
		}
	}	
	if(isset($out['data']))
		$out['data'] = sort_array($out['data']);

	return $out;
}
/*************************************************/
// fonction met en forme les valeur array pour l output string
function parser($input=null)
{
	$version = 1;
	$footer = null;
	$alloc_long = array(); // array d allocaltion Clef-NomLong pour les check de doublon
	$alloc_short = array(); // array d allocaltion Clef-Nomcourt pour les check de doublon
	
	if(empty($input['const']['LONG_MANAGER_NAME'])){
		$_SESSION['error']['LONG_MANAGER_NAME'][] = 'Merci de remplir le "Nom Complet" de votre d&eacute;p&ocirc;t.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LONG_MANAGER_NAME';
	}
	if(empty($input['const']['SHORT_MANAGER_NAME'])){
		$_SESSION['error']['SHORT_MANAGER_NAME'][] = 'Merci de remplir le "Nom Abr&eacute;g&eacute;" de votre d&eacute;p&ocirc;t.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'SHORT_MANAGER_NAME';
	}
	if(strlen($input['const']['LONG_MANAGER_NAME']) > 25){
		$_SESSION['error']['LONG_MANAGER_NAME'][] = 'Le "Nom Complet" de votre d&eacute;p&ocirc;t fait plus de 25 caract&egrave;res.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LONG_MANAGER_NAME';
	}
	if(strlen($input['const']['SHORT_MANAGER_NAME']) > 10){
		$_SESSION['error']['SHORT_MANAGER_NAME'][] = 'Le "Nom Abr&eacute;g&eacute;" de votre d&eacute;p&ocirc;t fait plus de 10 caract&egrave;res';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'SHORT_MANAGER_NAME';
	}
	if(empty($input['data']) || !is_array($input['data']))
		$_SESSION['error']['data'][] = 'Merci de remplir vos s&eacute;ries.';
	else
	{
		$out = null;
		foreach($input['data'] as $key => $array)
		{
			if	(($array = check_manga_line($array, $key))
				and !in_array($array['LONG_PROJECT_NAME'], $alloc_long)
				and !in_array($array['SHORT_PROJECT_NAME'], $alloc_short)
				)
			{
				// les array d allocation :
				$alloc_long[] = $array['LONG_PROJECT_NAME'];
				$alloc_short[] = $array['SHORT_PROJECT_NAME'];
				// 2eme partie, list serie
				$out.=$array['LONG_PROJECT_NAME'].' '.$array['SHORT_PROJECT_NAME'].' '.$array['FIRST_CHAPTER'].' '.
					$array['LAST_CHAPTER'].' '.$array['FIRST_TOME'].' -1 '.$array['STATE'].
					$array['GENDER'].' '.$array['INFOPNG'].' '.$array['CHAPTER_SPECIALS']."\n";
				// 3eme partie, chap sp
				if(!empty($array['string_chap_sp']))
					$footer .= "#\n".$array['LONG_PROJECT_NAME']." C\n".str_replace(';','',$array['string_chap_sp'])."\n";
				// 4eme partie, tome
				if(!empty($array['array_tome']) and is_array($array['array_tome']))
				{
					$footer .= "#\n".$array['LONG_PROJECT_NAME']." T\n";
					foreach ($array['array_tome'] as $id => $tome)
					{
						$footer .= $id;
						if(isset($tome['NAME']) and $tome['NAME'] != null)
						{
							$footer .= ' ' .$tome['NAME'];
							if(isset($tome['DEF_LINE_1']) and $tome['DEF_LINE_1'] != null)
							{
								$footer .= ' ' .$tome['DEF_LINE_1'];
							}
							if(isset($tome['DEF_LINE_2']) and $tome['DEF_LINE_2'] != null)
							{
								$footer .= ' ' .$tome['DEF_LINE_2'];
							}
						}
						$footer .= "\n";
					}
				}
			}
		}
	}
	if(empty($out))
		$_SESSION['error']['data'][] = 'Merci de remplir correctement vos s&eacute;ries.';
	if(!empty($_SESSION['error']))
		return false;
	// 1ere partie, le header
	$head = str_replace(' ','_',$input['const']['LONG_MANAGER_NAME']). ' ' .str_replace(' ','_',$input['const']['SHORT_MANAGER_NAME']).' '.$version."\n";
	return $head . $out . $footer;
}
/*************************************************/
// fonction check si les valeur de la ligne sont correcte
function check_manga_line($input, $id)
{
	// projet vide : on saute
	if	(  empty($input['LONG_PROJECT_NAME']) 
		&& empty($input['SHORT_PROJECT_NAME']) 
		&& empty($input['FIRST_CHAPTER']) 
		&& empty($input['LAST_CHAPTER']) 
		&& empty($input['INFOPNG']) 
		&& empty($input['string_chap_sp'])
		)
		return false;
	// Check que toutes les variables sont remplies & correctment
	if(!isset($input['LONG_PROJECT_NAME']) || $input['LONG_PROJECT_NAME'] == null){
		$_SESSION['error'][$id]['LONG_PROJECT_NAME'][] = 'Merci de remplir le "Nom Complet" de cette s&eacute;rie.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LONG_PROJECT_NAME_'.$id;
	}
	if(strlen($input['LONG_PROJECT_NAME']) > 50){
		$_SESSION['error'][$id]['LONG_PROJECT_NAME'][] = 'Le "Nom Complet" de cette s&eacute;rie fait plus de 50 caract&egrave;res.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LONG_PROJECT_NAME_'.$id;
	}
	if(!isset($input['SHORT_PROJECT_NAME']) || $input['SHORT_PROJECT_NAME'] == null){
		$_SESSION['error'][$id]['SHORT_PROJECT_NAME'][] = 'Merci de remplir le "Nom Abr&eacute;g&eacute;" de cette s&eacute;rie.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'SHORT_PROJECT_NAME_'.$id;
	}
	if(strlen($input['SHORT_PROJECT_NAME']) > 10){
		$_SESSION['error'][$id]['SHORT_PROJECT_NAME'][] = 'Le "Nom Abr&eacute;g&eacute;" de cette s&eacute;rie fait plus de 10 caract&egrave;res.';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'SHORT_PROJECT_NAME_'.$id;
	}
	if	(  empty($input['STATE']) 
		|| empty($input['GENDER']) 
		|| !isset($input['FIRST_CHAPTER']) 
		|| !isset($input['LAST_CHAPTER']) 
		)
		$_SESSION['error'][$id]['champs'][] = 'Pour Uncle Joe ses champs c\'est comme sa femme : il aime pas qu\'on y touche...';
		
	$input['FIRST_CHAPTER'] = (isset($input['FIRST_CHAPTER']) && $input['FIRST_CHAPTER'] == -1)? null : $input['FIRST_CHAPTER'];
	$input['LAST_CHAPTER'] = (isset($input['LAST_CHAPTER']) && $input['LAST_CHAPTER'] == -1)? null : $input['LAST_CHAPTER'];
	
	if	(	(isset($input['FIRST_CHAPTER']) && $input['FIRST_CHAPTER'] != null) 
			xor (isset($input['LAST_CHAPTER']) && $input['LAST_CHAPTER'] != null)
		) // si un chap deb OU fin remplis error
	{
		if(!isset($input['FIRST_CHAPTER']) || $input['FIRST_CHAPTER'] == null) {
			$_SESSION['error'][$id]['FIRST_CHAPTER'][] = 'Merci de remplir "Premier chapitre".';
			if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'FIRST_CHAPTER_'.$id;
		}
		if(!isset($input['LAST_CHAPTER']) || $input['LAST_CHAPTER'] == null){
			$_SESSION['error'][$id]['LAST_CHAPTER'][] = 'Merci de remplir "Dernier chapitre".';
			if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LAST_CHAPTER_'.$id;
		}
	}
	if (	(!isset($input['FIRST_CHAPTER']) || $input['FIRST_CHAPTER'] == null)
			&& (!isset($input['LAST_CHAPTER']) || $input['LAST_CHAPTER'] == null)
			&& empty($input['array_tome'])
		) // si rien de remplis comme sortie
	{
		$_SESSION['error'][$id]['sortie'][] = 'Merci de remplir les champs "Tome" ou "Chapitre".';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'FIRST_CHAPTER_'.$id;
	}
	if(!empty($input['FIRST_CHAPTER']) && !ctype_digit($input['FIRST_CHAPTER'])){
		$_SESSION['error'][$id]['FIRST_CHAPTER'][] = 'Merci de remplir correctement "Premier chapitre".';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'FIRST_CHAPTER_'.$id;
	}
	elseif(intval($input['FIRST_CHAPTER']) > 999999999 || intval($input['FIRST_CHAPTER']) < 0){
		$_SESSION['error'][$id]['chapitre'][] = 'Votre premier chapitre est hors de la limite 0 / 999 999 999...';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'FIRST_CHAPTER_'.$id;
	}
	if(!empty($input['LAST_CHAPTER']) && !ctype_digit($input['LAST_CHAPTER'])){
		$_SESSION['error'][$id]['LAST_CHAPTER'][] = 'Merci de remplir correctement "Dernier chapitre".';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LAST_CHAPTER_'.$id;
	}
	elseif(intval($input['LAST_CHAPTER']) > 999999999 || intval($input['LAST_CHAPTER']) < 0){
		$_SESSION['error'][$id]['chapitre'][] = 'Votre dernier chapitre est hors de la limite 0 / 999 999 999...';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'LAST_CHAPTER_'.$id;
	}
	if(!empty($input['string_chap_sp']) && preg_match('#[^0-9 ;,.]#', $input['string_chap_sp'])){
		$_SESSION['error'][$id]['string_chap_sp'][] = 'Merci de remplir correctement "Chapitre sp&eacute;ciaux".';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'string_chap_sp_'.$id;
	}
	if(!empty($input['array_tome']) and is_array($input['array_tome']))
	{ // gestion des tomes
		foreach($input['array_tome'] as $id_tome=>$tome)
		{
			if(!empty($tome['error']) and $tome['error'] == 'id_empty')
			{
				$_SESSION['error'][$id]['array_tome'][$id_tome][] = 'Merci de remplir le n° du tome.';
				if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'array_tome_'.$id.'_id_'.$id_tome;
			}
			elseif(!empty($tome['error']) and $tome['error'] == 'id_nan')
			{
				$_SESSION['error'][$id]['array_tome'][$id_tome][] = 'Merci de remplir correctement le n° du tome.';
				if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'array_tome_'.$id.'_id_'.$id_tome;
			}

			if($id_tome < 0 or $id_tome > 999999999){
				$_SESSION['error'][$id]['array_tome'][$id_tome][] = 'Merci de mettre un n° de tome entre 0 et 999 999.';
				if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'array_tome_'.$id.'_id_'.$id_tome;
			}
			if(!empty($tome['NAME']) && strlen($tome['NAME']) > 30){
				$_SESSION['error'][$id]['array_tome'][$id_tome][] = 'Merci de mettre un titre de moins de 30 caract&egrave;res.';
				if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'array_tome_'.$id.'_name_'.$id_tome;
			}
			if(!empty($tome['DEF_LINE_1']) && strlen($tome['DEF_LINE_1']) > 60){
				$_SESSION['error'][$id]['array_tome'][$id_tome][] = 'Merci de mettre une description 1 de moins de 60 caract&egrave;res.';
				if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'array_tome_'.$id.'_def1_'.$id_tome;
			}
			if(!empty($tome['DEF_LINE_2']) && strlen($tome['DEF_LINE_2']) > 60){
				$_SESSION['error'][$id]['string_chap_sp'][$id_tome][] = 'Merci de mettre une description 2 de moins de 60 caract&egrave;res.';
				if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'array_tome_'.$id.'_def2_'.$id_tome;
			}
		}
	}
	unset($id_tome, $tome);
	if(!empty($_SESSION['error'][$id]))
		return false;
	/////////////////////////////////////////////////
	// check des coherances entre debut et fin
	if(intval($input['FIRST_CHAPTER']) > intval($input['LAST_CHAPTER'])){
		$_SESSION['error'][$id]['chapitre'][] = 'Votre n° de premier chapitre est plus grand que votre dernier...';
		if(empty($_SESSION['error']['id'])) $_SESSION['error']['id'] = 'FIRST_CHAPTER_'.$id;
	}
		
	if(!empty($_SESSION['error'][$id]))
		return false;
	//////////////////////////////////////////////////
	// traitement des chap sp et des tomes
	if(!empty($input['string_chap_sp']))
	{
		$input['array_chap_sp'] = array();
		$chap_sp_array_tmp = explode(';', $input['string_chap_sp']);
		natcasesort($chap_sp_array_tmp);
		foreach($chap_sp_array_tmp as $key=>$value)
		{
			if(!in_array(intval($value * 10), $input['array_chap_sp']))
				$input['array_chap_sp'][] = intval($value * 10);
		}
		$input['string_chap_sp'] = implode('; ', $input['array_chap_sp']);
	}

	if(!empty($input['array_tome']) and is_array($input['array_tome']))
	{
		foreach($input['array_tome'] as $id_tome=>$tome)
		{
				if(isset($tome['NAME']))
					$input['array_tome'][$id_tome]['NAME'] = str_replace(' ', '_', $tome['NAME']);

				if(!isset($tome['NAME']) or $tome['NAME'] == null)
					$input['array_tome'][$id_tome]['NAME'] = '_';

				if(isset($tome['DEF_LINE_1']))
					$input['array_tome'][$id_tome]['DEF_LINE_1'] = str_replace(' ', '_', $tome['DEF_LINE_1']);

				if(!isset($tome['DEF_LINE_1']) or $tome['DEF_LINE_1'] == null)
					$input['array_tome'][$id_tome]['DEF_LINE_1'] = '_';

				if(isset($tome['DEF_LINE_2']))
					$input['array_tome'][$id_tome]['DEF_LINE_2'] = str_replace(' ', '_', $tome['DEF_LINE_2']);

				if(!isset($tome['DEF_LINE_2']) or $tome['DEF_LINE_2'] == null)
					$input['array_tome'][$id_tome]['DEF_LINE_2'] = '_';
		}
	}
	// on met les par def pour les valeurs vide

	$input['SHORT_PROJECT_NAME'] = str_replace(' ', '_', $input['SHORT_PROJECT_NAME']);
	$input['LONG_PROJECT_NAME'] = str_replace(' ', '_', $input['LONG_PROJECT_NAME']);
	$input['CHAPTER_SPECIALS'] = empty($input['array_chap_sp'])? 0 : count($input['array_chap_sp']);
	$input['FIRST_CHAPTER'] = ($input['FIRST_CHAPTER'] == null)? -1 : intval($input['FIRST_CHAPTER']);
	$input['LAST_CHAPTER'] = ($input['LAST_CHAPTER'] == null)? -1 : intval($input['LAST_CHAPTER']);
	$input['FIRST_TOME'] = empty($input['array_tome'])? -1 : 1;
	$input['INFOPNG'] = empty($input['INFOPNG'])? 0 : 1;
	$input['GENDER'] = (!ctype_digit($input['GENDER']) || $input['GENDER'] < 1 || $input['GENDER'] > 4)? 1 : intval($input['GENDER']);
	$input['STATE'] = (!ctype_digit($input['STATE']) || $input['STATE'] < 1 || $input['STATE'] > 3)? 1 : intval($input['STATE']);
	
	return $input;
}
/*************************************************/
// fonction lancer le dl du ficher
function set_download($str_file=null, $name = 'rakshata-manga-2')
{
	$size = strlen($str_file);
	log_f('`set_download`', 'file '.$size.'o / '.substr_count($str_file, "\n").' `\n`');
	header("Content-Type: application/octet-stream; charset=ISO-8859-1;");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $size");
	header("Content-Disposition: attachment; filename=\"" .$name. "\"");
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	echo $str_file;
	die();
}
/*************************************************/
// fonction creer ou modifier les cookie de maniere recursive
function set_cookie($mixed=null,$name=null, $time_out=30758400)
{
	if(is_array($mixed))
		foreach($mixed as $key=>$value)
			set_cookie($value, $name.'['.$key.']', $time_out);
	else
		setcookie($name, $mixed, time() + $time_out);
}
/*************************************************/
// fonction convertire utf8 en ascii
function switch_utf8_ascii($mixed, $out_enc='ISO-8859-1//TRANSLIT', $in_enc='UTF-8')
{
	if(is_array($mixed)){ // si on lui passe un array on recursive
		foreach($mixed as $key => $value){
			if(is_array($value))
				$out[$key] = switch_utf8_ascii($value, $out_enc, $in_enc);
			else
				$out[$key] = iconv($in_enc, $out_enc, $value);
		}
	}
	else
		$out = iconv($in_enc, $out_enc, $mixed);
	return $out;
}
/*************************************************/
// fonction fait le tri naturel des nom long; re-index les serie
function sort_array($array=array())
{
	$in = null;
	$add = array();
	foreach($array as $value){ // on boucle sur les series
		$in[] = $value;
		$add[] = (isset($value['LONG_PROJECT_NAME']) && $value['LONG_PROJECT_NAME'] != null)? $value['LONG_PROJECT_NAME'] : '';
	}
	natcasesort($add);
	foreach($add as $clef=>$valeur) // re index des clefs avec le natsort
		$out[] = $in[$clef];
	return sort_tome($out);
}
/*************************************************/
// fonction re-organise l array data tome
function sort_tome($array=array())
{
	$out = null;
	if(!empty($array) and is_array($array)) // si il y a bien des serie
	{
		foreach($array as $key=>$value)
		{
			$out[$key] = $value; // on charges les info serie
			if(!empty($value['array_tome']) and is_array($value['array_tome'])) // si on a des tomes
			{
				unset($out[$key]['array_tome']); // on vire les tomes de l output
				$alloc_tome = array();
				$error_tome = array();
				foreach($value['array_tome'] as $id=>$tome) // on boucles les tomes
				{
					if(isset($tome['ID'])) // si c est un envois POST
					{
						if($tome['ID'] == null or !ctype_digit($tome['ID'])) // si ID est vide & un truc remplis, on charge erreur
						{
							if(isset($tome['NAME']) and $tome['NAME'] != null) 
								$error_tome[$id]['NAME'] = $tome['NAME'];
							
							if(isset($tome['DEF_LINE_1']) and $tome['DEF_LINE_1'] != null) 
								$error_tome[$id]['DEF_LINE_1'] = $tome['DEF_LINE_1'];
						
							if(isset($tome['DEF_LINE_2']) and $tome['DEF_LINE_2'] != null) 
								$error_tome[$id]['DEF_LINE_2'] = $tome['DEF_LINE_2'];
							
							// si on a sauver qq chose, on cree le flag d erreur
							if(isset($error_tome[$id]))
							{
								if($tome['ID'] == null)
									$error_tome[$id]['error'] = 'id_empty';
								elseif (!ctype_digit($tome['ID'])) 
									$error_tome[$id]['error'] = 'id_nan';
							}
						}
						elseif(!in_array(intval($tome['ID']), $alloc_tome)) // si pas deja dans les tome de la serie
						{
							$out[$key]['array_tome'][intval($tome['ID'])] = null;
							$alloc_tome[] = intval($tome['ID']);
						
							if(isset($tome['NAME']) and $tome['NAME'] != null) 
								$out[$key]['array_tome'][intval($tome['ID'])]['NAME'] = $tome['NAME'];

							if(isset($tome['DEF_LINE_1']) and $tome['DEF_LINE_1'] != null) 
								$out[$key]['array_tome'][intval($tome['ID'])]['DEF_LINE_1'] = $tome['DEF_LINE_1'];
						
							if(isset($tome['DEF_LINE_2']) and $tome['DEF_LINE_2'] != null) 
								$out[$key]['array_tome'][intval($tome['ID'])]['DEF_LINE_2'] = $tome['DEF_LINE_2'];
						}
					}
					elseif(!isset($tome['ID']) and !in_array($id, $alloc_tome)) // si envois FILE
					{
						$out[$key]['array_tome'][intval($id)] = null;
						$alloc_tome[] = intval($id);
						
						if(isset($tome['NAME']) and $tome['NAME'] != null) 
							$out[$key]['array_tome'][intval($id)]['NAME'] = $tome['NAME'];
						
						if(isset($tome['DEF_LINE_1']) and $tome['DEF_LINE_1'] != null) 
							$out[$key]['array_tome'][intval($id)]['DEF_LINE_1'] = $tome['DEF_LINE_1'];
						
						if(isset($tome['DEF_LINE_2']) and $tome['DEF_LINE_2'] != null) 
							$out[$key]['array_tome'][intval($id)]['DEF_LINE_2'] = $tome['DEF_LINE_2'];
					}
				}
				unset($alloc_tome, $id, $tome);

				if(!empty($error_tome))
				{
					foreach ($error_tome as $tome) 
					{
						$out[$key]['array_tome'][] = $tome; // re assignation dans l array principale
					}
					unset($error_tome);
				}

				// tri des tome par ID
				if(isset($out[$key]['array_tome']) and is_array($out[$key]['array_tome']))
					ksort($out[$key]['array_tome']);
			}
		}
	}
	
	return $out;
}
/*************************************************/
// fonction faire un echo des erreurs de l'array passee
function show_error($mixed)
{
	if(is_array($mixed))
	{
		echo '<span class="error">';
		foreach($mixed as $echo_error) // parcours la liste des error
		{
			echo isset($i)? "<br />\n":null;
			echo $echo_error;
			$i = 1;
		}
		echo '</span>'."\n";
	}
	else
		echo '<span class="error">'.$mixed.'</span>'."\n";
		
}
/*************************************************/
// fonction faire un echo formate de la string d aide
function help($string)
{
	$string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false);
	echo '<span class="help" title="'.$string.'" onclick="alert(\''.addslashes($string).'\')">(?)</span>';		
}
/*************************************************/
// fonction desactive l effet de magic_quotes_gpc
function mq_stripslashes()
{
	function mq_loc_stripslashes(&$value, $key){
		$value = stripslashes($value);
	}

	$super_glob = array(&$_GET, &$_POST, &$_COOKIE); // liste des var traitees
	array_walk_recursive($super_glob, 'mq_loc_stripslashes');
}
/*************************************************/
// fonction echape les " du csv
function csv($texte)
{
	return str_replace('"', '""', $texte);
}
/*************************************************/
// fonction de mise en log
function log_f($title=null, $action=null)
{
		//ouverture ou creation du fichier de log
	if(!is_dir('log') and !mkdir('log', 0700, true))
		die('CONFIGURATION ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');
	$file = 'log/log_' .date('y-m'). '.csv';
	if(!is_file($file))
	{
		if(!touch($file))
			die('CONFIGURATION ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');
		chmod($file, 0600);
	}
	if(!($fichier_log = @fopen($file, 'a')))
		die('CONFIGURATION ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');

		//enregistrement de l'entree
	fputs($fichier_log , '"' .date('Y-m-d-H:i:s'). '","' .$_SERVER['REMOTE_ADDR']. '","' .csv($title). '","' .csv($action). "\"\n");
		//fermeture du fichier
	fclose($fichier_log);
}

