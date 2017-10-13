<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// index page: index page to protect code and includes the chat.php script    //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 22/02/17: Create file                                                      //
// 11/10/17: Fixed some small bugs                                            //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// -------------------------------------------------------------------------- //

// Load modules
require_once 'chat.php';
$options = array(
  "spaceshead" => "    ",
  "spacesbody" => "      ",
  "location" => "",
  "chatwidth" => "100%",
  "chatheight" => "80%",
  "chatfull" => "true",
  "chatlocation" => "middle",
  "chattitle" => "Php Web Chat"
);
$chat = new pwci($options);

?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <title>Php Web Chat Integrator</title>
    <!-- META -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php echo $chat->set_scripts(); ?>
    <!-- STYLE -->
    <style>
      body{background-color:#fff;font-size:.9em;font-size:14px;font-family:"Lucida Sans Unicode",Arial,Helvetica,Verdana,sans-serif;line-height:2}*{margin:0;padding:0}code,strong{font-size:12.6px;font-family:Menlo,Monaco,Consolas,"Courier New",monospace;color:#546172;background:#ecf3f8;padding:4px 5px;border-radius:0;box-sizing:border-box}a{color:#2e87ca;text-decoration:none}a:hover{color:#08c;text-decoration:underline}.container{width:100%;height:100%}.container h1{text-align:center;padding:2em}
    </style>
  </head>
  <body>
    <div class="container">
      <h1><a href="https://github.com/OperationsResearch/pwci">PWCi</a> - a Php Web Chat Integrator</h1>
<?php echo $chat->set_chat(); ?>
    </div>
  </body>
</html>