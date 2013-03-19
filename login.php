<?php
		session_start();	
?>
<html>
    <head>
        <title>Authentification</title>
        <link rel="stylesheet" type="text/css" href="images/style.css">
    </head>

<body style="text-align:center; ">
    <div style="color:#d9d9d9">
         <h1>Connexion</h1>
		 <h2>Ouvrir une session</h2>
    </div>
    <form name="form1" method="post">
        <div>
            <fieldset style="width:600;">
                <legend>Informations de compte</legend>
                
                    <table style="font-family:Verdana">
                        <tr>
                            <td>Nom Utilisateur : </td><td><input type="text" width="50" name="nom"/></td>
                        </tr>
                        <tr>
                            <td>Email: </td><td><input type="text" width="50" name="email"/></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><input type="submit" value="Se Connecter" /></td>
                        </tr>
						<?php
							$_SESSION['con'] = 'false';
							$_SESSION['utilisateur'] = '';
							
							include 'ConnexionDB.php'; 
							$echec = 'true';
	 						if(isset($_POST["nom"])!='' && isset($_POST["email"])!='')
							{
								$auth = mysql_query('SELECT * FROM Authentification');
								while($data = mysql_fetch_array($auth)){
									if($data['Email'] == $_POST["email"]){  
										$_SESSION['id_User'] = $data['ID'];
										$_SESSION['utilisateur'] = $data['Nom'];
										$_SESSION['email'] = $data['Email'];
										$_SESSION['con'] = 'true';
										echo "<script type='text/javascript'>window.location='index.php';</script>";
										break;
									}	
									else
										$echec = 'false';
								}
								if($echec=='false')
									echo'<tr><td colspan="2" align="center" style="color:Red">Nom  ou Email INCORRECTE</td></tr>';
							}
							mysql_close();
						?>
                    </table>
					
            </fieldset>
        </div>
    </form>
	
    </body>
</html>

