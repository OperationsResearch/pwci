<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// Admin page: Will be used to install het chat system                        //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 22/02/17: Create file                                                      //
// 20/08/17: addded comments                                                  //
// 11/10/17: Fixed some small bugs                                            //
// 14/11/18: added location param                                             //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// -------------------------------------------------------------------------- //

// Load modules
require_once 'lib/telegram/telegramUser.php';
require_once 'lib/telegram/telegramTools.php';
require_once 'lib/telegram/telegramMessenger.php';
$connected = telegramTools::config_exist();
if ($connected != false){
  $settings = telegramTools::load_settings();
  if ($connected == "connected") {
    $answer = array("status" => true, "message" => "");
  } else {
    $_REQUEST["config"] = "verifyAdmin";
  }
}

if (isset($_REQUEST["config"]) && $_REQUEST["config"] != "") {
  $stepconfig = $_REQUEST["config"];
  $settings["location"] = $_SERVER["HTTP_HOST"].str_replace("admin.php","",$_SERVER["REQUEST_URI"]);
  if (isset($_REQUEST["timezone"])) {
    if ($_REQUEST["timezone"] != "0") {
      $settings["timezone"] = $_REQUEST["timezone"];
    } else {
      header("Location: admin.php");
      exit();
    }
  }
  if(isset($_REQUEST["token"]) && $_REQUEST["token"] != "" && $stepconfig == "verifyBot") {
    $settings["token"] = $_REQUEST["token"];
    telegramTools::save_settings($settings, $_REQUEST["token"]);
    $settings = Messenger::verifyBot();
  } elseif ($stepconfig == "verifyAdmin") {
    $answer = Messenger::verifyAdmin();
    //$settings = telegramTools::load_settings();
  } elseif ($stepconfig == "deleteConfig") {
    if ($_REQUEST["token"] == $settings["token"]) {      
      telegramTools::reset_settings();
    }
    header("Location: admin.php");
  } else {
    header("Location: admin.php");
  }
}

### Function: Sort arrays by time
function sortByOrder($a, $b) {
  return $a["time"]-$b["time"];
  //return strcmp($a['diff_from_GMT'], $b['diff_from_GMT']);
}

### Function: Gets a timezone list
function timezonelist() {
  $zones = array();
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) {
    date_default_timezone_set($zone);
    $zones[$key]["zone"] = $zone;
    $zones[$key]["time"] = date("O", $timestamp);
    $zones[$key]["diff_from_GMT"] = "UTC/GMT ".date("P", $timestamp);
  }
  usort($zones, "sortByOrder");
  return $zones;
}

?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <title>Php Web Chat Integrator</title>
    <!-- META -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <!-- STYLE -->
    <style>
      body{background-color:#fff;font-size:.9em;font-size:14px;font-family:"Lucida Sans Unicode",Arial,Helvetica,Verdana,sans-serif;line-height:2}*{margin:0;padding:0}code,strong{font-size:12.6px;font-family:Menlo,Monaco,Consolas,"Courier New",monospace;color:#546172;background:#ecf3f8;padding:4px 5px;border-radius:0;box-sizing:border-box}a{color:#2e87ca;text-decoration:none}a:hover{color:#08c;text-decoration:underline}.container{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;padding:2em}.examplecontainer{margin:.5em 0;padding:1em;clear:both;overflow:hidden}.examplecontainer .left{width:75%;float:left}.examplecontainer ul li{text-align:left;line-height:1.5;background-image:url(images/bullet.png);background-repeat:no-repeat;background-position:0 8px;padding-left:20px;list-style-type:none}li{display:list-item;text-align:-webkit-match-parent}li strong{background:#feeae4;color:#c61717}form{margin:0 auto;padding:1em 0 0;clear:both}form input{width:300px;text-align:center;height:20px}form button{padding:.5em}.hover{float:right;width:25%;color:#fff;text-align:left}.hover img{opacity:.7;width:20%;position:fixed}.hover img:hover{width:100%;opacity:1;top:-100px;left:0;z-index:5}
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Config Php Web Chat Integrator</h1>
      <div class="examplecontainer">
<?php if (isset($answer) && $answer["status"] === true) {
  echo "        The config is set.<br />\n";
  echo "        ".$answer["message"];
?>
        <form action="admin.php" method="post" name="adminForm" id="adminForm">
          <input type="hidden" name="config" value="deleteConfig" />
<?php   if ($answer["message"] == "") { ?>
          <label for="token">Your token:</label>
          <input id="token" name="token" type="text" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11" />
<?php   } else { echo "          <input type=\"hidden\" id=\"token\" name=\"token\" value=\"".$settings["token"]."\" />\n"; } ?>
          <button type="submit" id="Send">Reset config (can't be undone!!!)</button>
        </form>
<?php } elseif (!isset($stepconfig)) { ?>
        <p>Php Web Chat integragor use the open api of <a href="https://telegram.org" title="Telegram" target="_blank">Telegram</a>. Telegram was introduced as a chat app between mobiles (it's linked to your cell phone), over the years they added so much features, that today there are desktop-apps (that work without phone) and bots. So if you don't have telegram yet, start by creating an account and follow the steps.</p>
        Open your <a href="https://telegram.org/apps" title="Download telegram client" target="_blank">telegram client</a> and start a conversation with <a href="https://telegram.me/botfather" title="Start conversation with BotFather" target="_blank">BotFather</a>
        <div class="left">
          In the conversation window of the BotFather:<br  />
          <ul>
            <li>create a new bot by typing <code>/newbot</code></li>
            <li>Follow the instructions from the BotFather</li>
            <li>Give your bot a name: <code><?php echo (isset($stepconfig)?$settings["bot_name"]:"Name of your Bot"); ?></code></li>
            <li>The system will create the bot and will now ask for a username</li>
            <li>Provide one that ends with the word "bot": <code><?php echo (isset($stepconfig)?$settings["username"]:"yourUsernameBot"); ?></code></li>
            <li>The bot is now created and you will receive a token.<br /><?php echo (isset($stepconfig)?"your token: <code>".$settings["token"]:"somehting like:<code>123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11"); ?></code></li>
          </ul>
        </div>
        <span class="hover"><img src="images/adminCreateBot.png" /></span>
        <form action="admin.php" method="post" name="adminForm" id="adminForm">
          <input type="hidden" name="config" value="verifyBot" />
          <label for="token">Give the received token:</label>
          <input id="token" name="token" type="text" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11" /><br />
<?php if (date_default_timezone_get() === "UTC") { ?>
          <label for="timezone">Set your timezone:</label>
          <select id="timezone" name="timezone">
            <option value="0">Select timezone to set as default</option>
<?php   foreach(timezonelist() as $v) { ?>
              <option value="<?php print $v["zone"] ?>"><?php print $v["diff_from_GMT"] . " - " . $v["zone"] ?></option>
<?php   } ?>
          </select><br />
<?php } ?>
          <button type="submit" id="Send">Verify your Bot >></button>
        </form>
<?php } else { ?>
        Send a message to your Bot:<br  />
        <ul>
          <li>Your Bot-name <code><?php echo $settings["bot_name"]; ?></code></li>
          <li>Add <a href="https://telegram.me/<?php echo $settings["username"]; ?>" title="Start a message with your bot" target="_blank"><?php echo $settings["username"]; ?></a> to a group or start a conversation with it</li>
          <li>Send the follow message:<br /><code>I'm the admin of this bot token <?php echo $settings["token"]; ?></code></li>
          <li>Wait a bit and try to verify you as admin</li>
<?php echo (isset($answer)?"          <li><strong>".$answer["message"]."</strong></li>\n":""); ?>
        </ul>
        <form action="admin.php" method="post" name="adminForm" id="adminForm">
          <input type="hidden" name="config" value="verifyAdmin" />
          <button type="submit" id="Send">Verify you as admin</button>
        </form>
<?php } ?>    
      </div>
    </div>
  </body>
</html>