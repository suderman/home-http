<?php

// Load YAML configs
$GLOBALS['controllers'] = yaml_parse_file('controllers.yml');
$GLOBALS['commands'] = yaml_parse_file('commands.yml');

function list_devices(){
  $host =  $_SERVER["SERVER_NAME"];
  echo "<div class=container>";
  echo "<h1>$host:</h1>";
  echo "<dl class=dl-horizontal>";
  foreach($GLOBALS['commands'] as $key => $val) {
    echo "<dt>$key</dt>";
    echo "<dd>";
    list_commands($key);
    echo "</dd>";
  }
  echo "</div>";
  echo "</dl>";
}

function list_commands($device){
  $host =  $_SERVER["SERVER_NAME"];
  echo "<ul class=list-unstyled>";
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
  $host =  $_SERVER["SERVER_NAME"];
?>
<!DOCTYPE html>
<html><head>
<title><? echo $host; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css" rel="stylesheet">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function(){
    $('a').bind('click',function(event){
      event.preventDefault();
      $.get(this.href,{}); 
    })
  });
</script>
<body>
<?

  list_devices();
}
