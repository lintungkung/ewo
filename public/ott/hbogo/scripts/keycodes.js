/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

/* This is the key code file for TOI/js2.
 */

var KEY_A = "U+0041";
var KEY_B = "U+0042";
var KEY_C = "U+0043";
var KEY_D = "U+0044";
var KEY_E = "U+0045";
var KEY_F = "U+0046";
var KEY_G = "U+0047";
var KEY_H = "U+0048";
var KEY_I = "U+0049";
var KEY_J = "U+004A";
var KEY_K = "U+004B";
var KEY_L = "U+004C";
var KEY_M = "U+004D";
var KEY_N = "U+004E";
var KEY_O = "U+004F";
var KEY_P = "U+0050";
var KEY_Q = "U+0051";
var KEY_R = "U+0052";
var KEY_S = "U+0053";
var KEY_T = "U+0054";
var KEY_U = "U+0055";
var KEY_V = "U+0056";
var KEY_W = "U+0057";
var KEY_X = "U+0058";
var KEY_Y = "U+0059";
var KEY_Z = "U+005A";

// Direction Key
var KEY_BACK = 8;   // "BrowserBack";
var KEY_UP = 38;    // "Up";
var KEY_LEFT = 37;  // "Left";
var KEY_DOWN = 40;  // "Down";
var KEY_RIGHT = 39; // "Right";
var KEY_OK = 13;    // "Accept" | "Return";

// Number Key 0 ~ 9
var KEY_0 = 48; //"U+0030";
var KEY_1 = 49; //"U+0031";
var KEY_2 = 50; //"U+0032";
var KEY_3 = 51; //"U+0033";
var KEY_4 = 52; //"U+0034";
var KEY_5 = 53; //"U+0035";
var KEY_6 = 54; //"U+0036";
var KEY_7 = 55; //"U+0037";
var KEY_8 = 56; //"U+0038";
var KEY_9 = 57; //"U+0039";

// Function Key F1 - F4
var KEY_F1 = 112;
var KEY_F2 = 113;
var KEY_F3 = 114;
var KEY_F4 = 115;

// Page Key
var KEY_PAGEUP = 33; //
var KEY_PAGEDOWN = 34;


// Color key
var KEY_RED = 0xe0000;     // "Red";
var KEY_GREEN = 0xe0001;   // "Green";
var KEY_YELLOW = 0xe0002;  // "Yellow";
var KEY_BLUE = 0xe0003;    // "Blue";

// Channel Key
var KEY_CHANNEL_UP = 0xe0030;
var KEY_CHANNEL_DOWN = 0xe0031;

// Volume Key
var KEY_MUTE = 0xe00f0; // "VolumeMute";
var KEY_VOLUME_UP = 0xe00f3;
var KEY_VOLUME_DOWN = 0xe00f4;

// STB Function Key
var KEY_POWER = 0xe0035;
var KEY_EPG = 0xe0110;
var KEY_LANGUAGE = 0xe0200;
var KEY_INFO = 0xe0034;
var KEY_MENU = 0xe0033;
var KEY_ESCAPE = 27; // BBTV liveTV key

// PVR / Media Player Key
var KEY_MEDIA_PLAY = 0xe0010;
var KEY_MEDIA_PAUSE = 0xe0011;
var KEY_MEDIA_STOP = 0xe0012;
var KEY_MEDIA_REW = 0xe0013;
var KEY_MEDIA_FF = 0xe0014;
var KEY_MEDIA_REC = 0xe0017;
var KEY_MEDIA_PLAYPAUSE = 0xe0018;
var KEY_RECORD_LIST = 0xe0114;


var KEY_SCROLLUP = "ScrollUp";
var KEY_SCROLLDOWN = "ScrollDown";
var KEY_SCROLLLEFT = "ScrollLeft";
var KEY_SCROLLRIGHT = "ScrollRight";

/*******************************
 * NON-CNS Remote Control Keys
 *******************************
 * var KEY_STANDBY = "Standby";
 * var KEY_TV = 36; // "TV";
 * var KEY_WWW = "LaunchWww";
 * var KEY_VOD = "VOD";
 * var KEY_GUIDE = 917507;"Guide";
 * var KEY_MOS = "Mosaic";
 * var KEY_FAV = 917505;
 * var KEY_SERVICES = "Services";
 * var KEY_EXIT = 0; // "Exit";
 * var KEY_LIST = "List";
 * var KEY_CANCEL = "U+0018";
 * var KEY_NEXT_CHAPTER = "MediaNextTrack";
 * var KEY_PREV_CHAPTER = "MediaPreviousTrack";
 * var KEY_ZOOM = "Zoom";
 * var KEY_TEXT = "Teletext";
 *
*/

 /* HMC3021 KreaTV key code mapping
var KEY_0 = 48;//"U+0030";
var KEY_1 = 49;//"U+0031";
var KEY_2 = 50;//"U+0032";
var KEY_3 = 51;//"U+0033";
var KEY_4 = 52;//"U+0034";
var KEY_5 = 53;//"U+0035";
var KEY_6 = 54;//"U+0036";
var KEY_7 = 55;//"U+0037";
var KEY_8 = 56;//"U+0038";
var KEY_9 = 57;//"U+0039";

var KEY_A = "U+0041";
var KEY_B = "U+0042";
var KEY_C = "U+0043";
var KEY_D = "U+0044";
var KEY_E = "U+0045";
var KEY_F = "U+0046";
var KEY_G = "U+0047";
var KEY_H = "U+0048";
var KEY_I = "U+0049";
var KEY_J = "U+004A";
var KEY_K = "U+004B";
var KEY_L = "U+004C";
var KEY_M = "U+004D";
var KEY_N = "U+004E";
var KEY_O = "U+004F";
var KEY_P = "U+0050";
var KEY_Q = "U+0051";
var KEY_R = "U+0052";
var KEY_S = "U+0053";
var KEY_T = "U+0054";
var KEY_U = "U+0055";
var KEY_V = "U+0056";
var KEY_W = "U+0057";
var KEY_X = "U+0058";
var KEY_Y = "U+0059";
var KEY_Z = "U+005A";

var KEY_STANDBY = "Standby";
var KEY_INFO = 917506;//"Info";
var KEY_TV = 36;//"TV";
var KEY_WWW = "LaunchWww";
var KEY_VOD = "VOD";
var KEY_GUIDE = 917507;"Guide";
var KEY_MOS = "Mosaic";

var KEY_RED = 917556;//"Red";
var KEY_GREEN = 917777;//"Green";
var KEY_YELLOW = 917554;//"Yellow";
var KEY_BLUE = 917782;//"Blue";
var KEY_FAV = 917505;

var KEY_SERVICES = "Services";
var KEY_BACK = 8;//"BrowserBack";
var KEY_OK = 13;//"Accept";
var KEY_MENU = 917504;//"Menu";
var KEY_EXIT = 0;//"Exit";
var KEY_LIST = "List";

var KEY_UP = 38;//"Up";
var KEY_DOWN = 40;//"Down";
var KEY_LEFT = 37;//"Left";
var KEY_RIGHT = 39;//"Right";

var KEY_VOLUME_DOWN = "VolumeDown";
var KEY_VOLUME_UP = "VolumeUp";
var KEY_MUTE = 917555;//"VolumeMute";

var KEY_CHANNEL_UP = "ChannelUp";
var KEY_CHANNEL_DOWN = "ChannelDown";

var KEY_PLAY_PAUSE = "MediaPlayPause";
var KEY_REWIND = "MediaRewind";
var KEY_FAST_FORWARD = "MediaForward";
var KEY_STOP = "MediaStop";
var KEY_RECORD = "MediaRecord";
var KEY_CANCEL = "U+0018";
var KEY_NEXT_CHAPTER = "MediaNextTrack";
var KEY_PREV_CHAPTER = "MediaPreviousTrack";
var KEY_ZOOM = "Zoom";

var KEY_SCROLLUP = "ScrollUp";
var KEY_SCROLLDOWN = "ScrollDown";
var KEY_SCROLLLEFT = "ScrollLeft";
var KEY_SCROLLRIGHT = "ScrollRight";

var KEY_TEXT = "Teletext";*/

//
//  KeyCodesObject - provides an easy way to setup functions to be called
//  when a key code is entered.
//
//  addFunction() will add a keycode and function to a key code array.
//
//  processKeyCode() will lookup the given key and call the associated
//  function.
//
//  onKeyDefault() will be called by processKeyCode() if a key code is not
//  found and no 'default' was specified.
//
//  Example usage:
//
//  Create an empty keyArray array:
//
//    var keyArray = new Array();
//
//  Instantiate KeyCodesObject() at top of file:
//
//    keyCodes = new KeyCodesObject()
//
//  One time initialization (like in OnLoad()).  If more than one key can
//  be used for the same function, then an array of keys can be passed in.
//
//    keyCodes.addFunction(keyArray, [KEY_CHANNEL_UP, "U+0026"], channelUp);
//    keyCodes.addFunction(keyArray, "Enter", onOk);
//    keyCodes.addFunction(keyArray, KEY_REWIND, rewind);
//    etc.
//
//  in your onKey() function:
//
//    function onKey(event)
//    {
//      if (!handle_key_events) {
//        return false;
//      }
//      var keyCode = NyxKeyCodeTranslation(event.keyIdentifier,
//                                          event.ctrlKey);
//      var result = keyCodes.processKeyCode(keyCode, keyArray);
//
//      return result;
//    }
//
//  Note:  The functions (like channelUp() specified above) should
//         return true/false as appropriate.
//

function KeyCodesObject()
{
  this.getTypeOf = function (obj)
  {
    return Object.prototype.toString.call(obj).match(/^\[object (.*)\]$/)[1]
  }

  this.onKeyDefault = function (keyCode, debug)
  {
    if (debug) {
      DumpLog("KeyCodesObject.OnKeyDefault: keyCode = " + keyCode);
    }
    return false;
  }

  this.addFunction = function (keyCodeArray, key, func)
  {
    var keyType = this.getTypeOf(key);
    if (keyType == "String") {
      keyCodeArray[key] = { KeyCode: key, Func: func };
    }
    else if (keyType == "Array") {
      for (var k in key) {
        keyCodeArray[key[k]] = { KeyCode: key[k], Func: func };
      }
    }
    else {
      DumpLog("addFunction: invalid key parameter");
    }
  }

  this.processKeyCode = function (key, keyCodeArray, debug)
  {
    var debug = debug || false;
    var result = false;
    if (key in keyCodeArray) {
      if (debug) {
        DumpLog("KeyCodesObject.processKeyCode: key = " + key +
                " was found, call Function");
      }
      result = keyCodeArray[key].Func(key);
    }
    else if ("default" in keyCodeArray) {
      if (debug) {
        DumpLog("KeyCodesObject.processKeyCode: key = " + key +
                " was not found, call user's default");
      }
      result = keyCodeArray["default"].Func(key);
    }
    else {
      result = this.onKeyDefault(key, debug);
    }
    return result;
  }
}
