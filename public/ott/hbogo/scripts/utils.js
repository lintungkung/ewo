/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

function QueryString()
{
  var name, value, i;
  var str = location.href;
  var num = str.indexOf("?");
  str = str.substr(num+1);
  var arrtmp = str.split("&");
  for (i=0; i < arrtmp.length; i++) {
  	num=arrtmp[i].indexOf("=");
	  if(num>0){
	    name=arrtmp[i].substring(0,num);
	    value=arrtmp[i].substr(num+1);
	    this[name]=value;
	  }
  }
}

/** contructor a new delete func 
     left key to delete/move left
	 back key to return to up level
*/
function InputNewDelete(inputElement)
{
  var oldvalue = inputElement.value;
  var newvalue = null;
  var focus = inputElement.selectionStart;
  if (focus == 0) { // cannot delete more
    return;
  }
  focus--;

  if (focus < oldvalue.length) {
    newvalue = oldvalue.substr(0, focus) + oldvalue.substr(focus+1, oldvalue.length-focus-1);
  }
  else {// reach rightmost
    newvalue = oldvalue.substr(0, focus);
  }		
  inputElement.value = newvalue;			
  inputElement.setSelectionRange(focus,focus);	
}
