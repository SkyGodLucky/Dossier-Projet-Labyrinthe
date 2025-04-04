<?php
if (!isset($_SESSION["lang"])) {
  $_SESSION["lang"] = "fr";
}
if (isset($_GET["lang"])) {
  $_SESSION["lang"] = htmlspecialchars($_GET["lang"]);
}
?>

<header>
  <h1><img src="./assets/images/logo.png" alt="Logo Labyrinthe"></h1>
  <nav>
    <ul>
      <li>
        <form id="fr" method="GET">
          <input type="hidden" name="lang" value="fr" />
          <button>
            <img src="./assets/images/fr.svg" />
          </button>
        </form>
      </li>
      <li>
        <a href="?lang=en" id="en">
          <img src="./assets/images/uk.svg" />
        </a>
      </li>
      <div id="theme">
        <li>
          <a href="?theme=dark">
            <img src="./assets/images/lune.png" />
          </a>
        </li>
        <li>
          <a href="?theme=light">
            <img src="./assets/images/soleil.png" />
          </a>
        </li>
      </div>
    </ul>
  </nav>
</header>