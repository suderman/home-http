<?php

// Load YAML configs
$GLOBALS['controllers'] = yaml_parse_file('controllers.yml');
$GLOBALS['commands'] = yaml_parse_file('commands.yml');

function list_devices(){
  echo "<h2>API:</h2>";
  echo "<ul>";
  foreach($GLOBALS['commands'] as $key => $val) {
    echo "<li>$key ";
    list_commands($key);
    echo "</li>";
  }
  echo "</ul>";
}

function list_commands($device){
  $host =  $_SERVER["SERVER_NAME"];
  echo "<ul>";
  foreach($GLOBALS['commands'][$device] as $key => $val) {
    echo "<li><a href='http://$host/$device/$key'>/$device/$key</a></li>";
  }
  echo "</ul>";
}

function parse_command($device, $command) {
  $cmd = $GLOBALS['commands'][$device][$command];
  foreach($GLOBALS['controllers'] as $key => $val) {
    $cmd = str_replace("{$key}:", $val, $cmd);
  }
  return $cmd;
}

function run_command($device, $command) {
  $cmd = parse_command($device, $command);
  echo file_get_contents($cmd);
}

# Get the command from the URL
if (isset($_SERVER["REDIRECT_URL"])) {
  $params = ltrim($_SERVER["REDIRECT_URL"], '/');
  @list($device, $command) = explode('/', $params);

  if (($device) and ($command)) {
  echo run_command($device, $command);
  } else {
    list_commands($device);
  }

} else {

  list_devices();
}
