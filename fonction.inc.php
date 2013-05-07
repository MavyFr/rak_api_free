<?php
// var traceur include
$page_fonction = true;
if(empty($page_index))
	die('SECURITY ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');
////////////////////////////
// gestion des membres :
////////////////////////////

/*************************************************/
// fonction sort un array de la lecture d une string
function loader($string=null)
{
	if(!($ligne_array = explode("\n", str_replace("\n\n","\n", str_replace("\r","\n",$string)))))
		return -1;
	if(!($const = explode(' ', $ligne_array[0])))
		return -2;
	
	unset($ligne_array[0]);

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
			
			$out['data'][] = $check_elem;
			unset($elem, $check_elem);
		}
	}
	if(isset($out['data']))
		$out['data'] = sort_array($out['data']);
	return $out;
}
/*************************************************/
// fonction met en forme les valeur pour l output
function parser_to_ressource($input=null)
{
	if(empty($input['const']['LONG_MANAGER_NAME']))
		$_SESSION['error']['LONG_MANAGER_NAME'][] = 'Merci de remplir le "Nom Long" de votre d&eacute;p&ocirc;t.';
	if(empty($input['const']['SHORT_MANAGER_NAME']))
		$_SESSION['error']['SHORT_MANAGER_NAME'][] = 'Merci de remplir le "Nom Court" de votre d&eacute;p&ocirc;t.';
	if(strlen($input['const']['LONG_MANAGER_NAME']) > 25)
		$_SESSION['error']['LONG_MANAGER_NAME'][] = 'Le "Nom Long" de votre d&eacute;p&ocirc;t fait plus de 25 caract&egrave;res.';
	if(strlen($input['const']['SHORT_MANAGER_NAME']) > 10)
		$_SESSION['error']['SHORT_MANAGER_NAME'][] = 'Le "Nom Court" de votre d&eacute;p&ocirc;t fait plus de 10 caract&egrave;res';
	
	if(empty($input['data']) || !is_array($input['data']))
		$_SESSION['error']['data'][] = 'Merci de remplir vos s&eacute;ries.';
	else
	{
		$out = null;
		foreach($input['data'] as $key => $array){
			if(($array = check_manga_line($array, $key)))
				$out.=$array['LONG_PROJECT_NAME'].' '.$array['SHORT_PROJECT_NAME'].' '.$array['FIRST_CHAPTER'].' '.
					$array['LAST_CHAPTER'].' '.$array['FIRST_TOME'].' '.$array['LAST_TOME'].' '.$array['STATE'].
					$array['GENDER'].' '.$array['INFOPNG'].' '.$array['CHAPTER_SPECIALS']."\n";
		}
	}
	if(empty($out))
		$_SESSION['error']['data'][] = 'Merci de remplire correctement vos s&eacute;ries.';
	
	if(!empty($_SESSION['error']))
		return false;
	
	$head = str_replace(' ','_',$input['const']['LONG_MANAGER_NAME']). ' ' .str_replace(' ','_',$input['const']['SHORT_MANAGER_NAME']). "\n";
	return $head . $out;
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
		&& empty($input['FIRST_TOME']) 
		&& empty($input['LAST_TOME']) 
		&& empty($input['INFOPNG']) 
		&& empty($input['CHAPTER_SPECIALS'])
		)
		return false;
	// Check que toutes les variables sont remplies & correctment
	if(!isset($input['LONG_PROJECT_NAME']) || $input['LONG_PROJECT_NAME'] == null)
		$_SESSION['error'][$id]['LONG_PROJECT_NAME'][] = 'Merci de remplir le "Nom Long" de cette s&eacute;rie.';
	if(strlen($input['LONG_PROJECT_NAME']) > 50)
		$_SESSION['error'][$id]['LONG_PROJECT_NAME'][] = 'Le "Nom Long" de cette s&eacute;rie fait plus de 50 caract&egrave;res.';
	if(!isset($input['SHORT_PROJECT_NAME']) || $input['SHORT_PROJECT_NAME'] == null)
		$_SESSION['error'][$id]['SHORT_PROJECT_NAME'][] = 'Merci de remplir le "Nom Courts" de cette s&eacute;rie.';
	if(strlen($input['SHORT_PROJECT_NAME']) > 10)
		$_SESSION['error'][$id]['SHORT_PROJECT_NAME'][] = 'Le "Nom Court" de cette s&eacute;rie fait plus de 10 caract&egrave;res.';
	if	(  empty($input['STATE']) 
		|| empty($input['GENDER']) 
		|| !isset($input['FIRST_CHAPTER']) 
		|| !isset($input['LAST_CHAPTER']) 
		|| !isset($input['FIRST_TOME']) 
		|| !isset($input['LAST_TOME'])
		)
		$_SESSION['error'][$id]['champs'][] = 'Pour Uncle Joe ses champs c\'est comme sa femme : il aime pas qu\'on y touche...';
	if(	(isset($input['FIRST_CHAPTER']) && $input['FIRST_CHAPTER'] != null) 
		xor (isset($input['LAST_CHAPTER']) && $input['LAST_CHAPTER'] != null)
	)
	{
		if(!isset($input['FIRST_CHAPTER']) || $input['FIRST_CHAPTER'] == null) 
			$_SESSION['error'][$id]['FIRST_CHAPTER'][] = 'Merci de remplire "Premier chapitre".';
		if(!isset($input['LAST_CHAPTER']) || $input['LAST_CHAPTER'] == null)
			$_SESSION['error'][$id]['LAST_CHAPTER'][] = 'Merci de remplire "Dernier chapitre".';
	}
	if(	(isset($input['FIRST_TOME']) && $input['FIRST_TOME'] != null)
		xor (isset($input['LAST_TOME']) && $input['LAST_TOME'] != null)
	)
	{
		if(!isset($input['FIRST_TOME']) || $input['FIRST_TOME'] == null)
			$_SESSION['error'][$id]['FIRST_TOME'][] = 'Merci de remplire "Premier tome".';
		if(!isset($input['LAST_TOME']) || $input['LAST_TOME'] == null)
			$_SESSION['error'][$id]['LAST_TOME'][] = 'Merci de remplire "Dernier tome".';
	}
	if (	(!isset($input['FIRST_CHAPTER']) || $input['FIRST_CHAPTER'] == null)
		&& (!isset($input['LAST_CHAPTER']) || $input['LAST_CHAPTER'] == null)
		&& (!isset($input['FIRST_TOME']) || $input['FIRST_TOME'] == null)
		&& (!isset($input['LAST_TOME']) || $input['LAST_TOME'] == null)
	)
		$_SESSION['error'][$id]['sortie'][] = 'Merci de remplire les champs "Tome" ou "Chapitre".';
	if(!empty($input['FIRST_CHAPTER']) && !is_num($input['FIRST_CHAPTER']))
		$_SESSION['error'][$id]['FIRST_CHAPTER'][] = 'Merci de remplire correctement "Premier chapitre".';
	if(!empty($input['LAST_CHAPTER']) && !is_num($input['LAST_CHAPTER']))
		$_SESSION['error'][$id]['LAST_CHAPTER'][] = 'Merci de remplire correctement "Dernier chapitre".';
	if(!empty($input['FIRST_TOME']) && !is_num($input['FIRST_TOME']))
		$_SESSION['error'][$id]['FIRST_TOME'][] = 'Merci de remplire correctement "Premier tome".';
	if(!empty($input['LAST_TOME']) && !is_num($input['LAST_TOME']))
		$_SESSION['error'][$id]['LAST_TOME'][] = 'Merci de remplire correctement "Dernier tome".';
	if(!empty($input['CHAPTER_SPECIALS']) && !is_num($input['CHAPTER_SPECIALS']))
		$_SESSION['error'][$id]['CHAPTER_SPECIALS'][] = 'Merci de remplire correctement "Chapitre sp&eacute;ciaux".';

	if(!empty($_SESSION['error'][$id]))
		return false;
	
	if(intval($input['FIRST_CHAPTER']) > intval($input['LAST_CHAPTER']))
		$_SESSION['error'][$id]['chapitre'][] = 'Votre n° de premier chapitre est plus grand que votre dernier...';
	if(intval($input['FIRST_TOME']) > intval($input['LAST_TOME']))
		$_SESSION['error'][$id]['tome'][] = 'Votre n° de premier tome est plus grand que votre dernier...';
	if(intval($input['FIRST_CHAPTER']) > 999999999 || intval($input['FIRST_CHAPTER']) < 0)
		$_SESSION['error'][$id]['chapitre'][] = 'Votre premier chapitre est hors de la limite 0 / 999 999 999...';
	if(intval($input['LAST_CHAPTER']) > 999999999 || intval($input['LAST_CHAPTER']) < 0)
		$_SESSION['error'][$id]['chapitre'][] = 'Votre dernier chapitre est hors de la limite 0 / 999 999 999...';
	if(intval($input['FIRST_TOME']) > 999999999 || intval($input['FIRST_TOME']) < 0)
		$_SESSION['error'][$id]['chapitre'][] = 'Votre premier tome est hors de la limite 0 / 999 999 999...';
	if(intval($input['LAST_TOME']) > 999999999 || intval($input['LAST_TOME']) < 0)
		$_SESSION['error'][$id]['chapitre'][] = 'Votre dernier tome est hors de la limite 0 / 999 999 999...';
		
	if(!empty($_SESSION['error'][$id]))
		return false;
	
	// on met les par def pour les valeurs vide
	$input['SHORT_PROJECT_NAME'] = str_replace(' ', '_', $input['SHORT_PROJECT_NAME']);
	$input['LONG_PROJECT_NAME'] = str_replace(' ', '_', $input['LONG_PROJECT_NAME']);
	$input['CHAPTER_SPECIALS'] = empty($input['CHAPTER_SPECIALS'])? 0 : intval($input['CHAPTER_SPECIALS']);
	$input['FIRST_CHAPTER'] = ($input['FIRST_CHAPTER'] == null)? -1 : intval($input['FIRST_CHAPTER']);
	$input['LAST_CHAPTER'] = ($input['LAST_CHAPTER'] == null)? -1 : intval($input['LAST_CHAPTER']);
	$input['FIRST_TOME'] = ($input['FIRST_TOME'] == null)? -1 : intval($input['FIRST_TOME']);
	$input['LAST_TOME'] = ($input['LAST_TOME'] == null)? -1 : intval($input['LAST_TOME']);
	$input['INFOPNG'] = empty($input['INFOPNG'])? 0 : 1;
	$input['GENDER'] = (!is_num($input['GENDER']) || $input['GENDER'] < 1 || $input['GENDER'] > 4)? 1 : intval($input['GENDER']);
	$input['STATE'] = (!is_num($input['STATE']) || $input['STATE'] < 1 || $input['STATE'] > 3)? 1 : intval($input['STATE']);
	
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
// fonction convertire utf8 en ascii
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
// fonction remet les output_value[] en output[]value
function is_num($string=null)
{
	return ($string == strval(intval($string)));
}
/*************************************************/
// fonction remet les output_value[] en output[]value
function sort_array($array=array())
{
	$in = null;
	$add = array();
	foreach($array as $value){
		$in[] = $value;
		$add[] = (isset($value['LONG_PROJECT_NAME']) && $value['LONG_PROJECT_NAME'] != null)? $value['LONG_PROJECT_NAME'] : '';
	}
	natcasesort($add);
	foreach($add as $clef=>$valeur)
		$out[] = $in[$clef];
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
// fonction faire un echo des erreurs de l'array passee
function help($string)
{
	$string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false);
	echo '<span class="help" title="'.$string.'" onclick="alert(\''.addslashes($string).'\')">(?)</span>';		
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


