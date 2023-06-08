/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

function ViewingBehaviorLogPrint(stbId, agentId, eventId, stamp,
                  value0, value1, value2, value3, value4,
                  value5, value6, value7, value8, value9)
{
  DumpLog("=========== VBL Print start ==============");
  DumpLog("STBID: " + stbId + ", Agent ID: " + agentId + ", Event ID: " + eventId);
  DumpLog("Time Stamp: " + stamp + "[" + (new Date(stamp)) + "]");
  if (value0 != undefined) {
    DumpLog("Value_0: " + value0);
  }
  if (value1 != undefined) {
    DumpLog("Value_1: " + value1);
  }
  if (value2 != undefined) {
    DumpLog("Value_2: " + value2);
  }
  if (value3 != undefined) {
    DumpLog("Value_3: " + value3);
  }
  if (value4 != undefined) {
    DumpLog("Value_4: " + value4);
  }
  if (value5 != undefined) {
    DumpLog("Value_5: " + value5);
  }
  if (value6 != undefined) {
    DumpLog("Value_6: " + value6);
  }
  if (value7 != undefined) {
    DumpLog("Value_7: " + value7);
  }
  if (value8 != undefined) {
    DumpLog("Value_8: " + value8);
  }
  if (value9 != undefined) {
    DumpLog("Value_9: " + value9);
  }
  DumpLog("=========== VBL Print end and push =======");
  var record = {AgentId: agentId,
                      EventType : eventId};
  if (value0 != undefined) {
    record.Value_0 = value0;
  }
  if (value1 != undefined) {
    record.Value_1 = value1;
  }
  if (value2 != undefined) {
    record.Value_2 = value2;
  }
  if (value3 != undefined) {
    record.Value_3 = value3;
  }
  if (value4 != undefined) {
    record.Value_4 = value4;
  }
  if (value5 != undefined) {
    record.Value_5 = value5;
  }
  if (value6 != undefined) {
    record.Value_6 = value6;
  }
  if (value7 != undefined) {
    record.Value_7 = value7;
  }
  if (value8 != undefined) {
    record.Value_8 = value8;
  }
  if (value9 != undefined) {
    record.Value_9 = value9;
  }
  record.EventTimeStamp = stamp;
  cns.logService.push(record);
}

function ViewingBehaviorLog()
{
  var vbmCallback = null;//TODO when cns VBM Client is ready
  var otherVBL = null;
  var self = this;
  var stbId = '000000';
  var msAdjustment = 0;
  var timeBase;
  var timerTDT = null;
  var queue = new Array();
  var TIMEOUT_TRYTDT = 1000;
  var QUEUE_MAX_LEN = 100;
  var bootTimeName = "cfg.custom.vbl.boottime";
  var is = toi.informationService;
  var vbmClientVersion = "";

  this.AGENT_ID_BOOT = 1;
  this.AGENT_ID_REPORT = 2;
  this.AGENT_ID_ZAPPING = 3;
  this.AGENT_ID_LOG = 4;
  this.AGENT_ID_APP = 5;
  this.AGENT_ID_PVR_VOL = 6;
  this.AGENT_ID_PVR_PER = 7;
  this.AGENT_ID_VOD = 8;
  this.AGENT_ID_DROP_COUNTER = 9;

  this.BOOTSTATUS_POWEROFF_TO_ACTIVE = 0;
  this.BOOTSTATUS_POWEROFF_TO_STANDBY = 1;
  this.BOOTSTATUS_ACTIVE_TO_STANDBY = 2;
  this.BOOTSTATUS_ACTIVE_TO_ACTIVE = 3;
  this.BOOTSTATUS_STANDBY_TO_ACTIVE = 4;
  this.BOOTSTATUS_STANDBY_TO_STANDBY = 5;

  this.ZAPPING_WAY_OTHER = 0;
  this.ZAPPING_WAY_NUMBERKEY = 1;
  this.ZAPPING_WAY_UP_OR_DOWN = 2;
  this.ZAPPING_WAY_BANNER = 3;
  this.ZAPPING_WAY_EPG = 4;
  this.ZAPPING_WAY_BACKKEY = 5;
  this.ZAPPING_WAY_MAINMENU = 6;
  this.ZAPPING_WAY_REMINDER = 7;
  this.ZAPPING_WAY_LIVETV = 8;
  this.ZAPPING_WAY_FROM_THIRDPARTY = 9;
  this.ZAPPING_WAY_INFORMATION_OBJECT = gIsObjectConst.VBL_ZAPPINGWAY;

  this.ZAPPING_PROGRAM_ACTIVED_UNKNOWN = 'Unknown';
  this.ZAPPING_PROGRAM_ACTIVED_YES = 'Yes';
  this.ZAPPING_PROGRAM_ACTIVED_NO = 'No';

  function ReportOne(isContinuous)
  {
    var item;
    if (queue.length == 0) {
      return;
    }

    item = queue.shift();
    
    if (isContinuous == true) {
      setTimeout(function ()
        {
          ReportOne(true);
        }, 10);
    }
    
    if ((item === undefined) || (item.StbId != stbId)) {
      return;
    }

    if (vbmCallback != null) {
      var tmpTime = new Date(item.Stamp);
      if (tmpTime.getFullYear() < 2013) {
        item.Stamp += msAdjustment;
      }
      vbmCallback(item.StbId, item.AgentId, item.EventId, item.Stamp,
                  item.Value0, item.Value1, item.Value2, item.Value3, item.Value4,
                  item.Value5, item.Value6, item.Value7, item.Value8, item.Value9);
    }
  }
  
  function TryTDT()
  {
    var curTime = new Date();
    clearTimeout(timerTDT);
    timerTDT = null;
    DumpLog("VBL TryTDT curTime.getFullYear() = " + curTime.getFullYear());
    if (curTime.getFullYear() < 2013) {
      timerTDT = setTimeout(TryTDT, TIMEOUT_TRYTDT);
      timeBase = curTime;
    }
    else {
      msAdjustment = curTime.getTime() - (timeBase.getTime() + TIMEOUT_TRYTDT/2);
      if (is.isObjectDefined(bootTimeName)) {
        var bootTime = is.getObject(bootTimeName) * 1;
        var bootTimeDate = new Date(bootTime);
        if (bootTimeDate.getFullYear() < 2013) {
          bootTime += msAdjustment;
          is.setObject(bootTimeName, bootTime.toString(), is.STORAGE_VOLATILE);
        }
      }
      DumpLog("VBL TryTDT msAdjustment = " + msAdjustment);
      ReportOne(true);
    }
  }

  this.GetAdjstment = function()
  {
    var adjustment = 0;
    if (otherVBL != null) {
      adjustment = otherVBL.GetAdjstment();
    }
    else {
      adjustment = msAdjustment;
    }
    return adjustment;
  }

  this.PushQueue = function(stbId, agentId, eventId, stamp,
                              value0, value1, value2, value3, value4,
                              value5, value6, value7, value8, value9)
  {
    queue.push({StbId: stbId,
                      AgentId: agentId,
                      EventId: eventId,
                      Stamp: stamp,
                      Value0: value0,
                      Value1: value1,
                      Value2: value2,
                      Value3: value3,
                      Value4: value4,
                      Value5: value5,
                      Value6: value6,
                      Value7: value7,
                      Value8: value8,
                      Value9: value9});
    if (timerTDT == null) {
      timeBase = new Date();
      timerTDT = setTimeout(TryTDT, 1000);
    }
    
    DumpLog("VBL queue size = " + queue.length);

    if (queue.length > QUEUE_MAX_LEN) {
      ReportOne(false);
    }
  }

  this.Generic = function(agentId, eventId, value0, value1, value2, value3,
                              value4, value5, value6, value7, value8, value9)
  {
    if (otherVBL != null) {
      return otherVBL.Generic(agentId, eventId, value0, value1, value2, value3,
                             value4, value5, value6, value7, value8, value9);
    }
    else {
      if (vbmCallback != null) {
        var stamp = new Date();
        if (!is.isObjectDefined(bootTimeName)) {
          var bootTime = stamp.getTime();
          is.setObject(bootTimeName, bootTime.toString(), is.STORAGE_VOLATILE);
        }

        if ((stamp.getFullYear() < 2013) || (queue.length != 0)) {
          return this.PushQueue(stbId, agentId, eventId, stamp.getTime(),
                  value0, value1, value2, value3, value4,
                  value5, value6, value7, value8, value9);
        }

        var currentTime = stamp.getTime();
        return vbmCallback(stbId, agentId, eventId, currentTime,
                  value0, value1, value2, value3, value4,
                  value5, value6, value7, value8, value9);
      }
    }
  }

  this.GetVbmClientVersion = function()
  {
    if ((top.viewingBehaviorLogger != undefined)&&
      (top.viewingBehaviorLogger != null)&&
      (top.viewingBehaviorLogger != self)) {
      return otherVBL.GetVbmClientVersion();
    }
    else if (window == top.window) { //caller should be background.
        return vbmClientVersion;
    }
    DumpLog("[ERROR]VbmClientVersion = " + vbmClientVersion);
    return vbmClientVersion;
  }

  this.Destroy = function()
  {
    // Empty
  }

  if ((top.viewingBehaviorLogger != undefined)&&
    (top.viewingBehaviorLogger != null)&&
    (top.viewingBehaviorLogger != self)) {
    otherVBL = top.viewingBehaviorLogger;
    DumpLog("VBL DEBUG - This is not the first one, let it to be singleton");
  }
  else if (window == top.window) { //caller should be background.
    var is = toi.informationService;
    stbId = is.getObject(gIsObjectConst.STBID);

    //@TODO: Implement vbmCallback
    vbmCallback = ViewingBehaviorLogPrint;
    DumpLog("VBL DEBUG - This is called by background");
    try {
      cns.logService.init();
      vbmClientVersion = cns.logService.version();
      DumpLog("vbmClientVersion = " + vbmClientVersion);
    }
    catch (e) {
      DumpLog("cns.logService.init() exception: " + e);
    }
  }
  else {
    DumpLog("VBL ERROR - shall not enter this case");
  }
}

