<?php
	session_start();
if($_SESSION['newTry'] == 1){
	$_SESSION['newTry'] = 0;
	header('Content-Type: text/html; charset=ISO-8859-1');
	$rps = $_POST['data'];
	$time = $_POST['time'];
	$TabRps = explode(';', $rps);
	mysql_connect("localhost", "root", "") or die ("<p id='Erreur'>connection impossible au serveur</p>");
	mysql_select_db("QCM_DB") or die("<p id='Erreur'> Base de Données inconnue </p>");
	$dataQuestion =mysql_fetch_array(mysql_query('SELECT count(*) as NB, SUM(Note) as Sum FROM Questions'));
	//$rps = mysql_fetch_array(mysql_query('SELECT * FROM Reponse'));
		
	$Qcm = mysql_query('SELECT * FROM Questions');
	$q= 0;
	$note = 0;
	$cR = 0;
	$fR = 0;
	$nbQ = $dataQuestion['NB'];
	$contenu = '';
	while($data = mysql_fetch_array($Qcm)){
		$q++;
		$contenu .= '<h3 style="text-align:left;"> - '.$data['Questions'].'  ('.$q.'/'.$nbQ.')</h3>';
		$reponds =  mysql_query('SELECT * FROM Reponse WHERE Id_Question = ' . $data['Id']);
		$contenu .='<ul>';
		$noR = 0;
		$bonR = '';
		while($dataR = mysql_fetch_array($reponds)){
			$test = 0;
			for($i=0; $i<count($TabRps)-1; $i++){
				if($TabRps[$i] == $dataR['Id'])
					{$test =1; $noR = 1;}
			}
			if($test==1 && $dataR['Correct']==1){
				$contenu .= '<li style="text-align:left; color:#090; font-weight: bold">'.$dataR['reponse'].'</li>'; 
				$note = $note + $data['Note']; $cR++;
			}
			elseif($test==1 && $dataR['Correct']==0)
				{$contenu .= '<li style="text-align:left; color:#F00; font-weight: bold">'.$dataR['reponse'].'</li>'; $noR = 0; $fR++;}
			else
				{$contenu .= '<li style="text-align:left; color:#111">'.$dataR['reponse'].'</li>';}
			
			if($dataR['Correct']==1)$bonR=$dataR['reponse'];
		}
		if($noR == 0)$contenu .= '<span style="padding-left:10px; font-weight:bold; position:relative"> * Mauvaise réponse la bonne était : <b style="padding-left:15px; color:green; font-weight:bolder; ">'.$bonR.'</b></span> ';
		$contenu .='</ul>';
	}
	
	$resultat= $note.'/'.$dataQuestion['Sum'];
	mysql_query('INSERT INTO archive(Note, Score, Id_User, DateQcm) VALUES('.$note.', "'.$time.'", '.$_SESSION['id_User'].', NOW())');
	
	$rst = '<fieldset id="result"><legend> Résultat du QCM :</legend><h2>Résultat du QCM : </h2>
	<ul>
		<li>Bonne réponse : <b>'.$cR.'/'.$nbQ.'('.round(($cR/$nbQ)*100, 1).'%)</b></li>
		<li>Mauvaise réponse : <b>'.$fR.'/'.$nbQ.'('.round(($fR/$nbQ)*100, 1).'%)</b></li>
		<li>Pas réponse : <b>'.($nbQ -($fR+$cR)).'/'.$nbQ.'('.round((($nbQ -($fR+$cR))*100)/$nbQ, 1).'%)</b></li>
		<li>Temps écoulé : <b>'.$time.'</b></li>
	</ul>
	Note =' . $resultat;
	echo $rst .'<br/><a href="javascript:detailler()" id="detail">Details>> </a><br/>';
	
	//Sur un server la fonction mail suffira.
	$headers ='From: "nom"<adresse@fai.fr>'."\n"; 
	$headers .='Reply-To: adresse_de_reponse@fai.fr'."\n"; 
	$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n"; 
	$headers .='Content-Transfer-Encoding: 8bit';

	$mail = mail($_SESSION['email'], 'QCM', 'Résultat de QCM :' . $rst, $headers);

	if(!$mail)
		echo "<p id='Erreur'>Mailer Error</p>";
	else
		echo "<p style='color:#009900'>Le Résultat a été au envoyé au ".$_SESSION['email']."</p>";
	echo '</fieldset>';
	
	
	
	//Détailles
	echo '<fieldset id="contenuDetail"><legend> Détaille du QCM :</legend>'.$contenu.'</fieldset>';
}
?>