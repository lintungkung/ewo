/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

// Globals
var gActiveElement = null;
var gNaviOn = true;
var gIsSVGBrowser = typeof(gSVGNS) !== "undefined";
var gBorderColorNormal = "#6D4F47";
var gBorderColorHighLight = "#285bb7";
var gBackgroundColorNormal = "#999";
var gBackgroundColorFocus = "#eee";
var gBackgroundColorHighlight = "#999";
var gOptionListHighlightColor = "#666";
var gFontHighlightColor = "#ffffff";
var gFontNormalColor = "#999999";
/**
 * Turn key navigation on for widget navigation. Key events will be handled by
 * functions in widgets.
 */
function NaviOn()
{
    gNaviOn = true;
}

/**
 * Turn key navigation off for widget navigation. No events will be handled
 * in any widgets.
 */
function NaviOff()
{
    gNaviOn = false;
}

/**
 * Turns a widget active. The widget is highlighted and can receive key
 * events.
 * @param {widget} The widget that will be active.
 */
function setActiveWidget(widget)
{
    if (gActiveElement && widget) {
        gActiveElement.blur();
    }
    if (widget) {
        gActiveElement = widget;
        gActiveElement.highlight();
    }
}

/**
 * Turns a widget inactive.
 */
function unsetActiveWidget()
{
    if (gActiveElement) {
        gActiveElement.blur();
        gActiveElement = null;
    }
}

/**
 * Returns the active widget.
 */
function getActiveWidget()
{
    return gActiveElement;
}

/**
 * Sets widget key navigation relationship left-right.
 * Parameters:
 *   widgets    Array of widgets that will get navigational relationship set.
 *   circular   Optional, last element connected to the first. Default false.
 */
function widgetNavigationLeftToRight(widgets, circular)
{
    for (var i = 0; i<widgets.length; ++i) {
        widgets[i].rightElement =
            (i<widgets.length) ? widgets[i+1] : widgets[i].rightElement;
        widgets[i].leftElement = (i>0) ? widgets[i-1] : widgets[i].leftElement;
    }
    if (circular) {
        widgets[0].leftElement = widgets[widgets.length-1];
        widgets[widgets.length-1].rightElement = widgets[0];
    }
}

/**
 * Sets widget key navigation relationship up-down.
 *   widgets    Array of widgets that will get navigational relationship set.
 *   circular   Optional, last element connected to the first. Default false.
 */
function widgetNavigationUpToDown(widgets, circular)
{
    for (var i = 0; i<widgets.length; ++i) {
        widgets[i].downElement =
            (i<widgets.length) ? widgets[i+1] : widgets[i].downElement;
        widgets[i].upElement = (i>0) ? widgets[i-1] : widgets[i].upElement;
    }
    if (circular) {
        widgets[0].upElement = widgets[widgets.length-1];
        widgets[widgets.length-1].downElement = widgets[0];
    }
}

/**
 * GenericWidget
 */
function GenericWidget(parentObject, x, y, width, height, props)
{
    if (arguments.length == 0) {
        DumpLog("GenericWidget called w/o args");
        return;
    }
    this.__class__ = "GenericWidget";
    this.parentObject = parentObject;
    this.box = createBox(parentObject, x, y, width, height, props);
    this.selected = false;

    try {
        parentObject.appendChild(this.box);
    }
    catch (e) {
        showCallStack();
        DumpLog("Cannot add Window to parentObject: " + parentObject);
        if (parentObject !== null && parentObject.__class__) {
            DumpLog("parentObject has type " + parentObject.__class__);
        }
        throw e;
    }

}

GenericWidget.prototype.blur = function ()
{
    this.selected = false;
    this.box.setBorder(4,gBorderColorNormal);
};

GenericWidget.prototype.highlight = function ()
{
    this.selected = true;
    this.box.setBorder(4,gBorderColorHighLight);
};

GenericWidget.prototype.setClass = function (className)
{
    this.box.setClass(className);
};

GenericWidget.prototype.setOpacity = function (opacity)
{
    this.box.setOpacity(opacity);
};

GenericWidget.prototype.setBackgroundColor = function (color)
{
    this.box.setBackgroundColor(color);
};

GenericWidget.prototype.setVisible = function (visible)
{
    this.box.setVisible(visible);
};

GenericWidget.prototype.isVisible = function ()
{
    return this.box.isVisible();
};

GenericWidget.prototype.appendChild = function (node)
{
    this.box.appendChild(node);
};

GenericWidget.prototype.insertBefore = function (newnode, oldnode)
{
    this.box.insertBefore(newnode,oldnode);
};

GenericWidget.prototype.removeChild = function (node)
{
    this.box.removeChild(node);
};

GenericWidget.prototype.getX = function ()
{
    return this.box.getX();
};

GenericWidget.prototype.getY = function ()
{
    return this.box.getY();
};

GenericWidget.prototype.move = function (x, y)
{
    this.box.move(x, y);
};

GenericWidget.prototype.resize = function (w, h)
{
    this.box.resize(w, h);
};

GenericWidget.prototype.getWidth = function ()
{
    return this.box.getWidth();
};

GenericWidget.prototype.getHeight = function ()
{
    return this.box.getHeight();
};

GenericWidget.prototype.setHeight = function (height)
{
    this.box.setHeight(height);
};

GenericWidget.prototype.setWidth = function (width)
{
    this.box.setWidth(width);
};

GenericWidget.prototype.moveToTop = function ()
{
    this.parentObject.removeChild(this.box);
    gRootElement.appendChild(this.box);
};

/**
 * GenericLabel
 * Standard widget for displaying text in one or multiple lines. The
 * constructor is backwards compatible but can also take a property bag
 * as the style parameter.
 */
function GenericLabel(x, y, width, height, parentObject, text, style,
                      backgroundColor)
{
    // Backward compatible, put extra params in props
    if (typeof(style) === "string") {
        props = {};
        props.className = style;
        props.backgroundColor = backgroundColor;
    }
    else {
        // Dictionary parameters
        props = style || {};
    }
    props.className = props.className || null;
    props.fontColor = props.fontColor || null;
    props.backgroundColor = props.backgroundColor || null;
    props.editable = props.editable || false;
    props.type = props.type || "";
    props.align = props.align || "left";
    props.fontSize = props.fontSize || 14;

    if (props.multiline === undefined && text) {
        props.multiline = text.length > 30;
    }

    GenericWidget.call(this, parentObject, x, y, width, height, {
                           backgroundColor: props.backgroundColor,
                           rounded: props.rounded});
    this.__class__ = "GenericLabel";

    this.text = createTextNode(this.box, text, {
                                   width: width, height: height,
                                   fontColor: props.fontColor,
                                   editable: props.editable,
                                   type: props.type,
                                   align: props.align,
                                   multiline: props.multiline,
                                   maxLength: props.maxLength,
                                   size: props.size});

    this.text.setFontSize(props.fontSize);

    if (style) {
        this.setClass(style);
    }
    this.setText(text);

}

GenericLabel.prototype.__proto__ = GenericWidget.prototype;

GenericLabel.prototype.setText = function (text)
{
    this.text.setText(text);
};

GenericLabel.prototype.getText = function ()
{
    return this.text.getText();
};

GenericLabel.prototype.setBold = function (bold)
{
    this.text.setBold(bold);
};

GenericLabel.prototype.getBold = function ()
{
    return this.text.getBold();
};

GenericLabel.prototype.setFontSize = function (fontSize)
{
    this.text.setFontSize(fontSize);
};

GenericLabel.prototype.setFontColor = function (fontColor)
{
    this.text.setFontColor(fontColor);
};

/**
 * GenericButton
 */
function GenericButton(x, y, width, height, selectFunc, parentObject, text)
{
    this.selected = false;
    this.select = selectFunc;
    this.enabled = true;
    this.colorMapEnabled = {bg: "#aaa", fg: "#000"};
    this.colorMapDisabled = {bg: "#777", fg: "#666"};

    var props = {
        backgroundColor: this.colorMapEnabled.bg,
        rounded: true,
        align: "center",
        fontColor: this.colorMapEnabled.fg,
        multiline: false
    };

    if (width == null) {
        width = 100;
    }
    if (height == null) {
        height = 23;
    }
    GenericLabel.call(this, x, y, width, height, parentObject, text, props);
    this.__class__ = "GenericButton";
    this.box.setClass("widgetsbuttonborder");
    this.blur();
}

GenericButton.prototype.__proto__ = GenericLabel.prototype;
GenericButton.prototype.setLabel = GenericLabel.prototype.setText;

GenericButton.prototype.onKeyPressed = function (keyCode)
{
    if (this.enabled && (keyCode == KEY_OK || keyCode == "Enter")) {
        if (!this.selected) {
            this.selected = true;
            this.blur();
        }
        else {
            this.selected = false;
            this.highlight();
        }
        this.select();
        return true;
    }
    return false;
};

GenericButton.prototype.disable = function ()
{
    this.enabled = false;
    this.box.setBackgroundColor(this.colorMapDisabled.bg);
    this.text.setFontColor(this.colorMapDisabled.fg);
};

GenericButton.prototype.enable = function ()
{
    this.enabled = true;
    this.box.setBackgroundColor(this.colorMapEnabled.bg);
    this.text.setFontColor(this.colorMapEnabled.fg);
};

/**
 * GenericGraphicWindow
 */
function GenericGraphicWindow(x, y, width, height, zIndex, selectFunc,
                              parentObject, titleText)
{
    GenericWidget.call(this, parentObject, x, y, width, height,
                       {rounded: true, backgroundColor: "#ccc",
                        zIndex: zIndex, clipped: true});
    this.__class__ = "GenericGraphicWindow";

    this.width = width;
    this.height = height;
    this.collapsedHeight = 21;
    this.setDefaultClipBox();

    this.title = new GenericLabel(0, 0, width, 22, this.box, " " + titleText,
                                  {fontColor: "white"});
    this.select = selectFunc;
    this.collapsed = false;

    this.blur();
}

GenericGraphicWindow.prototype.__proto__ = GenericWidget.prototype;

GenericGraphicWindow.prototype.blur = function ()
{
    GenericWidget.prototype.blur.call(this);
    this.title.setBackgroundColor(gBorderColorNormal);
};

/**
 * Indicates that the content of this widget has focus
 */
GenericGraphicWindow.prototype.focus = function ()
{
    GenericWidget.prototype.highlight.call(this);
    this.title.setBackgroundColor(gBorderColorHighLight);
};

GenericGraphicWindow.prototype.getText = function ()
{
    return this.title.getText();
};

/**
 * Indicates that this widget and not its content has focus
 */
GenericGraphicWindow.prototype.highlight = function ()
{
    GenericWidget.prototype.highlight.call(this);
    this.selected = false;
    this.title.setBackgroundColor(gBorderColorNormal);
};

GenericGraphicWindow.prototype.onKeyPressed = function (keyCode)
{
    if (keyCode == KEY_OK) {
        if (!this.selected) {
            this.focus();
        }
        else {
            this.highlight();
        }
        if (this.select) {
            this.select();
        }
        return true;
    }
    return false;
};

GenericGraphicWindow.prototype.setBodyText = function (text)
{
    if (!text) {
        text = "";
    }
    if (!this.bodyText) {
        this.bodyText = new GenericLabel(3, 22, this.width, null,
                                         this.box, text, {multiline: true});
    }
    else {
        this.bodyText.setText(text);
    }
};

GenericGraphicWindow.prototype.setPos = GenericWidget.prototype.move;

GenericGraphicWindow.prototype.setText = function (text)
{
    this.title.setText(text);
};

GenericGraphicWindow.prototype.setWidth = function (width)
{
    this.title.setWidth(width);
    GenericWidget.prototype.setWidth.call(this, width);
};

GenericGraphicWindow.prototype.setDefaultClipBox = function ()
{
    // Make the clipbox slightly bigger than widget in order to avoid clipping
    // the border
    this.box.setClipBox(-4, -4, this.width+8, this.height+8);
};

GenericGraphicWindow.prototype.toggleCollapse = function ()
{
    if (this.collapsed) {
        this.collapsed = false;
        this.box.setHeight(this.height);
        this.setDefaultClipBox();
    }
    else {
        this.collapsed = true;
        this.box.setHeight(this.collapsedHeight);
        this.box.setClipBox(-4, -4, this.width+8, this.collapsedHeight+8);
    }
};

/**
 * GenericWindow
 * This widget has no special features. Only for backward compability.
 */
function GenericWindow(x, y ,width, height, zIndex, parentObject, image)
{
    GenericWidget.call(this, parentObject, x, y, width, height,
                       {zIndex: zIndex});
    this.__class__ = "GenericWindow";

    this.setStyle = this.setClass;
    this.bgImage = null;
    if (typeof(image) != 'undefined' && image) {
        this.setBackgroundImage(image);
    }
}

GenericWindow.prototype.__proto__ = GenericWidget.prototype;

GenericWindow.prototype.removeChildNodes = function ()
{
    while (this.box.hasChildNodes()) {
        this.box.removeChild(this.box.firstChild);
    }
};

GenericWindow.prototype.setBackgroundImage = function (image)
{
    if (this.bgImage === null) {
        this.bgImage = new Picture(0, 0, this.width, this.height, null,
                                   this.box, image);
    }
    this.bgImage.changePicture(image);
};

/**
 * GenericLogWindow
 * This widget is a GraphicWindow with the ability to append text lines.
 * Inserted line goes on top.
 */
function GenericLogWindow (x, y, width, height, parentObject, size, title,
                           style)
{
    GenericGraphicWindow.call(this, x, y, width, height, null, null,
                              parentObject, title);
    this.__class__ = "GenericLogWindow";

    this.logBox = createLogBox(this.box, 3, 23, width, height-23, size);
    this.box.appendChild(this.logBox);
}

GenericLogWindow.prototype.__proto__ = GenericGraphicWindow.prototype;

GenericLogWindow.prototype.setFontSize = function (fontSize)
{
    this.logBox.setFontSize(fontSize);
};

GenericLogWindow.prototype.writeLog = function (text)
{
    this.logBox.writeLog(text);
};

GenericLogWindow.prototype.clear =
    GenericLogWindow.prototype.clearLog = function ()
{
    this.logBox.clear();
};

/**
 * GenericInput
 * Widget for user input via remote or keayboard.
 */
function GenericInput(x, y, width, height, selectFunc, parentObject, type,
                      maxLength, size)
{
    if (width == null) {
        width = 100;
    }
    if (height == null) {
        height = 30;
    }
    if (size == null) {
      size = maxLength;
    }

    GenericLabel.call(this, x, y, width, height, parentObject, "",
                      {editable: true, type: type, maxLength: maxLength,
                       size: size});
    this.__class__ = "GenericInput";
    this.setClass("widgetsinput");
    this.blur();
}

GenericInput.prototype.__proto__ = GenericLabel.prototype;

GenericInput.prototype.getValue = function ()
{
    return this.text.getText();
};

GenericInput.prototype.setValue = function (value)
{
    return this.text.setText(value);
};

/**
 * If trying to set focus before the widget is layouted, ekioh will crash.
 * Appending to the DOM-tree is not enough. A solution is setTimeout().
 */
GenericInput.prototype.highlight = function ()
{
    GenericLabel.prototype.highlight.call(this);
    this.box.setBackgroundColor(gBackgroundColorFocus);
    this.text.focus();
};

GenericInput.prototype.blur = function ()
{
    GenericLabel.prototype.blur.call(this);
    this.box.setBackgroundColor(gBackgroundColorNormal);
    this.text.blur();
};

/**
 * Widget that shows a list of options.
 * @param {number} x X coordinate
 * @param {number} y Y coordinate
 * @param {number} width Width
 * @param {number} height Height
 * @param parentObject Which widget the option list should be placed into
 * @param [selectFunc] Function handle that is called when an option is
 * selected
 * @constructor
 */
function GenericOptionList(x, y, width, height, parentObject, selectFunc)
{
  GenericWidget.call(this, parentObject, x, y, width, height,
                     {clipped: true});
  this.__class__ = "GenericOptionList";
  this.options = [];
  this.selectedIndex = -1;
  this.onSelect = selectFunc;
  this.width = width;
  this.height = height;
  this.useImages = false;
  this.hideSelected = false;

  /**
   * Function that is called when item selection changes. Default null.
   * @type function
   */
  this.onSelectionChanged=null;

  /**
   * On true, changes widget navigation so
   * list gets focus directly, without having to press ok. Navigate away with
   * arrows. Default false which gives old behavior.
   * @type boolean
   */
  // Set to false per default for backwards compatibility.
  this.focusOnSelection = true ;//false;

  this.scrollIndex = 0;
  this.labelHeight = 18;
  this.labels = []; // Array of Label widgets
  this.images = []; // Array of Picture widgets

  this.blur();
}

GenericOptionList.prototype.__proto__ = GenericWidget.prototype;

/**
 * Adds an option
 * @param value A value to store for the option
 * @param {string} text The text shown for the option
 * @param {string} [image_src] Url to an icon for the option
 */
GenericOptionList.prototype.addOption = function (value, text, image_src,
                                                  fontColor)
{
  if (!fontColor) {
    var fontColor = "black";
  }

  this.options.push({value: value, text: text, image_src: image_src,
                    fontColor: fontColor});
  // Use timer so that doLayout is only called once when many options are
  // added. This gives better performance.
  if (this.renderTimer!=null) {
    clearTimeout(this.renderTimer);
  }
  this.renderTimer = setTimeout(
    function(obj) {  // Create closure
      return function() {
        obj.doLayout();
        obj.renderTimer=null;
      };
    }(this), 1);
};

/**
 * Unsets focus of widget
 */
GenericOptionList.prototype.blur = function ()
{
  GenericWidget.prototype.blur.call(this);
  this.box.setBackgroundColor(gBackgroundColorNormal);
};

/**
 * Unsets focus of widget
 */
GenericOptionList.prototype.clear = function ()
{
  this.options = [];
  this.selectedIndex = -1;
  this.doLayout();
};

/**
 * Alias for clear
 * @function
 * @see #clear
 * @deprecated
 */
GenericOptionList.prototype.clearOptions = GenericOptionList.prototype.clear;

/**
 * Reposition the options in correct order
 * @private This function shouldn't be called from outside
 */
GenericOptionList.prototype.doLayout = function ()
{
  // Check if we need to create labels. This is done because creation of many
  // labels takes lots of time so we don't want to add more than needed and
  // what fits in the list.
  while ((this.labels.length+1)*this.labelHeight < this.height
         && this.labels.length < this.options.length) {
    var contentHeight = this.labels.length*this.labelHeight;
    this.labels.push(new GenericLabel(0, contentHeight, this.width,
                                      this.labelHeight, this.box, ""));
    this.images.push(new Picture(0, contentHeight, this.labelHeight,
                                 this.labelHeight, 0, this.box, null));
  }

  // Remove labels if needed
  if (this.labels.length > this.options.length) {
    for (var i = this.options.length; i < this.labels.length; ++i) {
      this.box.removeChild(this.labels[i].box);
      this.box.removeChild(this.images[i].image);
    }
    this.labels.splice(this.options.length,
                       this.labels.length - this.options.length);
    this.images.splice(this.options.length,
                       this.images.length - this.options.length);
  }

  // Check which option should be top item in the view.
  var selectedIndex = this.getSelectedIndex();
  if (selectedIndex < this.scrollIndex) {
    this.scrollIndex = selectedIndex;
  }
  else if (selectedIndex > (this.labels.length + this.scrollIndex - 1)) {
    this.scrollIndex = selectedIndex - this.labels.length + 1;
  }
  // Adjust to stay inside bounds (if e.g. options are removed)
  this.scrollIndex =
    Math.max(0, Math.min(this.scrollIndex,
                         this.options.length - this.labels.length));

  // Draw options
  for (var i = 0; i < this.labels.length; ++i) {
    this.labels[i].setText(this.options[this.scrollIndex+i].text);
    this.labels[i].setFontColor(this.options[this.scrollIndex + i].fontColor);
    if (selectedIndex==i+this.scrollIndex && !this.hideSelected) {
      this.labels[i].setBackgroundColor(gOptionListHighlightColor);
    }
    else {
      this.labels[i].setBackgroundColor(null);
    }
    if (this.useImages) {
      this.labels[i].move(30, null);
      this.images[i].changePicture(this.options[this.scrollIndex+i].image_src);
      this.images[i].setVisible(true);
    }
    else {
      this.labels[i].move(0, null);
      this.images[i].changePicture(null);
      this.images[i].setVisible(false);
    }
  }
};

/**
 * Not implemented
 */
GenericOptionList.prototype.disable = function ()
{
    // Ignore
};

/**
 * Not implemented
 */
GenericOptionList.prototype.enable = function ()
{
    // Ignore
};

/**
 * Enable icons for options in list
 * @param {boolean} [show] Shows icons if true, hides if false. Default true.
 */
GenericOptionList.prototype.enableImages = function (show)
{
  if (typeof show == "undefined") {
    show = true;
  }
  this.useImages = show;
  this.doLayout();
};

/**
 * Returns the first value of the option with the given text
 * @param {string} text Text to search for
 * @return Option value
 */
GenericOptionList.prototype.getValueByText = function (text)
{
  for (var i=0; i<this.options.length; i++) {
    if (this.options[i].text === text) {
      return this.options[i].value;
    }
  }
  return -1;
};

/**
 * Alias for getValueByText
 * @function
 * @see #getValueByText
 * @deprecated
 */
GenericOptionList.prototype.findOptionValueByText =
  GenericOptionList.prototype.getValueByText;

/**
 * Sets focus on widget
 */
GenericOptionList.prototype.focus = function ()
{
  GenericWidget.prototype.highlight.call(this);
  this.box.setBackgroundColor(gBackgroundColorFocus);
};

/**
 * Returns option value for given index
 * @param {int} index Index of option to return
 * @return Option value
 */
GenericOptionList.prototype.getValue = function (index)
{
  if (index >= 0 && index < this.options.length) {
    return this.options[index].value;
  }
  else {
    return null;
  }
};

/**
 * Returns the number of options
 * @return {int} Number of options in the list
 */
GenericOptionList.prototype.getLength = function ()
{
  return this.options.length;
};

/**
 * Alias for getLength
 * @function
 * @see #getLength
 * @deprecated
 */
GenericOptionList.prototype.getNrOfValues =
  GenericOptionList.prototype.getLength;

/**
 * Returns the index of the selected option
 * @return {int} Index of the selected option
 */
GenericOptionList.prototype.getSelectedIndex = function ()
{
  return this.selectedIndex;
};

/**
 * Returns the value of the selected option
 * @return option value
 * @throws Throws an Exception if nothing is selected
 */
GenericOptionList.prototype.getSelectedOption = function ()
{
  if (this.selectedIndex !== -1) {
    return this.options[this.selectedIndex].value;
  }
  throw "GenericOptionList: Nothing selected";
};

/**
 * Alias for getSelectedOption
 * @function
 * @see #getSelectedOption
 * @deprecated
 */
GenericOptionList.prototype.getSelected =
  GenericOptionList.prototype.getSelectedOption;

/**
 * Returns the descriptive text of the selected option
 * @return {string} Text of the selected option
 * @throws Throws an Exception if nothing is selected
 */
GenericOptionList.prototype.getSelectedText = function ()
{
  if (this.selectedIndex !== -1) {
    return this.options[this.selectedIndex].text;
  }
  throw "GenericOptionList: Nothing selected";
};

/**
 * Returns the value of the topmost option
 * @return Option value
 */
GenericOptionList.prototype.getTopValue = function ()
{
  return this.options[0].value;
};

/**
 * Highlight widget. If focusOnSelection is true this is the same as focus().
 * @see #focusOnSelection
 * @see #focus
 */
GenericOptionList.prototype.highlight = function ()
{
  if (this.focusOnSelection) {
    this.focus();
  }
  else {
    GenericWidget.prototype.highlight.call(this);
    this.selected = false; // Don't handle key input
    this.box.setBackgroundColor(gBackgroundColorHighlight);
  }
};

/**
 * Remove an option
 * @param value Value of the option to remove
 * @return {boolean} True if removed option, false if not found
 */
GenericOptionList.prototype.removeOption = function (value)
{
  for (var i=0; i<this.options.length; i++) {
    if (this.options[i].value == value) {
      this.options.splice(i, 1);
      if (this.getSelectedIndex() >= i) {
        this.setSelectedIndex(this.getSelectedIndex()-1);
      }
      this.doLayout();
      return true;
    }
  }
  return false;
};

/**
 * Scroll down one option
 */
GenericOptionList.prototype.scrollDown = function ()
{
  if (this.getSelectedIndex() < this.options.length-1) {
    this.setSelectedIndex(this.getSelectedIndex()+1);
  }
};

/**
 * Scroll up one option
 */
GenericOptionList.prototype.scrollUp = function ()
{
  if (this.getSelectedIndex() > 0) {
    this.setSelectedIndex(this.getSelectedIndex()-1);
  }
};

/**
 * Set height of widget.
 */
GenericOptionList.prototype.setHeight = function (height)
{
  this.box.setHeight(height);
};

/**
 * Sets the descriptive text for an option
 * @param value Value of the option to change
 * @param {string} text Text to change to
 * @return {boolean} True if successful
 */
GenericOptionList.prototype.setOptionText = function(value, text)
{
  for (var i=0; i<this.options.length; i++) {
    if (this.options[i].value == value) {
      this.options[i].text = text;
      this.doLayout();
      return true;
    }
  }
  return false;
};

/**
 * Alias of setOptionText
 * @function
 * @see #setOptionText
 * @deprecated
 */
GenericOptionList.prototype.changeText =
  GenericOptionList.prototype.setOptionText;

/**
 * Selects the option with the given value. This will fire an
 * onSelectionChanged event.
 * @param value Value of the option to select
 * @return {boolean} True if successful
 * @see #onSelectionChanged
 */
GenericOptionList.prototype.setSelectedOption = function (value)
{
  if (value) {
    for (var i=0; i<this.options.length; i++) {
      if (this.options[i].value == value) {
        this.setSelectedIndex(i);
        return true;
      }
    }
  }
  this.setSelectedIndex(0);
  return false;
};

/**
 * Alias of setSelectedOption
 * @function
 * @see #setSelectedOption
 * @deprecated
 */
GenericOptionList.prototype.setSelected =
  GenericOptionList.prototype.setSelectedOption;

/**
 * Selects the option with the given index. This will fire an
 * onSelectionChanged event.
 * @param {int} index Index of the option to select
 * @return {boolean} True if successful
 * @see #onSelectionChanged
 */
GenericOptionList.prototype.setSelectedIndex = function (index)
{
  if (index < 0 || index >= this.options.length) {
    DumpLog("Out of range: " + index + " >= " + this.options.length);
    this.selectedIndex = -1;
    this.doLayout();
    return false;
  }
  this.selectedIndex = index;
  this.doLayout();
  if (this.onSelectionChanged!=null) {
    this.onSelectionChanged({evtTarget: this,
                             selected: this.getSelectedOption()});
  }
  return true;
};

/**
 * Event handler for key pressed
 */
GenericOptionList.prototype.onKeyPressed = function (keyCode)
{
  if (keyCode == KEY_OK || keyCode == "Enter") {
    if (!this.focusOnSelection) {
      if (this.selected) {  // If list has focus
        this.highlight();
      }
      else {
        this.focus();
      }
    }
    if (this.onSelect) this.onSelect();
    return true;
  }
  if (!this.focusOnSelection && !this.selected) {
    // If list hasn't focus, return
    return false;
  }

  if (keyCode == KEY_UP) {
    if (this.getSelectedIndex() > 0) {
      this.scrollUp();
    }
    else {
      if (this.focusOnSelection) {
        setActiveWidget(this.upElement);
      }
    }
    return true;
  }
  if (keyCode == KEY_DOWN) {
    if (this.getSelectedIndex() < this.options.length-1) {
      this.scrollDown();
    }
    else {
      if (this.focusOnSelection) {
        setActiveWidget(this.downElement);
      }
    }
    return true;
  }
  if (this.focusOnSelection) {
    if (keyCode == KEY_LEFT) {
      setActiveWidget(this.leftElement);
      return true;
    }
    if (keyCode == KEY_RIGHT) {
      setActiveWidget(this.rightElement);
      return true;
    }
  }
  return false;
};

/**
 * Hides the selected index
 */
GenericOptionList.prototype.hideSelectedIndex = function ()
{
  this.hideSelected = true;
  this.doLayout();
};

/**
 * Shows the selected index
 */
GenericOptionList.prototype.showSelectedIndex = function ()
{
  this.hideSelected = false;
  this.doLayout();
};

/**
 * State tracker object. Outputs a table on screen
 * containing state transition statistics. Can also
 * be used to keep track of the current state.
 * @param {int} X position in pixles
 * @param {int} Y position in pixles
 * @param {int} width in pixles
 * @param {int} height in pixles
 * @param {array of string} states to keep track of
 * @param {parent.html} parentObject
 * @param {string} starting state
 * @param {style} style from css to be applied
 *
 * This widget is a copy of StateTrackerObject from widgets.js  (html).
 * Since there is no table support in svg, this widgets uses labels instead
 * The only difference is the method drawTable()
 */
function GenericStateTrackerObject(xPos, yPos, width, height, states,
                                   parentObject, initState, style)
{
    this.transitions = new Array();
    this.stateIds = new Array();
    this.parentObject = parentObject;

    this.lineHeight = 20;
    this.padding = 5;
    if (initState != null) {
        this.currentState = initState;
    }

    //Draws the transition matrix on screen
    this.drawTable = function ()
    {
        // @TODO: Optimize: Don't rebuild table, just update texts
        // Remove all children
        while (this.box.firstChild) {
            this.box.removeChild(this.box.firstChild);
        }
        var colStarts = new Array();
        colStarts.push(60);

        for (var i=0; i<this.states.length; i++) {
            var label = new GenericLabel(colStarts[i], 0, null, null,
                                         this.box, states[i]);
            label.setFontSize(15);
            var bbox = label.box.getBBox();
            colStarts.push(colStarts[i] + bbox.width + this.padding);
        }
        for (var i=0; i<this.states.length;i++) {
            var label = new GenericLabel(0, this.lineHeight*(i+1), null, null,
                                         this.box, this.states[i]);
            label.setFontSize(15);

            for (var j=0; j< this.transitions.length ;j++) {
                var label = new GenericLabel(colStarts[j],
                                             this.lineHeight*(i+1), null, null,
                                             this.box,
                                             this.transitions[i][j] + "");
                label.setFontSize(15);
            }
        }
    };

    // Marks a transition as invalid with "-"
    this.setInvalidTransition = function (state1, state2)
    {
        var i = this.stateIds[state1];
        var j = this.stateIds[state2];
        this.transitions[i][j] = "-";
        this.drawTable();
    };

    // Increments the value of the given transition
    this.registerTransition = function (state1, state2)
    {
        var i = this.stateIds[state1];
        var j = this.stateIds[state2];
        if (this.transitions[i][j] != "-") {
            this.transitions[i][j]++;
            this.drawTable();
            this.currentState = state2;
        }
        else {
            DumpLog("Invalid transition registered.");
        }
    };

    // Resets all values to 0
    this.resetTransitions = function ()
    {
        for (var i=0;i<this.transitions.length;i++) {
            for (var j=0;j<this.transitions.length;j++) {
                if (this.transitions[i][j] != "-") {
                    this.transitions[i][j] = 0;
                }
            }
        }
        this.drawTable();
    };

    // Initializes matrix and html objects
    this.states = states;
    // Initialize state id's
    for (var i=0;i<this.states.length;i++) {
        this.stateIds[states[i]]=i;
    }
    // Initialize transitions matrix
    this.transitions = new Array();
    for (var i=0;i<states.length;i++) {
        this.transitions[i] = new Array();
        for (var j=0;j<states.length;j++) {
            this.transitions[i][j] = 0;
        }
    }

    GenericWidget.call(this, parentObject, xPos, yPos, width, height);

    // Init graphics
    if (style) {
        this.setStyle(style);
    }
    this.drawTable();
}
GenericStateTrackerObject.prototype.__proto__ == GenericWidget.prototype;
GenericStateTrackerObject.prototype.setStyle =
    GenericWidget.prototype.setClass;

/**
 * GenericProgressbar
 * @TODO: needs implementation, steal from html widget
 */
function GenericProgressbar(x, y, width, height, zIndex, parentObject)
{
    var borderWidth = 4;

    // This is a workaround for Ekioh's and Webkit's different border handling.
    this.browserBorder = borderWidth;
    if (gIsSVGBrowser) {
      this.browserBorder = 0;
    }

    GenericWidget.call(this, parentObject, x, y, width + this.browserBorder,
                       height + this.browserBorder,
                       {backgroundColor: "#ccc", rounded: true,
                       zIndex: zIndex});

    this.box.setBorder(0, gBorderColorNormal);
    this.width = width;
    this.maxValue = 100;
    this.minValue = 0;
    this.value = 0;
    this.leftValue = -1;
    this.pointerValue = -1;
    this.pointerWidth = borderWidth;

    this.foreground = createBox(this.box, 10, 0, 100, height, {
                                    backgroundColor: gBorderColorNormal});
    this.foreground.setOpacity("0.5");

    this.box.appendChild(this.foreground);

    this.pointer = createBox(this.box, 20, 0, this.pointerWidth, height, {
                                 backgroundColor: gBorderColorNormal});
    this.box.appendChild(this.pointer);

    this.border = createBox(this.box, 0, 0, width - this.browserBorder,
                            height - this.browserBorder,
                            {backgroundColor: "none", rounded: true});
    this.border.setBorder(borderWidth, gBorderColorNormal);
    this.box.appendChild(this.border);

}

GenericProgressbar.prototype.__proto__ = GenericWidget.prototype;

GenericProgressbar.prototype.getLeftValue = function ()
{
    return this.leftValue;
};

GenericProgressbar.prototype.getMaxValue = function ()
{
    return this.maxValue;
};

GenericProgressbar.prototype.getMinValue = function ()
{
    return this.minValue;
};

GenericProgressbar.prototype.getPointerValue = function ()
{
    return this.pointerValue;
};

GenericProgressbar.prototype.getValue = function ()
{
    return this.value;
};

GenericProgressbar.prototype.redraw = function ()
{
    var left;
    var width;
    if (this.leftValue == -1) {
        left = 0;
    }
    else {
        left = Math.round((this.leftValue - this.minValue)
                          / (this.maxValue - this.minValue)
                          * (this.width));
        left = Math.min(Math.max(left, this.browserBorder), this.width);
    }

    width = Math.round((this.value - this.minValue)
                       / (this.maxValue - this.minValue)
                       * (this.width) - left);
    width = Math.min(Math.max(width, this.browserBorder),
                     this.width - left);
    left = Math.min(Math.max(left, this.browserBorder / 2),
                    this.width - width);

    this.foreground.move(left, this.browserBorder / 2);
    this.foreground.setWidth(width);
    if (this.pointerValue == -1) {
        this.pointer.setVisible(false);
    }
    else {
        this.pointer.setVisible(true);
        left = Math.round((this.pointerValue - this.minValue)
                          / (this.maxValue - this.minValue)
                          * (this.width));
        left = Math.min(Math.max(left, this.browserBorder / 2),
                        this.width - this.pointerWidth);
        this.pointer.move(left, this.browserBorder / 2);
    }
};

GenericProgressbar.prototype.resize = function (width, height)
{
    this.box.resize(width+this.browserBorder, height+this.browserBorder);
    this.foreground.setHeight(height);
    this.pointer.setHeight(height);
    this.border.resize(width-this.browserBorder, height-this.browserBorder);
};

GenericProgressbar.prototype.setLeftValue = function (leftValue)
{
    this.leftValue = leftValue;
};

GenericProgressbar.prototype.setMaxValue = function (maxValue)
{
    this.maxValue = maxValue;
};

GenericProgressbar.prototype.setMinValue = function (minValue)
{
    this.minValue = minValue;
};

GenericProgressbar.prototype.setPointerValue = function (pointerValue)
{
    this.pointerValue = pointerValue;
};

GenericProgressbar.prototype.setValue = function (value)
{
    this.value = value;
};

/**
 * Mimics DlnaWindow used in DlnaPortal that is used to display images. This
 * widget is missing support for autorotation.
 */
function GenericDlnaWindow(x, y, width, height, zIndex, parentObject, rotation,
                           image)
{
    GenericWidget.call(this, parentObject, x, y, width, height);
    this.width = width;
    this.height = height;
    this.bgImage = null;
    if (image) {
        this.setBackgroundImage(image);
    }
}

GenericDlnaWindow.prototype.__proto__ = GenericWidget.prototype;

GenericDlnaWindow.prototype.setAutorotation = function ()
{
    DumpLog("GenericDlnaWindow.setAutorotation() is not implemented");
};

GenericDlnaWindow.prototype.setBackgroundImage = function (image)
{
    if (!this.bgImage) {
        this.bgImage = new Picture(0, 0, this.width, this.height, null,
                                   this.box, image);
    }
    else {
        this.bgImage.changePicture(image);
    }
};

/**
 * GenericRectangle
 */
function GenericRectangle(x, y, width, height, parentObject, props)
{
    props = props || {};
    props.backgroundColor = props.backgroundColor || null;
    props.lineColor = props.lineColor || gBorderColorNormal;
    props.lineWidth = props.lineWidth || 4;
    props.rounded = props.rounded || false;

    GenericWidget.call(this, parentObject, x, y, width, height, {
                           backgroundColor: props.backgroundColor,
                           rounded: props.rounded});

    this.__class__ = "GenericRectangle";

    this.box.setBorder(props.lineWidth, props.lineColor);
}

GenericRectangle.prototype.__proto__ = GenericWidget.prototype;

/**
 * GenericTeletext
 * Generic Widget for displaying telext.
 * properties:
 * props.isTransparent - bool - transaprent background or not
 * props.isVisible - bool - visible or not
 */
function GenericTeletext(x, y, width, height, parentObject, props)
{
  GenericWidget.call(this, parentObject, x, y, width, height, props);
  this.__class__ = "GenericWindow";
  if (props == null) {
      props = {};
  }
  this.teletext = createTeletextNode(0, 0, width, height);
  props.isTransparent = props.isTransparent || false;
  props.isVisible = props.isVisible || false;
  this.appendChild(this.teletext.getNode());
  this.setTransparent(props.isTransparent || false);
  this.setVisible(props.isVisible || false);
  DumpLog("GenericTeletext: created teletext plugin " + this.teletext);
}

GenericTeletext.prototype.__proto__ = GenericWidget.prototype;

GenericTeletext.prototype.inputRedKey = function ()
{
  this.teletext.inputRedKey();
};

GenericTeletext.prototype.inputGreenKey = function ()
{
  this.teletext.inputGreenKey();
};

GenericTeletext.prototype.inputYellowKey = function ()
{
  this.teletext.inputYellowKey();
};

GenericTeletext.prototype.inputCyanKey = function ()
{
  this.teletext.inputCyanKey();
};

GenericTeletext.prototype.gotoIndexPage = function ()
{
  this.teletext.gotoIndexPage();
};

GenericTeletext.prototype.gotoPreviousPage = function ()
{
  this.teletext.gotoPreviousPage();
};

GenericTeletext.prototype.gotoNextPage = function ()
{
  this.teletext.gotoNextPage();
};

GenericTeletext.prototype.setControllableSubpages = function (mode)
{
  this.teletext.setControllableSubpages(mode);
};

GenericTeletext.prototype.isSubpagesVisible = function ()
{
  return this.teletext.isSubpagesVisible();
};

GenericTeletext.prototype.gotoPreviousSubpage = function ()
{
  this.teletext.gotoPreviousSubpage();
};

GenericTeletext.prototype.gotoNextSubpage = function ()
{
  this.teletext.gotoNextSubpage();
};

GenericTeletext.prototype.inputDigit = function (digit)
{
  if (digit >= 0 && digit <= 9) {
    try {
      this.teletext.inputDigit(digit);
    }
    catch (err) {
      DumpLog("GenericTeletext.inputDigit ERROR " + err);
    }
  }
};

GenericTeletext.prototype.setTransparent = function (isTransparent)
{
  this.teletext.setTransparent(isTransparent);
};

GenericTeletext.prototype.isTransparent = function ()
{
  return this.teletext.isTransparent();
};

GenericTeletext.prototype.setVisible = function (isVisible)
{
  this.teletext.setVisible(isVisible);
};

GenericTeletext.prototype.isVisible = function ()
{
  return this.teletext.isVisible();
};

GenericTeletext.prototype.setHeight = function (height)
{
  this.box.setHeight(height);
  this.teletext.setHeight(height);
};

GenericTeletext.prototype.setWidth = function (width)
{
  this.box.setWidth(width);
  this.teletext.setWidth(width);
};

GenericTeletext.prototype.setPosX = function (x)
{
  this.box.move(x, this.getY());
  this.teletext.setPosX(x);
};

GenericTeletext.prototype.setPosY = function (y)
{
  this.box.move(this.getX(),y);
  this.teletext.setPosY(y);
};

// Override original functions with generic
Label = GenericLabel;
Button = GenericButton;
GraphicWindow = GenericGraphicWindow;
LogWindow = GenericLogWindow;
Window = GenericWindow;
StateTrackerObject = GenericStateTrackerObject;
OptionList = GenericOptionList;
Progressbar = GenericProgressbar;
Input = GenericInput;
Rectangle = GenericRectangle;
