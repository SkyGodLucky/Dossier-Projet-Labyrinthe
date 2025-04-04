<?php
// Inclut le fichier contenant les fonctions nécessaires
require_once("./assets/php/function.php");

// Inclut le fichier pour gérer les cookies
require("./assets/php/cookiesManager.php");

// Démarre une nouvelle session ou reprend une session existante
session_start();

// Vérifie si la grille n'est pas encore initialisée dans la session
if (!isset($_SESSION['grid'])) {
  // Initialise la grille avec une grille aléatoire
  $_SESSION['grid'] = generateRandomGrid();
}

// Vérifie si une réinitialisation du jeu est demandée via le paramètre "reset"
if (isset($_GET['reset'])) {
  // Sauvegarde la langue actuelle
  $langueActuelle = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'fr';
  
  // Réinitialise la session
  session_start();
  
  // Charge une nouvelle grille aléatoire
  $_SESSION['grid'] = generateRandomGrid();
  
  // Réinitialise l'état de victoire
  $_SESSION['victory'] = false;
  
  // Restaure la langue précédente
  $_SESSION['lang'] = $langueActuelle;
  
  // Redirige vers la page sans le paramètre "reset"
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
  exit();
}

// Vérifie si une requête POST a été envoyée pour gérer un mouvement
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["direction"])) {
  // Récupère la direction du mouvement depuis le formulaire
  $direction = $_POST["direction"];

  // Crée une copie profonde de la grille actuelle
  // Utilise array_values pour réindexer les lignes de la grille
  // Cela permet de s'assurer que les indices des lignes sont consécutifs
  // et que les cellules sont correctement réindexées
  $grille = [];
  foreach ($_SESSION['grid'] as $ligne) {
    $grille[] = array_values($ligne);
  }

  // Effectue le déplacement dans la grille et récupère la nouvelle grille et le statut
  // Utilise la fonction shift pour déplacer le joueur dans la direction spécifiée
  // La fonction shift prend en paramètre la grille actuelle et la direction du mouvement
  // Elle retourne la nouvelle grille après le mouvement et un message de débogage
  // qui indique si le mouvement est valide ou non
  [$nouvelleGrille, $debugDeplacement] = shift($grille, $direction);

  // Vérifie si le mouvement est invalide
  if ($debugDeplacement === "invalid") {
    // Définit un message d'erreur dans la session
    $_SESSION['error'] = true;
  } else {
    // Supprime le message d'erreur si le mouvement est valide
    $_SESSION['error'] = false;
    // Met à jour la grille dans la session avec la nouvelle grille
    $_SESSION['grid'] = [];
    foreach ($nouvelleGrille as $ligne) {
      $_SESSION['grid'][] = array_values($ligne);
    }
  }
}

// Récupère la grille actuelle depuis la session ou initialise une grille aléatoire
$grid = $_SESSION['grid'] ?? generateRandomGrid();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Le Labyrinthe</title>
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>

<body class="<?= $_COOKIE["theme"] ?>">
  <?php require("./assets/php/header.php") ?>
  <main>
    <div class="game-container">
      <div class="grid-container">
        <div class="grid-table">
          <?php
          // Remplace les cellules de la grille par des images et ajoute la classe "fog" si nécessaire
          // Utilise la fonction replaceWithImages pour remplacer les cellules par des images
          replaceWithImages($grid);
          // Affiche la grille avec les images
          // Utilise la fonction displayGrid pour afficher la grille
          displayGrid($grid);
          ?>
        </div>
      </div>
      <div class="controls-container">
        <div class="direction-arrow-container">
          <div></div>
          <div class="direction-arrow arrow-up">
            <form method="POST">
              <input type="hidden" name="direction" value="up">
              <button type="submit">
                <img src="./assets/images/cercle-avec-une-fleche-pointant-vers-le-haut.png" alt="Arrow" />
              </button>
            </form>
          </div>
          <div></div>
          <div class="direction-arrow arrow-left">
            <form method="POST">
              <input type="hidden" name="direction" value="left">
              <button type="submit">
                <img src="./assets/images/cercle-avec-une-fleche-pointant-vers-la-gauche.png" alt="Arrow" />
              </button>
            </form>
          </div>
          <div></div>
          <div class="direction-arrow arrow-right">
            <form method="POST">
              <input type="hidden" name="direction" value="right">
              <button type="submit">
                <img src="./assets/images/cercle-avec-une-fleche-pointant-vers-la-droite.png" alt="Arrow" />
              </button>
            </form>
          </div>
          <div></div>
          <div class="direction-arrow arrow-bot">
            <form method="POST">
              <input type="hidden" name="direction" value="down">
              <button type="submit">
                <img src="./assets/images/cercle-avec-une-fleche-pointant-vers-le-bas.png" alt="Arrow" />
              </button>
            </form>
          </div>
          <div></div>
        </div>
        <a href="?reset=1" class="reset-button"><?= $_SESSION["lang"] === "en" ? "Play Again" : "Rejouer" ?></a>
      </div>
    </div>
    <!-- // Affiche un message de victoire si le joueur a trouvé l'os
    // Vérifie si le joueur a trouvé l'os en vérifiant la variable de session "victory" 
    // Si la variable de session "victory" est définie et vraie, cela signifie que le joueur a trouvé l'os
    // Dans ce cas, un message de victoire est affiché
    // Le message de victoire est affiché dans une superposition (overlay) qui couvre l'écran
    // et un bouton de fermeture est inclus pour permettre au joueur de redémarrer le jeu
    // Le message de victoire est affiché en utilisant la langue sélectionnée par le joueur -->
    <?php if (isset($_SESSION['victory']) && $_SESSION['victory']): ?>
      <div class="victory-overlay" onclick="window.location.href='?reset=1'"></div>
      <div class="victory-message">
        <button class="victory-close" onclick="window.location.href='?reset=1'"></button>
        <h2><?= $_SESSION["lang"] === "en" ? "Congratulations!" : "Félicitations !" ?></h2>
        <p><?= $_SESSION["lang"] === "en" ? "You have found the bone!" : "Vous avez trouvé l'os !" ?></p>
      </div>
    <?php endif; ?>
    <!-- // Affiche un message d'erreur si le joueur essaie de traverser un mur
    // Vérifie si une erreur est présente dans la session -->
    <?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
      <div class="error-message">
        <p><?= $_SESSION["lang"] === "en" ? "⚠️ You can't go through walls!" : "⚠️ Vous ne pouvez pas traverser les murs !" ?></p>
      </div>
    <?php endif; ?>
  </main>
  <?php require("./assets/php/footer.php") ?>
</body>

</html>