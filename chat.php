<?php
// -------------------------------------------------------------------------- //
// PWCI: a Php Web Chat Integrator                                            //
// -------------------------------------------------------------------------- //
// BY:  * Reygaert Omar                                                       //
// Infolink: https://github.com/OperationsResearch/pwci                       //
// -------------------------------------------------------------------------- //
// PAGE DESCRIPTION:                                                          //
// Chat page: php script to include in other webprojects                      //
// -------------------------------------------------------------------------- //
// UPDATES:                                                                   //
// 15/09/16: Create file                                                      //
// 11/10/17: Fixed some small bugs                                            //
// 20/08/17: addded comments                                                  //
// -------------------------------------------------------------------------- //
// COMMENTS:                                                                  //
// Rely on Aura.Session to create sessions and use Sessions                   //
// -------------------------------------------------------------------------- //
require_once 'lib/telegram/telegramTools.php';

define('PWCi_VERSION','0.03');
if (!class_exists('pwci')) {
  class pwci {
    private $spaceshead = "";
    private $spacesbody = "";
    private $location = "";
    private $debug = false; 
    private $initmessage = "";
    private $chattitle = "Php Web Chat";
    private $inputmessage = "Type a message...";
    private $session;
    private $faceleft = "images/people.png";
    private $faceright = "images/people.png";
    private $chatwidth = "24.3em";
    private $chatlocation = "right";
    private $chatheight = "27em";
    private $chatfull = false;
    private $jquery = false;
    private $angular = false;
    private $angularanimation = false;
    private $angularsantize = false;
    private $lodash = false;
    private $ngemoticons = false;

    ### Function: constructor for the chat, sets location to default or define it if folder has been renamed and create the config
    function __construct(array $config = []) {
      if (!array_key_exists("location", $config)) {
        $config["location"] = "pwci/";
//        throw new Exception("Can not find session for user");
      }
      foreach ($config as $key => $value) {
        if (isset($this->{$key})){
          $this->setConfig($key, $value);
        }
      }
      telegramTools::setTimezone();
      require_once 'Aura.Session/autoload.php';
      require_once 'lib/telegram/telegramUser.php';
      require_once 'lib/telegram/telegramSession.php';
      $sess = telegramSession::instance();
      $sess_user = $sess->getUser();
      if (!$sess_user) {
        $user = new telegramUser();
        $user->setUsername(preg_replace("/[^a-zA-Z]+/", "", uniqid()));
        $sess->setUser($user);
        $sess_user = $sess->getUser();
      }
      if (!$sess_user) {
        throw new Exception("Can not find session for user");
      }
      if ($sess->getMessages()) { $this->session = true; } else { $this->session = false;}
    }
    
    ### Function: gets a config
    public function getSetConfig($variable) {
      return $this->{$variable};
    }
    ### Function: set config
    public function setConfig($variable, $value) {
      if ($variable == "location") {
        $connected = telegramTools::config_exist();
        if ($connected !== "connected") { header("Location: ".$value."admin.php"); exit(); }
        if (substr($this->faceright, -16) == "images/people.png" || $this->location != $value) {
          $this->faceright = $value."images/people.png";
        }
        if (substr($this->faceleft, -16) == "images/people.png" || $this->location != $value) {
          $this->faceleft = $value."images/people.png";
        }
      }
      $this->{$variable} = $value;
    }
    ### Function: check if a chat session has been started
    public function is_chatsession () {
      return $this->getSetConfig("session");
    }

    ### Function: create the script- and css-html code to implement
    function set_scripts() {
      $script = "";
      $scripts .= (!$this->jquery?$this->spaceshead."<script src=\"//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js\"></script>\n":"");
      $scripts .= (!$this->angular?$this->spaceshead."<script type=\"text/javascript\" src=\"".$this->location."lib/angular/angular.min.js\"></script>\n":"");
      //$scripts .= (!$this->angular?$this->spaceshead."<script type=\"text/javascript\" src=\"".$this->location."lib/angular/angular-route.min.js\"></script>\n":"");
      $scripts .= (!$this->angularanimation?$this->spaceshead."<script type=\"text/javascript\" src=\"".$this->location."lib/angular/angular-animate.min.js\"></script>\n":"");
      $scripts .= (!$this->angularsantize?$this->spaceshead."<script type=\"text/javascript\" src=\"".$this->location."lib/angular/angular-sanitize.min.js\"></script>\n":"");
      $scripts .= (!$this->lodash?$this->spaceshead."<script type=\"text/javascript\" src=\"".$this->location."lib/lodash/lodash.core.min.js\"></script>\n":"");
      $scripts .= (!$this->ngemoticons?$this->spaceshead."<script type=\"text/javascript\" src=\"".$this->location."lib/ng-emoticons.js\"></script>\n":"");
      $scripts .= $this->spaceshead."<script type=\"text/javascript\">\n";
      $scripts .= $this->spaceshead."  /* <![CDATA[ */\n";
      $scripts .= $this->spaceshead."  var ajax_object = {\"ajax_url\":\"".$this->location."ajax.php\"".($this->initmessage != ""?",\"startmessage\":\"".$this->initmessage."\"":"").($this->debug?",\"debug\":true":"")."};\n";
      $scripts .= $this->spaceshead."  /* ]]> */\n";
      $scripts .= $this->spaceshead."</script>\n";
      $scripts .= $this->spaceshead."<script type=\"text/javascript\" src='".$this->location."scripts.js'></script>\n";
      $scripts .= (!$this->ngemoticons?$this->spaceshead."<link rel=\"stylesheet\" href=\"".$this->location."lib/ng-emoticons.css\" type=\"text/css\" media=\"all\" />\n":"");
      //$scripts .= $this->spaceshead."<link rel=\"stylesheet\" href=\"".$this->location."styles.css\" type=\"text/css\" media=\"all\" />\n";
      $scripts .= $this->spaceshead."<style>#chattingBox p:before,.content:before{content:\"\"}#chattingBox{font-size:.84em;border:1px solid #000;border-radius:.5em .5em 0 0;z-index:10;background:#aab8c2;bottom:0;height:".($this->chatfull?$this->chatheight:"4em").";width:".$this->chatwidth.";position:fixed}.chatleft{left:2em}.chatright{right:2em}.chatmiddle{position:fixed;margin:0 auto;left:0;right:0}#chattingBox #profile{background:#000;color:#fff;cursor:pointer;padding:1em 2em;border-radius:.4em .4em 0 0}#chattingBox p:before{background:#abff00;box-shadow:rgba(0,0,0,.2) 0 -.1em .3em .1em,inset #1a8a34 0 -.1em .3em,#89ff00 0 .15em .7em;border-radius:50%;display:inline-block;height:.8em;width:.8em;margin:0 .6em 0 0}#chattingBox form{padding:1.5em 0}.chat-message-counter{background:#e62727;border:1px solid #fff;border-radius:50%;font-size:.84em;font-weight:700;height:2.8em;width:2.8em;line-height:2.8em;margin:-1.5em 0 0 -1.5em;position:absolute;text-align:center;top:0".($this->chatfull?";display:none":"")."}.chatright .chat-message-counter{left:0}.chatmiddle .chat-message-counter{left:.8em;margin:.8em 0}.chatleft .chat-message-counter{right:-1.5em}.content:before,div.messagearea.right .content:before{border-top:.9em solid transparent;border-bottom:0 solid transparent}#messageBox{height:83%;height:-webkit-calc(100% - 10.4em);height:-moz-calc(100% - 10.4em);height:calc(100% - 10.4em); width:75%;width:-webkit-calc(100% - 3em);width:-moz-calc(100% - 3em);width:calc(100% - 3em);position: absolute;;margin-bottom:5.1em;padding:.6em 1.5em;overflow-y:scroll;background-color:#fff}#messageBox div.messagearea{padding:0 0 .55em;clear:both;margin-bottom:1em;margin-right:1.2em;overflow:hidden;position:relative}#messageBox div.messagearea.right{margin-right:-.4em;margin-left:1.55em}#messageBox div.messagearea .img{float:left;border-radius:50%;height:2.5em;width:2.5em;background:url(".$this->faceleft.") no-repeat #fff;background-size:2.5em;overflow:hidden;position:absolute;bottom:0}#messageBox div.messagearea .img span{font-size:0}#messageBox div.messagearea.right .img{float:right;background:url(".$this->faceright.") no-repeat #fff;background-size:2.5em;right:0}.messagearea .content{background:#effdde;color:#8495a3;margin-left:3.1em;font-size:1.08em;font-weight:600;padding:.8em;border-radius:.5em .5em .5em 0;position:relative;float:left;box-shadow:0 .12em 0 #aed5bb}#messageBox div.messagearea.right .content{float:right;margin-right:3.1em;margin-left:0;border-radius:.5em .5em 0;background:#419fd9;color:#fff;box-shadow:0 .15em 0 #2e83b8}.content:before{position:absolute;display:block;left:0;border-right:.7em solid #effdde;bottom:0;margin-left:-.6em;box-shadow:0 .12em 0 #aed5bb}div.messagearea.right .content:before{left:99%;margin-right:-.6em;margin-left:0;border-right:0 solid transparent;border-left:.7em solid #419fd9;box-shadow:0 .15em 0 #2e83b8}#chatBox{height:2.1em;border-top:1px solid #e7ebee;position:absolute;bottom:0;right:0;width:100%;background:#fff;".(!$this->chatfull?";display:none":"")."}#chatBox input{background:#fff;border:none;padding:0;font-size:1.2em;font-weight:400;color:#aab8c2;width:75%;width:-webkit-calc(100% - 5em);width:-moz-calc(100% - 5em);width:calc(100% - 5em);margin:0 1em}#chatBox button:focus,#chatBox input:focus{outline:0}#chatBox button{background:url(".$this->location."images/send.png) 0 -41px no-repeat #fff;width:2.5em;height:2.5em;position:absolute;right:1.1em;bottom:0;border:none}#chatBox button:hover{cursor:pointer;background-position:0 0}.debugbuttons{position:absolute;height:1em;top:0;right:0}.animatefade.ng-enter{transition:.1s ease all;opacity:0}.animatefade.ng-enter.ng-enter-active{opacity:1}</style>";
      return $scripts;
    }

    ### Function: create the body html code
    function set_chat() {
      $html = "$this->spacesbody<div ng-app=\"chatApp\" id=\"chattingBox\" class=\"chat".$this->chatlocation."\">
$this->spacesbody  <div ng-controller=\"chatCtrl\" id=\"chatview\">
$this->spacesbody    <div id=\"profile\">
$this->spacesbody      <p>".$this->chattitle."</p>
$this->spacesbody      <span class=\"chat-message-counter\">{{ total }}</span>
$this->spacesbody      <div ng-if=\"DEBUG\" class=\"debugbuttons\" style=\"padding: 20px\">
$this->spacesbody        <button ng-click=\"getUpdates()\">Get Updates (debug)</button>
$this->spacesbody      </div>
$this->spacesbody    </div>
$this->spacesbody    <div id=\"messageBox\" class=\"p1\" scroll2-Bottom=\"messages\">
$this->spacesbody      <div id=\"messageList\">
$this->spacesbody        <div ng-repeat=\"message in messages\" class=\"messagearea animatefade\" ng-class=\"{ right: message.src == 'client', left: message.src == 'admin' } \">
$this->spacesbody          <div class=\"img\"><span>profilePicture</span></div>
$this->spacesbody          <div class=\"content\" ng-bind-html=\"message.text | emoticons:{linkTarget:'_blank'}\">
$this->spacesbody          </div>
$this->spacesbody        </div>
$this->spacesbody      </div>
$this->spacesbody    </div> <!-- end chat-history -->
$this->spacesbody    <form id=\"chatBox\" class=\"p1\" name=\"chatBox\" ng-if=\"ERROR == false\" ng-submit=\"submitMessage(form.chatMessage, 'client')\">
$this->spacesbody      <input id=\"chatMessage\" type=\"text\" ng-model=\"form.chatMessage\" placeholder=\"".$this->inputmessage."\" autocomplete=\"off\" />
$this->spacesbody      <button type=\"submit\" id=\"pwcisend\"></button>
$this->spacesbody    </form>
$this->spacesbody  </div> <!-- end chat -->
$this->spacesbody</div> <!-- end live-chat -->
$this->spacesbody<script>
$this->spacesbody  (function() {
$this->spacesbody    var i = ".($this->chatfull?"0":"1").";
$this->spacesbody    $('#chattingBox #profile').on('click', function() {
$this->spacesbody      $('#chatBox').slideToggle(100, 'swing');
$this->spacesbody      $('.chat-message-counter').fadeToggle(300, 'swing');
$this->spacesbody      $('#chattingBox').animate({height:(++i % 2) ? '4em' : '".$this->chatheight."'},300);
$this->spacesbody    });
$this->spacesbody  }) ();
$this->spacesbody</script>\n";
      return $html;
    }
  }
}

?>
