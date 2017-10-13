<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// Ajax page: Will be used to communicate live with the chatuser              //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 15/09/16: Create file                                                      //
// 22/02/17: Fixed some bugs                                                  //
// 20/08/17: addded comments                                                  //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// Rely on Aura.Session to create sessions and use Sessions                   //
// -------------------------------------------------------------------------- //
require_once 'lib/telegram/telegramTools.php';
telegramTools::setTimezone();
// Load session library
require_once 'Aura.Session/autoload.php';
require_once 'lib/telegram/telegramUser.php';
require_once 'lib/telegram/telegramSession.php';
require_once 'lib/telegram/telegramMessenger.php';
if (isset($_REQUEST["action"]) && $_REQUEST["action"] != "") {
  $sess = telegramSession::instance();
  $sess_user = $sess->getUser();
  if (!$sess_user) {
    die();
  } else {
    if (function_exists($_REQUEST["action"])) {
      echo $_REQUEST["action"]($sess, telegramTools::post('data'));
    }
  }
}
### Function: Get POST Data and send it.
function send_message(telegramSession $sess, $data) {
  if (!$data) {
    die("WARNING: no 'data' to send");
  }
  $r = Messenger::handle_message($sess, $data);
  if (isset($r->error)) {
    status_header(500);
  }
  else {
    http_response_code(200);
  }
}

### Function: Check for session and updates
function check_messages(telegramSession $sess, $data){
  $updates = Messenger::getUpdates($sess, $data);
  if (isset($updates['error'])) {
    status_header(500);
  }
  return json_encode($updates);
  die(); 
}

?>