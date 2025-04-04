function shift($grid, $direction)
{
  // Créer une copie de la grille pour le traitement
  $newGrid = [];
  foreach ($grid as $row) {
    $newGrid[] = array_values($row);
  }
  
  // Find the current position of the dog (1)
  $dogPosition = null;
  foreach ($newGrid as $rowIndex => $row) {
    foreach ($row as $colIndex => $cell) {
      if ($cell === "1") {
        $dogPosition = [$rowIndex, $colIndex];
        break 2;
      }
    }
  }

  if (!$dogPosition) {
    return $newGrid;
  }

  [$currentRow, $currentCol] = $dogPosition;

  // Determine the new position based on the direction
  $newRow = $currentRow;
  $newCol = $currentCol;

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
      return $newGrid;
  }

  // Check if the new position is within bounds and not a wall (2)
  if (
    isset($newGrid[$newRow][$newCol]) &&
    $newGrid[$newRow][$newCol] !== "2"
  ) {
    // Check if the dog catches the bone (3)
    if ($newGrid[$newRow][$newCol] === "3") {
      $_SESSION['victory'] = true;
    }
    
    // Move the dog to the new position
    $newGrid[$currentRow][$currentCol] = "0"; // Clear the old position
    $newGrid[$newRow][$newCol] = "1"; // Set the new position
    return $newGrid;
  }
  
  return "invalid";
} 