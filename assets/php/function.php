<?php function grid1()
{
  return [
    ["1", "0", "2", "2", "2", "2",],
    ["2", "0", "0", "0", "2", "2",],
    ["2", "2", "2", "0", "2", "2",],
    ["2", "2", "0", "0", "2", "2",],
    ["2", "2", "0", "2", "2", "2",],
    ["2", "2", "0", "0", "2", "2",],
    ["2", "2", "2", "0", "0", "3",]
  ];
}

function grid2()
{
  return [
    ["1", "2", "0", "2", "0", "2",],
    ["0", "0", "0", "0", "0", "0",],
    ["2", "2", "2", "2", "0", "2",],
    ["0", "0", "0", "0", "0", "0",],
    ["0", "2", "0", "2", "2", "2",],
    ["2", "2", "0", "0", "0", "0",],
    ["3", "0", "0", "2", "0", "2",]
  ];
}

function replaceWithImages(&$grid)
{
  // Trouver la position du chien
  $dogPosition = null;
  foreach ($grid as $rowIndex => $row) {
    foreach ($row as $colIndex => $cell) {
      if ($cell === "1") {
        $dogPosition = [$rowIndex, $colIndex];
        break 2;
      }
    }
  }

  // Remplacer les cellules par des images et ajouter la classe fog
  foreach ($grid as $rowIndex => &$row) {
    foreach ($row as $colIndex => &$cell) {
      $isNearDog = false;
      if ($dogPosition) {
        // Vérifier si la cellule est à proximité du chien
        // La proximité est définie comme étant dans une distance de 1 cellule (horizontale ou verticale)
        // abs est utilisé pour calculer la distance absolue entre les indices de ligne et de colonne
        $distance = abs($rowIndex - $dogPosition[0]) + abs($colIndex - $dogPosition[1]);
        $isNearDog = $distance <= 1;
        // Si la cellule est à proximité du chien, on enlève la classe fog
      }

      switch ($cell) {
        case "1":
          $cell = "<img src='./assets/images/chien.png' alt='dog' class='grid-image'>";
          $cell = "<div class='grid-cell dog'>" . $cell . "</div>";
          break;
        case "2":
          // Si la cellule est un mur (2), on ajoute une image de mur
          $cell = "<img src='./assets/images/mur-de-briques.png' alt='wall' class='grid-image'>";
          $cell = "<div class='grid-cell" . (!$isNearDog ? " fog" : "") . "'>" . $cell;
          if (!$isNearDog)
          // On ajoute la classe fog si la cellule n'est pas à proximité du chien
          // On ajoute une image de brouillard si la cellule n'est pas à proximité du chien
          {
            $cell .= "<img src='./assets/images/fog.png' alt='fog' class='fog-overlay'>";
          }
          $cell .= "</div>";
          break;
        case "3":
          // Si la cellule est un os (3), on ajoute une image d'os
          $cell = "<img src='./assets/images/os.png' alt='os' class='grid-image'>";
          $cell = "<div class='grid-cell" . (!$isNearDog ? " fog" : "") . "'>" . $cell;
          if (!$isNearDog)
          // On ajoute la classe fog si la cellule n'est pas à proximité du chien
          // On ajoute une image de brouillard si la cellule n'est pas à proximité du chien
          {
            $cell .= "<img src='./assets/images/fog.png' alt='fog' class='fog-overlay'>";
          }
          $cell .= "</div>";
          break;
        case "0":
          // Si la cellule est vide (0), on laisse la cellule vide
          $cell = "<div class='grid-cell" . (!$isNearDog ? " fog" : "") . "'>";
          if (!$isNearDog)
          // On ajoute la classe fog si la cellule n'est pas à proximité du chien
          // On ajoute une image de brouillard si la cellule n'est pas à proximité du chien
          {
            $cell .= "<img src='./assets/images/fog.png' alt='fog' class='fog-overlay'>";
          }
          $cell .= "</div>";
          break;
      }
    }
  }
}

function displayGrid($grid)
{
  foreach ($grid as $row) {
    echo "<div class='grid-row'>";
    foreach ($row as $cell) {
      echo "<div class='grid-cell'>$cell</div>";
    }
    echo "</div>";
  }
}

function shift($grid, $direction)
{
  // Créer une copie de la grille pour le traitement
  $newGrid = [];
  foreach ($grid as $row) {
    // Utiliser array_values pour réindexer les colonnes
    // Cela permet de s'assurer que les colonnes sont correctement indexées après le traitement
    $newGrid[] = array_values($row);
  }

  // Trouver la position actuelle du chien (1)
  $dogPosition = null;
  foreach ($newGrid as $rowIndex => $row) {
    foreach ($row as $colIndex => $cell) {
      // Vérifier si la cellule contient le chien (1)
      // Si oui, on stocke sa position
      if ($cell === "1") {
        $dogPosition = [$rowIndex, $colIndex];
        break 2; // Sortir des deux boucles dès qu'on trouve le chien
      }
    }
  }

  // Si le chien n'est pas trouvé, retourner la grille inchangée et un message vide
  // Cela peut arriver si la grille est vide ou si le chien a été retiré
  if (!$dogPosition) {
    return [$newGrid, ""];
  }

  // Extraire la position actuelle du chien
  // $dogPosition est un tableau contenant la position actuelle du chien
  [$currentRow, $currentCol] = $dogPosition;

  // Déterminer la nouvelle position en fonction de la direction
  $newRow = $currentRow;
  $newCol = $currentCol;

  // Utiliser un switch pour gérer les directions de mouvement
  // Les directions possibles sont : up, down, left, right
  // En fonction de la direction, on met à jour les indices de ligne et de colonne
  // Cela permet de déplacer le chien dans la grille
  switch ($direction) {
    case 'up':
      $newRow--;
      break;
    case 'down':
      $newRow++;
      break;
    case 'left':
      $newCol--;
      break;
    case 'right':
      $newCol++;
      break;
    default:
      return [$newGrid, ""];
      // Si la direction n'est pas valide, retourner la grille inchangée et un message vide
  }

  // Vérifier si la nouvelle position est dans les limites et n'est pas un mur (2)
  if (
    isset($newGrid[$newRow][$newCol]) && // Vérifier si la nouvelle position existe dans la grille
    $newGrid[$newRow][$newCol] !== "2" // Vérifier si la nouvelle position n'est pas un mur (2)
  ) {
    // Vérifier si le chien attrape l'os (3)
    if ($newGrid[$newRow][$newCol] === "3") {
      $_SESSION['victory'] = true;
    }

    // Déplacer le chien vers la nouvelle position
    $newGrid[$currentRow][$currentCol] = "0"; // Effacer l'ancienne position
    $newGrid[$newRow][$newCol] = "1"; // Définir la nouvelle position
    return [$newGrid, ""]; // Retourner la nouvelle grille et un message vide
  }

  return [$newGrid, "invalid"]; // Retourner la grille inchangée et un message d'erreur
}

function generateRandomGrid() {
    $rows = 7;
    $cols = 6;
    $grid = array_fill(0, $rows, array_fill(0, $cols, "0"));
    
    // Placer le chien (1) dans la première ligne
    $dogCol = rand(0, $cols - 1);
    $grid[0][$dogCol] = "1";
    
    // Placer l'os (3) dans la dernière ligne
    $boneCol = rand(0, $cols - 1);
    $grid[$rows - 1][$boneCol] = "3";
    
    // Placer des murs (2) de manière aléatoire
    $wallCount = rand(10, 15); // Nombre de murs à placer
    $placedWalls = 0;
    
    while ($placedWalls < $wallCount) {
        $row = rand(0, $rows - 1);
        $col = rand(0, $cols - 1);
        
        // Ne pas placer de mur sur le chien ou l'os
        if ($grid[$row][$col] === "0") {
            $grid[$row][$col] = "2";
            $placedWalls++;
        }
    }
    
    // Vérifier si le chemin est valide (chien peut atteindre l'os)
    if (!isPathValid($grid)) {
        // Si le chemin n'est pas valide, on recommence
        return generateRandomGrid();
    }
    
    return $grid;
}

function isPathValid($grid) {
    // Récupère les dimensions de la grille (nombre de lignes et de colonnes)
    $rows = count($grid);
    $cols = count($grid[0]);
    
    // Initialise la position du chien à null
    $dogPos = null;
    
    // Parcourt toutes les cellules de la grille pour trouver la position du chien (1)
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            // Si on trouve le chien (1), on stocke sa position et on sort des deux boucles
            if ($grid[$i][$j] === "1") {
                $dogPos = [$i, $j];
                break 2;
            }
        }
    }
    
    // Si on n'a pas trouvé le chien, le labyrinthe n'est pas valide
    if (!$dogPos) return false;
    
    // Crée un tableau de visite pour marquer les cases déjà explorées
    // Initialise toutes les cases à false (non visitées)
    $visited = array_fill(0, $rows, array_fill(0, $cols, false));
    
    // Initialise la pile avec la position du chien pour commencer l'exploration
    $stack = [$dogPos];
    
    // Tant qu'il y a des cases à explorer dans la pile
    while (!empty($stack)) {
        // Récupère la dernière position ajoutée à la pile (principe du DFS)
        // $stack est un tableau de positions (lignes et colonnes)
        // array_pop retire le dernier élément du tableau et le retourne
        // $row et $col sont les coordonnées de la position actuelle
        [$row, $col] = array_pop($stack);
        
        // Si on trouve l'os (3), le chemin est valide
        if ($grid[$row][$col] === "3") {
            return true;
        }
        
        // Si la case a déjà été visitée, on passe à la suivante
        if ($visited[$row][$col]) continue;
        
        // Marque la case actuelle comme visitée
        $visited[$row][$col] = true;
        
        // Définit les quatre directions possibles : haut, bas, gauche, droite
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];
        
        // Explore les quatre directions possibles
        foreach ($directions as [$dr, $dc]) {
            // Calcule la nouvelle position
            $newRow = $row + $dr;
            $newCol = $col + $dc;
            
            // Vérifie si la nouvelle position est :
            // - Dans les limites de la grille
            // - Non visitée
            // - Pas un mur (2)
            if ($newRow >= 0 && $newRow < $rows && 
                $newCol >= 0 && $newCol < $cols && 
                !$visited[$newRow][$newCol] && 
                $grid[$newRow][$newCol] !== "2") {
                // Ajoute la nouvelle position à la pile pour l'explorer plus tard
                $stack[] = [$newRow, $newCol];
            }
        }
    }
    
    // Si on a exploré toutes les cases possibles sans trouver l'os, le chemin n'est pas valide
    return false;
}
