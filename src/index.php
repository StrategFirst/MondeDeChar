<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">

<head>
	<meta charset="utf-8" />
	<title> Monde de Char </title>

	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/index.css">
</head>

<body>

	<input class="nav-memory" type="radio" name="menu" id="select-clan" checked/>
	<input class="nav-memory" type="radio" name="menu" id="select-joueur" />
	<input class="nav-memory" type="radio" name="menu" id="select-char" />
	
	<header>
		<nav>
			<label for="select-clan"> Clan </label>
			<label for="select-joueur"> Joueur </label>
			<label for="select-char"> Char </label>
		</nav>
	</header>

	<main>

		<iframe id="pane-clan" src="clan.php" frameborder="0"></iframe>
		<iframe id="pane-joueur" src="joueur.php" frameborder="0"></iframe>
		<iframe id="pane-char" src="char.php" frameborder="0"></iframe>
	</main>
<!-- 		

		
	<aside>
		<iframe src="who_is.php"> </iframe>
	</aside>
	 -->

</body>

</html>
