<?php 
	session_start();
	if($_SESSION['con'] != 'true')
		echo "<script type='text/javascript'>window.location='login.php';</script>";
	
	$_SESSION['newTry'] = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="images/style.css">
<link rel="stylesheet" href="images/anythingslider.css">
<script type="text/javascript" src="jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="images/jquery.anythingslider.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#resultat').css('display', 'none');
		$('#next').click(function(){alert('ok');});
		var Tmn = 3;
		var sc = 0;
		var tm = '';
		var mn = Tmn;
		//Compteur
		function Go(){
			setTimeout(function(){
				sc--;
				if(sc<0){sc=59; mn--;}
		
				tm=((mn<10)?"0":" ") + mn +":";
				tm+=((sc<10)?"0":" ") + sc +"";
				var x = mn/3;
				if(x<=1 && x>=2/3)
					$('#time').css('color', 'green');
				else if(x<=2/3 && x>=1/3)
					$('#time').css('color', 'orange');
				else
					$('#time').css('color', 'red');

				$('#time').val(tm);
				if(mn == 0 && sc == 0)
					{resultatQCM(); }
				Go();
			}, 1000);
		}
	
		Go();
	
		var TabDiv = new Array();
		var i = 0;
		var nQ = $('div').length;
		//Cette partie de code permet de cacher ou apparaitre les boutton de slider next et previews 
		//selon la question, si elle est la 1° question button previews sera cacher, si elle la dernière next sera cacher
		$('#QCM').each(function(index){
			TabDiv[index] = '#'+$(this).attr('id');
			if(index != 0){
				$(this).hide();
			}else
				$(this).children('#previews').hide();
			
			if(index == nQ-1)$(this).children('#next').hide();
		
			
			/*$(this).children('#next').click( function () {
				$(TabDiv[i]).fadeOut();
				i++;
				$(TabDiv[i]).fadeIn();
				alert('ok');
    		});
			$(this).children('#previews').click( function () {
				$(TabDiv[i]).fadeOut();
				i--;
				$(TabDiv[i]).fadeIn();
				if(i==0) $(this).children('#previews').hide();
    		});*/
  		});
		
		//Lorsqu 'on clique sur le button résultat avant que le temps s'achever.
		$('#resultat').click(function(){
			resultatQCM();
		});
		
		//cette function permet de récupérer les reponses séléctées et les mettre dans un tableau, puis les envois
		// au reponse.php qui les traite et retourne la réponses et envoi un émail. 
		function resultatQCM(){
			$('#chrono').hide();
			var tab = [];
			$("input:checked[name='check[]']").each(function(){
				tab.push($(this).attr('id'));
			});
			$('div').hide();//Cacher les Questions et Afficher le résultat.
			var rps = '';
			for(i=0; i<tab.length; i++)
				rps += tab[i];
				
			/*Récupérer le temps écoulé*/
			if(sc>0){sc = 60 - sc; mn = 3 -(mn+1);}
			else mn = 3 - mn;				
			
			var time = mn + ':' + sc;
			
			$.ajax({
				type:'POST',
				url:'reponse.php',
				data:'data='+rps+'&time='+time,
				cache:false,
				success: function(result){
					if(result)
						$('#main').html(result);
				}
			});
		}
		
		
		$('#next').click(function(){alert('ok');});
		var curentQuestion = 0;
		//Function de slider
		$('#QCM').anythingSlider({
				theme           : 'metallic',
				easing          : 'easeInOutBack',
				enableStartStop : true,
				buildArrows     : true, //Afficher les butons de nav
				buildNavigation     : false,
				onSlideComplete : function(slider){
					//alert('Welcome to Slide #' + slider.currentPage);
					/*curentQuestion++;
					if(curentQuestion == nbQuestion-1)
						$('#resultat').css('display', 'block');*/
					$('.anythingSlider .back a ').css('display', 'block');
					$('.anythingSlider .forward a').css('display', 'block');
					if(slider.currentPage == 1)
						$('.anythingSlider .back a ').css('display', 'none');
					if(slider.currentPage == nbQuestion){
						$('.anythingSlider .forward a').css('display', 'none');
						$('#resultat').css('display', 'block');
					}
				}
			});
		
	});/*==  End ready  ==*/
	
	function detailler(){
		document.getElementById('detail').style.display = 'none';
		document.getElementById('contenuDetail').style.display = 'block';
	}
</script>
<title>-- QCM --</title>
</head>

<body>
	<h1 style="color:#d9d9d9">QCM - <?php echo $_SESSION['utilisateur']; ?></h1>
	<ul class="header-content">
		<li><a href="">Nouvelle Essaie</a></li>
		<li><a id="resultat" href="javascript:">Résultat</a></li>
		<li><a href='Login.php'>Deconnexion</a></li>		
	</ul>
	<div></div>
	<p id="main" style="margin-top:75px; padding:20px; text-align:left">	
<?php 
	include 'ConnexionDB.php';
		
	$qcm = mysql_query('SELECT * FROM Questions');
	$nbQuestion =mysql_fetch_array(mysql_query('SELECT count(*) as NB FROM Questions'));
	echo '<script type="text/javascript"> var nbQuestion = '.$nbQuestion["NB"].';</script>';
	$q= 0;
	$contenu = '';
	$point = 0;
	$contenu .= '<ul id="QCM" style="top:50px; text-align:left">';
	while($data = mysql_fetch_array($qcm)){
		$point =  $point + $data['Note'];
		$q++;
		$contenu .= '<li><h3> - '.$data['Questions'].'  ('.$q.'/'.$nbQuestion['NB'].')   --  '.$point['Note'].' Point(s)  --</h3>';
		$reponds =  mysql_query('SELECT * FROM Reponse WHERE Id_Question = ' . $data['Id']);
		while($dataR = mysql_fetch_array($reponds)){
			$contenu .= '<input id="'.$dataR['Id'].';" name="check[]" type="checkbox">'.$dataR['reponse'].'<br/>';
		}
		$contenu .= '</li>';
	}
	$contenu .= '</ul>';
	echo $contenu;
?>
	<p id="chrono">
		<input type="text" id="time" name="time" readonly="yes" style="font-size:28px; margin:50px; font-weight:bolder; border:0"/>
	</p>
</p>

</body>
</html>
