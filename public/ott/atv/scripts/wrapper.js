/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

// Globals
var gLogOn = false;
var gToiPluginVersion = 0;

// Call stack code
function showCallStack()
{
  var f = showCallStack,result="Call stack:\n\n";

  while ((f = f.caller) !== null) {
    var match = f.toString().match(/^function (\w+)\(/);
    if (!match) {
      match = f.toString().match(/(\w)+\s*[=:]\s*function/);
    }
    if (match !== null) {
      var fname = match[1];
    }
    else {
      var fname = "Anonymous";
    }
    result += "F:" + fname + "\n";
    result += "A:" + parseArguments(f.arguments) + "\n";
    result += "\n";
  }
  alert(result);
}

function parseArguments(a)
{
  var result=[];

  for (var i = 0; i < a.length; i++) {
    if ('string' == typeof a[i]) {
      result.push("\"" + a[i] + "\"");
    }
    else {
      result.push(a[i]);
    }
  }
  return "(" + result.join(", ") + ")";
}

function alertError(e)
{
  DumpLog("error! " + e +
          "\nline: " + (e.lineNumber?e.lineNumber:e.line) +
          "\nfile: " + (e.sourceURL?e.sourceURL:e.file));
}

/**
 * Turn log dumps on for widgets
 */
function LogOn()
{
  gLogOn = true;
}

/**
 *  Turn log dumps off for widgets
 */
function LogOff()
{
  gLogOn = false;
}

/**
 *  Dump message to log. If isRequireLogOn is set, then logging needs
 *  to be turned on by calling LogOn() before calling this function to
 *  get the message logged.
 */
var wrapperCurrentApp = null;
function DumpLog(message, isRequireLogOn)
{
  try {
    // Get app's containing folder name
    if (!wrapperCurrentApp) {
      var url = window.location.pathname.split("/");
      wrapperCurrentApp = url[url.length - 2];
    }
  }
  catch (e) {
    wrapperCurrentApp = "unknown";
  }

  if (isRequireLogOn && !gLogOn) {
    // Don't log
  }
  else if (navigator.userAgent.match("Ekioh v")) {
    alert(wrapperCurrentApp + " - " + message);
  }
  else {
    if (typeof(dump) !== 'undefined') {
      dump(wrapperCurrentApp + " - " + message);
    }
    else{
      alert(wrapperCurrentApp + " - " + message);
    }
  }
}

function addToiPlugin(mime, id)
{
	var embed = document.createElement("embed");
	if (id) 
		embed.id = id;
    embed.type = mime;
    embed.setAttribute("hidden", "true");
    document.body.appendChild(embed);
    document.body.offsetWidth;
}

/**
 * Add the TOI extensions to the webkit page.
 */
function EmbedToi()
{
  if (navigator.userAgent.match("WebKit")) {
    // Add the npruntime TOI/JS plugin
    var html = document.createElement("embed");
    html.id = "embededToi";
    html.type = "application/x-motorola-toi";
    html.setAttribute("hidden", "true");
    document.body.appendChild(html);
    document.body.offsetWidth;
    gToiPluginVersion = embededToi.version;

    // Add the npruntime TEI/JS plugin
    html = document.createElement("embed");
    html.id = "embed2";
    html.type = "application/x-motorola-viaccess";
    html.setAttribute("hidden", "true");
    document.body.appendChild(html);

    // Add the channel map TEI/JS plugin
    html = document.createElement("embed");
    html.id = "channelmapplugin";
    html.type = "application/x-motorola-channelmap";
    html.setAttribute("hidden", "true");
    document.body.appendChild(html);

    // This following line is needed to end up on the right
    // side of a race condition.
    var ignoreMe = document.body.offsetWidth;
  }
}

/**
 * Add the TOI extensions to the svg page.
 */
function EmbedToiInSvg()
{
  var foreignObj = document.createElement("foreignObject");
  foreignObj.setAttributeNS(null, "id", "toiJs2Plugin");
  foreignObj.setAttributeNS(null, "requiredExtensions",
                            "application/x-motorola-toi");
  foreignObj.setAttributeNS(null, "hidden", "true");
  document.documentElement.appendChild(foreignObj);

  // Add the channel map TEI/JS plugin
  var foreignObj = document.createElement("foreignObject");
  foreignObj.setAttributeNS(null, "id", "channelmapplugin");
  foreignObj.setAttributeNS(null, "requiredExtensions",
                            "application/x-motorola-channelmap");
  foreignObj.setAttributeNS(null, "hidden", "true");
  document.documentElement.appendChild(foreignObj);
}

/**
 * Identify type of browser
 */
function IdentifyBrowser()
{
  if (navigator.userAgent.match("midori")) {
    EmbedToiInSvg();
    return "svg";
  }
  else if (navigator.userAgent.match("WebKit")) {
    EmbedToi();
    return "webkit";
  }
  else if (navigator.userAgent.match("Ekioh v")
           || navigator.userAgent.match("Opera")) {
    EmbedToiInSvg();
    return "svg";
  }
  return "webkit";
}

NyxKeyCodeTranslation = KeyCodePatch;

function KeyCodePatch(key, ctrl)
{
  if (key == KEY_LEFT && ctrl || key == KEY_SCROLLLEFT) {
    key = KEY_REWIND;
  }
  else if (key == KEY_RIGHT && ctrl || key == KEY_SCROLLRIGHT) {
    key = KEY_FAST_FORWARD;
  }
  else if (key == KEY_UP && ctrl || key == KEY_SCROLLUP) {
    key = KEY_CHANNEL_UP;
  }
  else if (key == KEY_DOWN && ctrl || key == KEY_SCROLLDOWN) {
    key = KEY_CHANNEL_DOWN;
  }
  else if (key == "Text") {
    key = KEY_TEXT;
  }
  return null;//key;
}

/*
 * function InputObject()
 * Input handler
 *
 * Instances of this object must be named InputObj
 */
function InputObject()
{
  this.listeners = new Array();

  this.remoteInputHandler = function (event)
  {
    var ret = false;
    var i = 0;
    var listeners = this.listeners.slice(0);//clone the array;
    var len = listeners.length;//snapshot to avoid defect on AddEventListener during key's travel;
    while (i < len && !ret) {
      ret = listeners[i++](event);
    }
    if (ret) {
      DumpLog("key event.consume", true);
      event.stopPropagation();
    }
    return ret;
  };

  this.AddEventListener = function (event, callback)
  {
    var self = this;
    if (!this.listeners.length) {
      // Add only if first callback registration
      document.addEventListener(event, function(event){
	      self.remoteInputHandler(event);
		}, false);
    }
    this.listeners.unshift(callback);
  };

  this.RemoveEventListener = function (event, callback)
  {
	var self = this;
    for (i = 0; i < this.listeners.length; i++) {
      if (this.listeners[i] == callback) {
        this.listeners.splice(i, 1);
      }
    }
    if (!this.listeners.length) {
      // Remove only if no listeners
      document.removeEventListener(event, function(event){
	      self.remoteInputHandler(event);
		}, true);
    }
  };
  this.EVENT_KEY_PRESS = "keydown"; // Events
}

/**
 * The global key event handler for navigation inside and between widgets.
 * All key events pass trough this function before they are handled by the
 * correct widget.
 * This function is normally called by the InputObject
 *
 * @param {event} The event trigged by the remote.
 * @return True if the event is handled and should
 * be consumed by the InputObject, else false.
 */
function appWidgets_Keypress(event)
{
  try {
    // Return without action
    if (!gNaviOn || !gActiveElement) {
      DumpLog("return key event from widgets.js", true);
      return false;
    }
    var keyCode = KeyCodePatch(event.keyIdentifier, event.ctrlKey);
	var key;
	
	//if (keyCode == null)
		key = event.which;
	//else
		//key = keyCode;
    // Give input to active widget
    if (gActiveElement.onKeyPressed && gActiveElement.onKeyPressed(event)) { 
      DumpLog("Give key event to active widget in widgets.js", true);
      return true;
    }

    // Navigate
    switch (key) {
    case KEY_LEFT:
      setActiveWidget(gActiveElement.leftElement);
      return true;
    case KEY_UP:
      setActiveWidget(gActiveElement.upElement);
      return true;
    case KEY_RIGHT:
      setActiveWidget(gActiveElement.rightElement);
      return true;
    case KEY_DOWN:
      setActiveWidget(gActiveElement.downElement);
      return true;
    }

    DumpLog("return key event without action from appWidgets_Keypress()",
            true);
    return false;
  }

  catch (e) {
    alert(e);
    return false;
  }
}

/**
 * Video plane widget.
 * @param {int} X position in pixles.
 * @param {int} Y position in pixles.
 * @param {int} width in pixles.
 * @param {int} height in pixles.
 * @param {posZ} z-value.
 */
function VideoPlane(posX, posY, width, height, posZ, parentObject, index)
{
  // This construction to create the videoplane element is due to videoplane's
  // index attribute being constant. It has to be initialized at creation, here
  // by using innerHTML. Should ideally be replaced with something like
  // document.createElement("videoplane index=<index>");
  // and avoid creating the div element.
  if (index != 1) {
    var mainVideoPlaneDiv = document.createElement("div");
    if (navigator.userAgent.match("534")
        || navigator.userAgent.match("Opera")) {
      mainVideoPlaneDiv.innerHTML =
        "<video id='mainvideoplane' src='toi://'></video>";
    }
    else {
      mainVideoPlaneDiv.innerHTML =
        "<videoplane id='mainvideoplane'></videoplane>";
    }
    document.body.appendChild(mainVideoPlaneDiv);
    this.html = document.getElementById("mainvideoplane");
  }
  else {
    if (!navigator.userAgent.match("Opera")) {
      var pipVideoPlaneDiv = document.createElement("div");

      if (navigator.userAgent.match("534")) {
        pipVideoPlaneDiv.innerHTML =
          "<video id='pipvideoplane' " +
          "src='toi://' pip='true'></video>";
      }
      else {
        pipVideoPlaneDiv.innerHTML =
          "<videoplane id='pipvideoplane' index=1></videoplane>";
      }
      document.body.appendChild(pipVideoPlaneDiv);
      this.html = document.getElementById("pipvideoplane");
    }
  }

  this.html.style.left = posX;
  this.html.style.top = posY;
  this.html.style.zIndex = posZ;
  this.html.style.position = "absolute";
  this.html.style.overflow = "hidden";
  this.html.style.visibility = "visible";
  this.html.style.display = "";
  this.html.subtitles = false;

  if (width != null) {
    this.html.style.width = width;
  }
  if (height != null) {
    this.html.style.height = height;
  }

  // It's not allowed if parentObject == document.body in HMC3000's webkit.
  if (parentObject == document.body) {
    var div = document.createElement("div");
    document.body.appendChild(div);
    this.parentObject = div;
    this.parentObject.appendChild(this.html);
  } else {
    this.parentObject = parentObject;
    this.parentObject.appendChild(this.html);
  }

  // Variables for fullscreen toggle
  this.fullscreen = false;
  this.oldX = posX;
  this.oldY = posY;
  this.oldWidth = width;
  this.oldHeight = height;
  this.oldZ = posZ;

  this.getPosX = function ()
  {
    return this.html.style.left;
  };

  this.setPosX = function (x)
  {
    this.html.style.left = x;
  };

  this.getPosY = function ()
  {
    return this.html.style.top;
  };

  this.setPosY = function (y)
  {
    this.html.style.top = y;
  };

  this.getWidth = function ()
  {
    return this.html.style.width;
  };

  this.setWidth = function (width)
  {
    this.html.style.width = width;
  };

  this.getHeight = function ()
  {
    return this.html.style.height;
  };

  this.setHeight = function (height)
  {
    this.html.style.height = height;
  };

  this.getPosZ = function ()
  {
    return this.html.style.zIndex;
  };

  this.setPosZ = function (z)
  {
    this.html.style.zIndex = z;
  };

  this.setSubtitles = function (state)
  {
    this.html.subtitles = state;
  };

  this.getSubtitles = function ()
  {
    return this.html.subtitles;
  };

  // Toggle between fullscreen and the previously set size
  this.toggleFullscreen = function ()
  {
    if (this.fullscreen) {
      this.html.style.position = "absolute";
      this.setPosX(this.oldX);
      this.setPosY(this.oldY);
      this.setWidth(this.oldWidth);
      this.setHeight(this.oldHeight);
      this.fullscreen = false;
    }
    else {
      this.oldX = this.getPosX(),
      this.oldY = this.getPosY();
      this.oldWidth = this.getWidth();
      this.oldHeight = this.getHeight();
      this.html.style.position = "fixed";
      this.setPosX(0);
      this.setPosY(0);
      this.setWidth("100%");
      this.setHeight("100%");
      this.fullscreen = true;
    }
  };

  this.isFullscreen = function ()
  {
    return this.fullscreen;
  };

  this.setVisible = function (visible)
  {
    if (visible) {
      this.html.style.visibility = 'visible';
    }
    else {
      this.html.style.visibility = 'hidden';
    }
  };

  this.isVisible = function ()
  {
    if (this.html.style.visibility == 'visible') {
      return true;
    }
    else {
      return false;
    }
  };

  this.setDisplayed = function (visible)
  {
    if (visible) {
      this.html.style.display = '';
    }
    else {
      this.html.style.display = 'none';
    }
  };

  this.isDisplayed = function ()
  {
    if (this.html.style.display != 'none') {
      return true;
    }
    else {
      return false;
    }
  };

  this.append = function ()
  {
    this.parentObject.appendChild(this.html);
  };

  this.remove = function ()
  {
    this.parentObject.removeChild(this.html);
  };

  this.videoWidth = function ()
  {
    return this.html.videoWidth;
  };

  this.videoHeight = function ()
  {
    return this.html.videoHeight;
  };

  this.videoAspectRatio = function ()
  {
    return this.html.videoAspectRatio;
  };

  this.videoActiveArea = function ()
  {
    return this.html.videoActiveArea;
  };

  this.clipX = function ()
  {
    return this.html.clipX;
  };

  this.clipY = function ()
  {
    return this.html.clipY;
  };

  this.clipWidth = function ()
  {
    return this.html.clipWidth;
  };

  this.clipHeight = function ()
  {
    return this.html.clipHeight;
  };

  this.transformX = function ()
  {
    return this.html.transformX;
  };

  this.transformY = function ()
  {
    return this.html.transformY;
  };

  this.transformWidth = function ()
  {
    return this.html.transformWidth;
  };

  this.transformHeight = function ()
  {
    return this.html.transformHeight;
  };

  this.setVideoClip = function (x, y, width, height)
  {
    this.html.setVideoClip(x, y, width, height);
  };

  this.setVideoTransform = function (x, y, width, height)
  {
    this.html.setVideoTransform(x, y, width, height);
  };

  this.onVideoChange = function (func)
  {
    this.html.onVideoChange = func;
  };
}


/**
 * VideoTag widget.
 * @param {int} X position in pixles.
 * @param {int} Y position in pixles.
 * @param {int} width in pixles.
 * @param {int} height in pixles.
 * @param {posZ} z-value.
 * @param {autoplay} video tag attribute.
 * @param {src} video tag attribute.
 */
function VideoTag(posX, posY, width, height, posZ,
                  parentObject, autoplay, src)
{
  var mainVideoTag = document.createElement("video");
  mainVideoTag.width = width;
  mainVideoTag.height = height;
  if (autoplay == 1) {
    mainVideoTag.autoplay = "autoplay";
  }
  mainVideoTag.src = src;

  mainVideoTag.style.left = posX;
  mainVideoTag.style.left = posX;
  mainVideoTag.style.top = posY;
  mainVideoTag.style.zIndex = posZ;
  mainVideoTag.style.position = "absolute";

  this.parentObject = parentObject;
  this.parentObject.appendChild(mainVideoTag);

  this.setOutlineStyle = function(outline)
  {
     mainVideoTag.style.outline = outline;
  }

  this.getPosX = function ()
  {
    return mainVideoTag.style.left;
  };

  this.setPosX = function (x)
  {
    mainVideoTag.style.left = x;
  };

  this.getPosY = function ()
  {
    return mainVideoTag.style.top;
  };

  this.setPosY = function (y)
  {
    mainVideoTag.style.top = y;
  };

  this.getWidth = function ()
  {
    return mainVideoTag.width;
  };

  this.setWidth = function (width)
  {
    mainVideoTag.width = width;
  };

  this.getHeight = function ()
  {
    return mainVideoTag.height;
  };

  this.setHeight = function (height)
  {
    mainVideoTag.height = height;
  };

  this.setVisible = function (visible)
  {
    if (visible) {
      mainVideoTag.style.visibility = 'visible';
    }
    else {
      mainVideoTag.style.visibility = 'hidden';
    }
  };

  this.setControls = function(controls)
  {
    if (controls == 1) {
      mainVideoTag.controls = "controls";
    }
  }

  this.getControls = function()
  {
    return mainVideoTag.controls;
  }

  this.getAutoplay = function()
  {
    return mainVideoTag.autoplay;
  }

  this.getDuration = function()
  {
    return mainVideoTag.duration;
  }

  this.getCurrentTime = function()
  {
    return mainVideoTag.currentTime;
  }

  this.setCurrentTime = function(time)
  {
    mainVideoTag.currentTime = time;
  }

  this.getVolume = function()
  {
    return mainVideoTag.volume;
  }

  this.setVolume = function(volume)
  {
    mainVideoTag.volume = volume;
  }

  this.getMute = function()
  {
    return mainVideoTag.muted;
  }

  this.setMute= function()
  {
    mainVideoTag.muted = !mainVideoTag.muted;
  }

  this.play = function ()
  {
    mainVideoTag.play();
  };

  this.pause = function ()
  {
    mainVideoTag.pause();
  };

  this.remove = function ()
  {
    this.parentObject.removeChild(mainVideoTag);
  };

  this.addEventListener = function(type, listener, useCapture)
  {
    mainVideoTag.addEventListener(type, listener, useCapture)
  };

  this.removeEventListener = function(type, listener, useCapture)
  {
    mainVideoTag.removeEventListener(type, listener, useCapture)
  };
}

/**
 * IFrameTag widget.
 * @param {int} X position in pixles.
 * @param {int} Y position in pixles.
 * @param {int} width in pixles.
 * @param {int} height in pixles.
 * @param {posZ} z-value.
 * @param {src} IFrame tag attribute.
 */
function IFrameTag( posX, posY, width, height, posZ,
                  parentObject, src)
{
  var iframeTag = document.createElement("iframe");
  iframeTag.width = width;
  iframeTag.height = height;
  iframeTag.src = src;
  iframeTag.style.left = posX;
  iframeTag.style.top = posY;
  iframeTag.style.zIndex = posZ;
  iframeTag.style.border = 0;
  iframeTag.frameBorder = "0";
  iframeTag.scrolling = "no";
  iframeTag.overflow = "no";
  iframeTag.style.position = "absolute";
  iframeTag.style.backgroundcolor = "transparent";

  this.parentObject = parentObject;
  this.parentObject.appendChild(iframeTag);

  this.setOnLoad = function (onload)
  {
  	iframeTag.onload = onload;
  };

  this.getWidth = function ()
  {
    return iframeTag.width;
  };

  this.setWidth = function (width)
  {
    iframeTag.width = width;
  };

  this.getHeight = function ()
  {
    return iframeTag.height;
  };

  this.setHeight = function (height)
  {
    iframeTag.height = height;
  };

  this.focus = function ()
  {
	iframeTag.focus();
  };
  this.blur = function ()
  {
	iframeTag.blur();
  };
  this.remove = function ()
  {
	this.parentObject.removeChild(iframeTag);
  };
  
  this.setsrc = function (url)
  {
	iframeTag.src = url;
  };

  this.getsrc = function ()
  {
	return iframeTag.src;
  };
  this.setzIndex = function(value){
    iframeTag.style.zIndex = value;
  };
}
