<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// User Interface: class that create chat user and gets user id               //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 15/09/16: Create file                                                      //
// 11/10/17: Fixed some small bugs                                            //
// 20/08/17: addded comments                                                  //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// -------------------------------------------------------------------------- //

### CLass: user
class telegramUser {
  private $username;
  private $updated;
  private $user_id;
  private $telegram_offset = false;

  ### Function: Public - get the offset for a user
  public function getTelegramOffset() {
    return $this->telegram_offset;
  }

  ### Function: Public - set the offset for a user
  public function setTelegramOffset($telegram_offset) {
    $this->telegram_offset = $telegram_offset;
  }

  ### Function: Public - get an update
  public function getUpdated() {
    return $this->updated;
  }

  ### Function: Public - set an update
  public function setUpdated($updated) {
    $this->updated = $updated;
  }

  ### Function: Public - get the username
  public function getUsername() {
    return $this->username;
  }

  ### Function: Public - set the username
  public function setUsername($username) {
    $this->username = $username;
  }

  ### Function: get
  public function getUserid() {
    return $this->user_id;
  }

  ### Function: 
  public function setUserid($user_id) {
    $this->user_id = $user_id;
  }
}