<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// Session Library: Class that manage sessions                                //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 15/09/16: Create file                                                      //
// 11/10/17: Fixed some small bugs                                            //
// 20/08/17: addded comments                                                  //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// Rely on Aura.Session to create sessions and use Sessions                   //
// -------------------------------------------------------------------------- //

### Class: Session managment system
final class telegramSession {

  ### Function: Public - start a singleton instance
  public static function instance() {
    static $inst = null;
    if ($inst === null) {
      $inst = new telegramSession();
    }
    return $inst;
  }

  private $segment;

  ### Function: Private - construct the session
  private function __construct(){
    $session_factory = new \Aura\Session\SessionFactory;
    $session = $session_factory->newInstance($_COOKIE);
    //keep session until browser close
    $session->setCookieParams(array('lifetime' => '0'));
    $this->segment = $session->getSegment('telegram\Session\telegramSession');
  }

  ### Function: Private - get data from the session
  private function get($key, $default = null) {
    return $this->segment->get($key, $default);
  }

  ### Function: Private - set data to the session
  private function set($key, $value) {
    $this->segment->set($key, $value);
  }

  ### Function: Public - save object user in the session
  public function setUser(telegramUser $user) {
    $timestamp = (new DateTime())->getTimestamp();
    $user->setUpdated($timestamp);
    $this->set('user', $user);
  }

  ### Function: Public - get object user from session
  public function getUser() {
    return $this->get('user');
  }

  ### Function: Public - update the offeset in the session
  public function setTelegramOffset($offset) {
    if ($user = $this->getUser()) {
      $user->setTelegramOffset($offset);
      $this->set('user', $user);
      return $user;
    }
    return false;
  }

  ### Function: Public - get the offset from the session
  public function getTelegramOffset() {
    if ($user = $this->getUser()) {
      return $user->getTelegramOffset();
    }
    return false;
  }

  ### Function: Public - save messages in the session
  public function setMessages($data) {
    $this->set('messages', $data);
  }

  ### Function: Public - get all messages from the session
  public function getMessages() {
    return (array)$this->get('messages');
  }
}