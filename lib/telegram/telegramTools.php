<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// Tools Interface: class that holds tools to transform data                  //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 15/09/16: Create file                                                      //
// 22/02/17: add some more functionality                                      //
// 11/10/17: Fixed some small bugs                                            //
// 20/08/17: addded comments                                                  //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// -------------------------------------------------------------------------- //

### Class: telegram tools system
class telegramTools {

  ### Function: Public - check if config exist
  public static function config_exist() {
    if (!empty(glob(realpath(dirname(__FILE__)."/..")."/*.json"))) {
      if (self::get_settings_item("connected")) {
        return "connected";
      } else { return true; }
    } else { return false; }
  }
  ### Function: Public - get the values of the POST
  public static function post($arg) {
    return isset($_POST[$arg]) ? $_POST[$arg] : false;
  }

  ### Function: Public - send json to url and return result
  public static function postJson($url, $payload = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
  
  ### Function: Public - set timezone
  public static function setTimezone() {
    $timezone = self::get_settings_item("timezone");
    if($timezone) {
      date_default_timezone_set($timezone);
    }
  }

  ### Function: Private - get the settings from file
  public static function load_settings() {
    return unserialize(file_get_contents(reset(glob(realpath(dirname(__FILE__)."/..")."/*.json"))));
  }

  ### Function: Public - save settings
  public static function save_settings( $settings, $file="config" ) {
    file_put_contents(realpath(dirname(__FILE__)."/..")."/".$file.".json", serialize($settings));
  }

  ### Function: Public - delete all settings
  public static function reset_settings() {
    array_map('unlink', glob("lib/*.json"));
  }

  ### Function: Public - get one item
  public static function get_settings_item( $key, $default_value = false ) {
    $settings = self::load_settings();
    return isset($settings[$key]) ? $settings[$key] : $default_value;
  }

  ### Function: Public - save one item
  public static function save_settings_item( $key, $value ) {
    $settings = self::load_settings();
    $settings[$key] = $value;
    if (isset($settings["token"]) && $settings["token"] != "") {
      self::save_settings($settings, $settings["token"]);
    } else {
      self::save_settings($settings, $settings["token"]);
    }
  }
}