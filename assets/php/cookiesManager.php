<?php
if (!isset($_COOKIE["theme"])) {
  setcookie("theme", "light", ["expires" => time() + 3600]);
  header("Refresh:0");
}
if (isset($_GET["theme"])) {
  setcookie("theme", htmlspecialchars($_GET["theme"]), ["expires" => time() + 3600]);
  header("Refresh:0; url=" . $_SERVER['PHP_SELF']);
}
