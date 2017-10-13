# pwci 📢
a Php Web Chat Integrator

*Uses [Telegram](https://telegram.org/) for communication. You find more information about the api [here](https://core.telegram.org/api)*

# Table of contents

- [Installation](#installation)
- [Answer Chats](#answer-chat)
- [Recommended configurations](#recommended-configurations)
- [Custom configurations](#custom-configurations)
- [Reconfigure](#uninstallation)
- [Contributing](#contributing)
- [License](#license)

# Installation

[(Back to top)](#table-of-contents)

1. Make sure php is installed (preferably, version > 5.1)
2. Create an folder, ex. `/yourproject/pwci`
3. Download the [code](https://github.com/OperationsResearch/pwci) and place it in the folder you created
      *You can do a git clone or download the zip and extract it in the folder*

4. Open a webbrowser and go to the folder (ex. http://YOURHOST/pwci)
5. You follow the instructions on the page. 
      *(if you are not transfered to the admin page go to ex. http://YOURHOUST/pwci/admin.php)*
      *You will need your token-api of telegram to setup the chat*

6. After finishing the first time use page you are ready to use the chat
7. You can include now in your project:
    ```require_once 'pwci/chat.php';
    $chat = new pwci();
    echo $chat->set_scripts();
    echo $chat->set_chat();
    ```
5. Have a look at [Recommended configurations](#recommended-configurations).

# Answer chat

[(Back to top)](#table-of-contents)

When somebody start a chat, you will receive it in your Telegram client. It will generate something like this:

```
  YOURBOTNAME, [20.07.17 18:03]
  deadd > Hello
```

Because multiple persons can chat with you at once you have to define the user name so that it can deliver to the correct chat user:

```
  deadd: Hi how can I help
  
  !!!It's really important that you include the username: to reply otherwise the chatuser will not receive it!!!
```

# Recommended configurations

[(Back to top)](#table-of-contents)

1. Creating a chat system is just calling the class new pwci(), but defining some options makes it more personal:
    ```
    $options = array(
      "spaceshead" => "SPACES for you header-code",
      "spacesbody" => "SPACES for the body-code",
      "location" => $deeperlocation."/pwci/",
      "chatheight" => "50%",
      "initmessage" => "Welcome messages that appears after some seconds",
      "inputmessage" => "input field message: ex. Type a message",
      "chattitle" => "Chat title",
      "faceleft" => "location/of/an/image/as/your/responseface.png"
    );
    $chat = new pwci($options);
    ```
2. If you don't want to include the chat on every page except if a chatsession has been started, then you can use this function:

    ```
      $chat->is_chatsession (); //It will return an answer if it exist or not. so you can make it visible on every page of only on the page you want until somebody start a chat
    ```

# Custom configurations

[(Back to top)](#table-of-contents)

These are all possible configurations:

```
  spaceshead       <- Put the amount of spaces so that it align with html code in the header
  spacesbody       <- Put the amount of spaces so that it align with html code in the body
  location         <- define where the pwci folder is
  debug            <- you can enable logging, default false
  initmessage      <- default message that appears after some seconds to invite the user to chat
  chattitle        <- The title of the chat window, default "Php Web Chat"
  inputmessage     <- the message where the chat person can type something, default "Type a message..."
  faceleft         <- picture location that will be used for the response of you, default "images/people.png"
  faceright        <- picture location of the chat person, default "images/people.png"
  chatwidth        <- width of the chat window, px/%/em can be used, default "24.3em"
  chatlocation     <- location of the chat window, left/center/right are possible, default "right"
  chatheight       <- height of the chat window, px/%/em can be used, default "27em"
  chatfull         <- chat will be over the complete window, this overrules height and width, default false
  jquery           <- if in your html is already an include of jquery then set this on true and the build in jquery won't be used. default false
  angular          <- if in your html is already an include of angular then set this to true and the build in angular won't be used. default false
  angularanimation <- if in your html is already an include of angularanimation then set this to true and the build in angularanimation won't be used. default false
  angularsantize   <- if in your html is already an include of angularsantize then set this to true and the build in angularsantize won't be used. default false
  lodash           <- if in your html is already an include of lodash then set this to true and the build in lodash won't be used. default false
  ngemoticons      <- if in your html is already an include of ngemoticons then set this to true and the build in ngemoticons won't be used. default false
```
  
all these options can be set when you initialize, but it can also be done after with the function `$chat->setConfig(OPTION, VALUE)`:

```
$chat->setConfig("jquery", true); //this will disable jquery and use the already implemented version.
```
    
If you want do more with the options you can also request the options `$chat->getConfig(OPTION)`:

```
$chat->getConfig("jquery"); //this will give the value of the option jquery.
```

# Reconfigure/Uninstallation

[(Back to top)](#table-of-contents)

Want to reconfigure or uninstall the chat system? No issues. Go to the follow url:

```
  http://YOURHOUST/pwci/admin.php

  you need your token (looks like 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11) and hit the "Reset config".
  REMARK: this can't be undone!
```

# Contributing

[(Back to top)](#table-of-contents)

Your contributions are always welcome!

Please check the issues or create one if it doesn't exist yet.

Do you have a remark, let me know. If we share the same vision and time is on my side, it will be implemented.

*Examples will be included in the future but try it first yourself, who knows you can get it working yourself*

# License

[(Back to top)](#table-of-contents)


The MIT License (MIT) 2017 - [OR](https://github.com/OperationsResearch/). Please have a look at the [LICENSE](LICENSE) for more details.

