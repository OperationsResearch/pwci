<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// Client Interface: interface that send and receive messages                 //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 15/09/16: Create file                                                      //
// 11/10/17: Fixed some small bugs                                            //
// 20/08/17: addded comments                                                  //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// Rely on the api of Telegram                                                //
// -------------------------------------------------------------------------- //

### Interface: Client
interface IClient{
    public function send($message);
    public function getHistory($offset = null);
}

### Class: telegram client
final class TelegramClient implements IClient {

  ### Function: Public - start a telegram client
  public static function instance() {
    static $inst = null;
    if ($inst === null) {
      $inst = new TelegramClient();
    }
    return $inst;
  }

  ### Function: Private - construct client by getting telegram bot token
  private function __construct() {
    $this->token = telegramTools::get_settings_item("token", "");
    $this->baseUrl = 'https://api.telegram.org/bot' . $this->token;
  }
  
  ### Function: Private - get a response and check if everything is okay
  private function getResults($result) {
    if (!$result) { throw new Exception("Telegram API call failed"); }
    $json = json_decode($result);
    if (!$json) { throw new Exception("Failed to get server response: " . print_r($result, true)); }
    if (!$json->ok) { throw new Exception("{$json->error_code}: $json->description"); }
    return $json;
  }

  ### Function: Public - send message to bot linked to the admin user
  public function send($data) {
    $url = $this->baseUrl."/sendMessage";
    return $this->getResults(telegramTools::postJson($url, $data));
  }

  ### Function: Public - get message that are still visible
  public function getHistory($offset = null) {
    $url = $this->baseUrl."/getUpdates";
    $data = $offset ? ['offset' => $offset] : [];
    return $this->getResults(telegramTools::postJson($url, $data));
  }

  ### Function: Public - get information about the connected bot
  public function getInfoBot() {
    $url = $this->baseUrl."/getMe";
    return $this->getResults(telegramTools::postJson($url));
  }

  ### Function: Public - check for an admin message to set the adminuser
  public function verificateMessage($messages) {
    foreach ($messages as $m) {
      if (isset($m->message)) {
        $output_array = [];
        $match = preg_match("/I'm the admin of this bot token (\[?[^\s]+\[?)/i", $m->message->text, $output_array);
        if ($match) {
          $request_token = $output_array[1];
          if (trim($request_token) == trim($this->token)) {
            $adminuser = new telegramUser();
            if ($m->message->chat->type == "group") {
              $adminuser->setUsername($m->message->chat->title);
              $adminuser->setUserid($m->message->chat->id);
            } else {
              $adminuser->setUsername((isset($m->message->from->username)?$m->message->from->username:$m->message->from->first_name." ".$m->message->from->last_name));
              $adminuser->setUserid($m->message->from->id);
            }
            return $adminuser;
          }
        }
      } 
    }
    return false;
  }
}

class Messenger {
  private static $adminuser = null;

  ### Function: Private - get the adminuser
  private static function getAdmin() {
    if (self::$adminuser) return self::$adminuser;
    $adminuser = telegramTools::get_settings_item('admin');
    if ($adminuser) {
      return self::$adminuser = $adminuser;
    } else { throw new Exception("No admin set"); }
  }
  
  ### Function: Public - set the adminuser for the bot
  public static function verifyAdmin() {
    $client = TelegramClient::instance();
    try {
      $updates = $client->getHistory();
    } catch (Exception $e) { return ["message" => $e->getMessage(), "status" => false]; }
    if ($updates->ok && count($updates->result) > 0) {
      if ($adminuser = $client->verificateMessage($updates->result)) {
        telegramTools::save_settings_item('connected', true);
        telegramTools::save_settings_item('admin', $adminuser);
        self::$adminuser = $adminuser;
      } else { return ["message" => "Admin message not found, retry or copy paste the message and send it again.", "status" => false]; }
    } else { return ["message" => "No new messages found, recheck or send the message again!!!", "status" => false]; }
    return ["message" => "The admin of your bot is ".$adminuser->getUsername(), "status" => true];
  }

  ### Function: Public - set info about the bot
  public static function verifyBot() {
    $client = TelegramClient::instance();
    try {
      $botinfo = $client->getInfoBot();
    } catch (Exception $e) { return ["message" => $e->getMessage(), "status" => false]; }
    if ($botinfo->ok && count($botinfo->result) > 0) {
      telegramTools::save_settings_item('bot_name', $botinfo->result->first_name);
      telegramTools::save_settings_item('username', $botinfo->result->username);
    } else { return ["message" => "failed to get bot info", "status" => false]; }
    return telegramTools::load_settings();
  }

  ### Function: Public - prepare to send message to bot
  public static function handle_message(telegramSession $sess, $message) {
    $client = TelegramClient::instance();
    $from = $sess->getUser()->getUsername();
    $data = array("chat_id" => self::getAdmin()->getUserid(), "text" =>  "*".$from." >* ".$message['text'], "parse_mode" => "Markdown", "src" => $message['src'], "offset" => 0);
    $savedata = array ("src" => $message['src'], "chat_id" => self::getAdmin()->getUserid(), "offset" => 0, "text" => $message['text']);
    $result = null;
    try {
      $result = $client->send($data);
      $sess->setMessages(array_merge($sess->getMessages(), array($savedata)));
    }
    catch (Exception $e) { $result = $e->getMessage(); }
    return $result;
  }

  ### Function: Public - get all messages from bot or from session
  public static function getUpdates(telegramSession $sess, $data = null) {
    if (isset($data["init"])) {
      $results = $sess->getMessages();
      if (empty($results)) { $results = NULL;}
    } else {
      $client = TelegramClient::instance();
      $sess_offset = $sess->getUser()->getTelegramOffset();
      $offset = ($sess_offset?($sess_offset>0?$sess_offset + 1:0):0);
      $updates = $client->getHistory($offset);
      if ($updates->ok && count($updates->result) > 0) {
        // We have updates, let's filter out updates
        // that are not directed at this user
        $username = $sess->getUser()->getUsername() . ':';
        $results = array_filter($updates->result, function ($mes) use ($username) {
          if (stripos($mes->message->text, $username) === 0) {
            // We got a match
            $mes->src = 'admin';
            $mes->chat_id = $mes->message->chat->id;
            $mes->offset = 0;
            $mes->text = trim(str_ireplace($username, '', $mes->message->text));
            unset($mes->message);
            return $mes;
          }
        });
        if (!empty($results)) {
          $offset = max(array_map(function($r) { return $r->update_id; }, $results));
        }
        $sess->setTelegramOffset($offset);
        $sess->setMessages(array_merge($sess->getMessages(), $results));
      }
    }
    return $results;
  }
}