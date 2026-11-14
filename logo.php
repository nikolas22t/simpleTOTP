<?php
// Logo lookup: logo.php?n=<service name>. Map in logos.json (keyword => domain); icons cached in logos/.
$n = strtolower(trim($_GET["n"] ?? ""));
$map = json_decode(@file_get_contents(__DIR__."/logos.json"), true) ?: [];
$key = null;
if (isset($map[$n])) $key = $n;
else {
  $base = preg_replace("/[^a-z0-9]+.*$/", "", preg_replace("/\.(com|net|io|org)\b.*/", "", $n)); // "myheritage.com/" -> myheritage, "kraken-x" -> kraken
  if (isset($map[$base])) $key = $base;
}
if (!$key) { http_response_code(404); exit; }
$domain = $map[$key];
$file = __DIR__."/logos/".preg_replace("/[^a-z0-9.-]/", "", $domain).".png";
if (!is_file($file) || filesize($file) < 100) {
  $ctx = stream_context_create(["http"=>["timeout"=>6,"user_agent"=>"Mozilla/5.0"]]);
  $img = @file_get_contents("https://www.google.com/s2/favicons?sz=128&domain=".urlencode($domain), false, $ctx);
  if (!$img || strlen($img) < 100) { http_response_code(404); exit; }
  file_put_contents($file, $img);
}
header("Content-Type: image/png");
header("Cache-Control: public, max-age=2592000");
readfile($file);
