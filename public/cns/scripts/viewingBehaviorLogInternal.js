/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

/*  Please include viewingBehaviorLog.js file before using these widgets */

function ViewingBehaviorLogInternal()
{
  var self = this;
  var vbl = new ViewingBehaviorLog();
  var lastBootReportTime = 0;
  var isTimeCalibrated = false;
  var bootTimeName = "cfg.custom.vbl.boottime";
  var is = toi.informationService;

  this.BOOTSTATUS_POWEROFF_TO_ACTIVE = vbl.BOOTSTATUS_POWEROFF_TO_ACTIVE;
  this.BOOTSTATUS_POWEROFF_TO_STANDBY = vbl.BOOTSTATUS_POWEROFF_TO_STANDBY;
  this.BOOTSTATUS_ACTIVE_TO_STANDBY = vbl.BOOTSTATUS_ACTIVE_TO_STANDBY;
  this.BOOTSTATUS_ACTIVE_TO_ACTIVE = vbl.BOOTSTATUS_ACTIVE_TO_ACTIVE;
  this.BOOTSTATUS_STANDBY_TO_ACTIVE = vbl.BOOTSTATUS_STANDBY_TO_ACTIVE;
  this.BOOTSTATUS_STANDBY_TO_STANDBY = vbl.BOOTSTATUS_STANDBY_TO_STANDBY;

  this.ZAPPING_WAY_OTHER = vbl.ZAPPING_WAY_OTHER;
  this.ZAPPING_WAY_NUMBERKEY = vbl.ZAPPING_WAY_NUMBERKEY;
  this.ZAPPING_WAY_UP_OR_DOWN = vbl.ZAPPING_WAY_UP_OR_DOWN;
  this.ZAPPING_WAY_BANNER = vbl.ZAPPING_WAY_BANNER;
  this.ZAPPING_WAY_EPG = vbl.ZAPPING_WAY_EPG;
  this.ZAPPING_WAY_BACKKEY = vbl.ZAPPING_WAY_BACKKEY;
  this.ZAPPING_WAY_MAINMENU = vbl.ZAPPING_WAY_MAINMENU;
  this.ZAPPING_WAY_REMINDER = vbl.ZAPPING_WAY_REMINDER;
  this.ZAPPING_WAY_LIVETV = vbl.ZAPPING_WAY_LIVETV;
  this.ZAPPING_WAY_FROM_THIRDPARTY = vbl.ZAPPING_WAY_FROM_THIRDPARTY;
  this.ZAPPING_WAY_INFORMATION_OBJECT = vbl.ZAPPING_WAY_INFORMATION_OBJECT;

  this.ZAPPING_PROGRAM_ACTIVED_UNKNOWN = vbl.ZAPPING_PROGRAM_ACTIVED_UNKNOWN;
  this.ZAPPING_PROGRAM_ACTIVED_YES = vbl.ZAPPING_PROGRAM_ACTIVED_YES;
  this.ZAPPING_PROGRAM_ACTIVED_NO = vbl.ZAPPING_PROGRAM_ACTIVED_NO;

  this.Boot = function(bootStatus)
  {
    var eventId = 0;
    var activeTime = 0;

    var currentDate = new Date();
    var currentTime = currentDate.getTime();
    if (!isTimeCalibrated && currentDate.getFullYear() >= 2013) {
      if (lastBootReportTime !== 0) {
        var date = new Date(lastBootReportTime);
        if (date.getFullYear() < 2013) {
          var adjustment = vbl.GetAdjstment();
          lastBootReportTime += adjustment;
        }
      }
      isTimeCalibrated = true;
    }

    if (bootStatus !== self.BOOTSTATUS_POWEROFF_TO_ACTIVE &&
        bootStatus !== self.BOOTSTATUS_POWEROFF_TO_STANDBY) {
      activeTime = currentTime - lastBootReportTime;
    }
    else {
      activeTime = 0;
    }

    lastBootReportTime = currentTime;

    DumpLog("[VBL]" + "Boot status = " + bootStatus);
    return vbl.Generic(vbl.AGENT_ID_BOOT, eventId, bootStatus, activeTime);
  }

  this.Report = function() // VBM report first
  {
    var eventId = 0;
    var hddSerial = 0;
    var hddSize = 0;
    var hddUsed = 0;
    var soId = 0;
    var ip = '000.000.000.000';
    var resolution = '';
    var audiotype = '';
    var audioTrack = '';
    var bootTime = 0;
    var bouquetId = 0;
    var is = toi.informationService;

    //@TODO:  Implement hdSerial, hdSize, hdUsed(%) when importing PVR feature

    if (is.isObjectDefined(gIsObjectConst.SMS_APP_ZIPCODE)) {
      soId = is.getObject(gIsObjectConst.SMS_APP_ZIPCODE).substr(0, 2);
    }

    var configSession = null;
    try {
      var networkConf;
      var networkDevices;

      configSession = toi.netService.createConfigurationSession();
      networkConf = toi.netService.getConfiguration();
      networkDevices = networkConf.getNetworkDevices(
        toi.consts.ToiNetConfiguration.NET_DEVICE_TYPE_ALL);
      for (var i = 0; i < networkDevices.length; i++) {
        if (networkConf.getGenericDeviceInfo(networkDevices[i]).type ===
          toi.consts.ToiNetConfiguration.NET_DEVICE_TYPE_ETHERNET) {
          var ethDevice = networkConf.getEthernetDevice(networkDevices[i]);
          var nicInfo = ethDevice.getInterfaceInfo(
              toi.consts.ToiNetIpDevice.DEFAULT_INTERFACE);
          ip = nicInfo.ipAddress;
          break;
        }
      }
      configSession.releaseInstance();
    }
    catch(e) {
      alert(e);
      if (configSession != null) {
        configSession.releaseInstance();
      }
    }

    if (is.isObjectDefined(gIsObjectConst.CONFIG_RESOLUTION)) {
      resolution = is.getObject(gIsObjectConst.CONFIG_RESOLUTION);
    }

    if (is.isObjectDefined(gIsObjectConst.CONFIG_DOLBY)) {
      audiotype = is.getObject(gIsObjectConst.CONFIG_DOLBY);
    }

    if (is.isObjectDefined(gIsObjectConst.CONFIG_AUDIOLANG)) {
      var temp = is.getObject(gIsObjectConst.CONFIG_AUDIOLANG);
      temp = temp.substr(0, 3).toLowerCase();
      switch (temp) {
      case 'chi':
        audioTrack = 'Chinese';
        break;
      case 'eng':
        audioTrack = 'English';
      default:
        audioTrack = temp;
        break;
      }
    }

    if (is.isObjectDefined(bootTimeName)) {
      var time = is.getObject(bootTimeName) * 1;
      var date = new Date(time);
      bootTime = time;
    }

    var systemBId = null;
    if (is.isObjectDefined(gIsObjectConst.HIGHEST_PRIOR_BOUQUET_ID)) {
      systemBId = is.getObject(gIsObjectConst.HIGHEST_PRIOR_BOUQUET_ID);
    }

    if (systemBId === null || systemBId === "") {
      try { // get Bouquent ID from smart card
        var opId = null;
        if (!is.isObjectDefined(gIsObjectConst.CA_OPERATOR_ID)) {
          is.setObject(
            gIsObjectConst.CA_OPERATOR_ID, "1226", is.STORAGE_PERMANENT);
        }
        opId = is.getObject(gIsObjectConst.CA_OPERATOR_ID) * 1;

        var infolist = toi.novelService.getAcInfoList(opId);
        var bqtid = -1;
        for (var i = 0 ; i < infolist.length ; ++i) {
          if (infolist[i].name == "BouquetID") {
            bqtid = infolist[i].value;
            break;
          }
        }

        if (bqtid > 0) { // Initial BID value: 0
          bouquetId = bqtid * 1;
        }
        else {
          if (typeof(DEFAULT_BOUQUET_ID) === "number") {
            bouquetId = DEFAULT_BOUQUET_ID;
          }
          else {
            bouquetId = 0;
          }
        }
      }
      catch (e) {
        DumpLog("novelService:" + e);
        if (typeof(DEFAULT_BOUQUET_ID) === "number") {
          bouquetId = DEFAULT_BOUQUET_ID;
        }
        else {
          bouquetId = 0;
        }
      }
    }
    else {
      bouquetId = systemBId * 1;
    }

    return vbl.Generic(vbl.AGENT_ID_REPORT, eventId,
                  hddSerial, hddSize, hddUsed, soId, ip, resolution,
                  audiotype, audioTrack, bootTime, bouquetId);
  }

  this.ReportPowerOn = function() // VBM report second
  {
    var eventId = 1;
    var hwVersion = '';
    var swVersion = '';
    var epgVersion = '';
    var loaderVersion = '';
    var vbmClientVersion = '';
    var stbUpgradeTime = 0;
    var uiLanguage = 'Chinese';
    var ratio = 'Auto';
    var dsiplay = 'FullScreen';
    var is = toi.informationService;

    if (is.isObjectDefined(gIsObjectConst.HARD_VERSION)) {
      hwVersion = is.getObject(gIsObjectConst.HARD_VERSION);
    }

    if (is.isObjectDefined(gIsObjectConst.APP_VERSION)) {
      swVersion = is.getObject(gIsObjectConst.APP_VERSION);
    }

    try {
      var appDownloadService = toi.downloadableAppManageService;
      var appId = appDownloadService.getAppInfoByName(DOWNLOADAPP_EPG_NAME).appId;
      var verInfo = appDownloadService.getAppVersion(appId)
      epgVersion = verInfo.installedVersion;
    }
    catch (e) {
      DumpLog("Failed to get epgVersion:" + e);
    }

    if (is.isObjectDefined(gIsObjectConst.LOADER_VERSION)) {
      loaderVersion = is.getObject(gIsObjectConst.LOADER_VERSION);
    }

    try {
      vbmClientVersion = vbl.GetVbmClientVersion();
    }
    catch (e) {
      DumpLog("Failed to get vbmClientVersion:" + e);
    }

    if (is.isObjectDefined(gIsObjectConst.UPGRADETIME_RECORDER)) {
      stbUpgradeTime = is.getObject(gIsObjectConst.UPGRADETIME_RECORDER) * 1;
    }

    if (is.isObjectDefined(gIsObjectConst.UI_PREFERRED_LANG)) {
      switch (is.getObject(gIsObjectConst.UI_PREFERRED_LANG)) {
      case 'eng':
        uiLanguage = 'English';
        break;
      case 'zho-tw':
      case 'zho-cn':
      default:
        uiLanguage = 'Chinese';
        break;
      }
    }

    if (is.isObjectDefined(gIsObjectConst.CONFIG_RATIO)) {
      var config = is.getObject(gIsObjectConst.CONFIG_RATIO);
      if (config == 'Auto') {
        ratio = 'Auto';
        dsiplay = 'FullScreen';
      }
      else {
        var components = config.split(' ');
        if (components.length == 2) {
          ratio = components[0];
          switch (components[1]) {
          case 'LB':
            dsiplay = 'LetterBox';
            break;
          case 'PB':
            dsiplay = 'PillarBox';
            break;
          case 'PS':
            dsiplay = 'PanScan';
            break;
          default:
            dsiplay = '';
            break;
          }
        }
      }
    }

    return vbl.Generic(vbl.AGENT_ID_REPORT, eventId,
                  hwVersion, swVersion, epgVersion, loaderVersion,
                  vbmClientVersion, stbUpgradeTime, uiLanguage, ratio, dsiplay);
  }

  this.Zapping = function(enterTime, exitTime, curService, lastService,
                          wayOfChange, isFromFavoriteMode, isAuthorized)
  {
    var eventId = 0;
    return vbl.Generic(vbl.AGENT_ID_ZAPPING, eventId, enterTime,
                  exitTime, curService, lastService, wayOfChange,
                  isFromFavoriteMode, isAuthorized);
  }

  var getCurrentServiceId = function(){
    var mediaSequences = toi.mediaService.enumeratePlayers();
    var serviceid = -1;

    if (mediaSequences.length > 0 ){
      var currentUri = mediaSequences[0].url.split(".");
      DumpLog("getCurrentServiceId: mediaSequences[0].url = "+currentUri);
      serviceid = currentUri[currentUri.length -1];
      DumpLog("getCurrentServiceId: serviceid 1 = "+serviceid);
    }

    if (serviceid == null || serviceid == "") {
		serviceid = -1;
    }

    return serviceid;
  }

  this.Error = function(owner, errorCode, errorDescription)
  {
    var eventId = 0;
    var srvId = getCurrentServiceId();
    DumpLog("Error: srvId = "+srvId);
    return vbl.Generic(vbl.AGENT_ID_LOG, eventId,
                  owner, srvId, errorCode, errorDescription);
  }

  this.AppEntrance = function(appName, appFrom, serviceUrl)
  {
    var eventId = 0;
    return vbl.Generic(vbl.AGENT_ID_APP, eventId,
                  appName, appFrom, serviceUrl);
  }

  this.Destroy = function()
  {
    vbl.Destroy();
  }
}

function ZappingReportHandler(vblInternal)
{
  this.ZAPPING_WAY_OTHER = vblInternal.ZAPPING_WAY_OTHER;
  this.ZAPPING_WAY_NUMBERKEY = vblInternal.ZAPPING_WAY_NUMBERKEY;
  this.ZAPPING_WAY_UP_OR_DOWN = vblInternal.ZAPPING_WAY_UP_OR_DOWN;
  this.ZAPPING_WAY_BANNER = vblInternal.ZAPPING_WAY_BANNER;
  this.ZAPPING_WAY_EPG = vblInternal.ZAPPING_WAY_EPG;
  this.ZAPPING_WAY_BACKKEY = vblInternal.ZAPPING_WAY_BACKKEY;
  this.ZAPPING_WAY_MAINMENU = vblInternal.ZAPPING_WAY_MAINMENU;
  this.ZAPPING_WAY_REMINDER = vblInternal.ZAPPING_WAY_REMINDER;
  this.ZAPPING_WAY_LIVETV = vblInternal.ZAPPING_WAY_LIVETV;
  this.ZAPPING_WAY_FROM_THIRDPARTY = vblInternal.ZAPPING_WAY_FROM_THIRDPARTY;

  this.ZAPPING_PROGRAM_ACTIVED_UNKNOWN = vblInternal.ZAPPING_PROGRAM_ACTIVED_UNKNOWN;
  this.ZAPPING_PROGRAM_ACTIVED_YES = vblInternal.ZAPPING_PROGRAM_ACTIVED_YES;
  this.ZAPPING_PROGRAM_ACTIVED_NO = vblInternal.ZAPPING_PROGRAM_ACTIVED_NO;

  var self = this;
  var is = toi.informationService;
  var wayOfZappingName = vblInternal.ZAPPING_WAY_INFORMATION_OBJECT;
  var srvIdName = gIsObjectConst.VBL_SERVICEID;
  var eventTimeStamp = 0;
  var exitTimeStamp = 0;
  var serviceId = 0;
  var lastServiceId = 0;
  var zappingWay = self.ZAPPING_WAY_OTHER;
  var favoriteListMode = false;
  var frontServiceLocked = false;
  var channelActived = self.ZAPPING_PROGRAM_ACTIVED_UNKNOWN;
  var caProgramAvailable = true;

  function resetParameters()
  {
    eventTimeStamp = 0;
    exitTimeStamp = 0;
    serviceId = 0;
    lastServiceId = 0;
    zappingWay = self.ZAPPING_WAY_OTHER;
    frontServiceLocked = false;
    channelActived = self.ZAPPING_PROGRAM_ACTIVED_UNKNOWN;
    caProgramAvailable = true;
  }

  function onFrontServiceLockStateChange(event)
  {
	  frontServiceLocked = event && event.info && event.info.isLocked;
    DumpLog("ZappingReportHandler::frontendService locked="
      + frontServiceLocked);
  }

  function onNovelServiceMessageTypeChange(event)
  {
    switch (event.messageType) {
		case toi.consts.TeiNovelService.MESSAGE_TYPE_BADCARD:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_EXPICARD:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_INSERTCARD:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_NOOPER:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_BLACKOUT:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_OUTWORKTIME:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_WATCHLEVEL:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_PAIRING:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_NOENTITLE:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_DECRYPTFAIL:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_NOMONEY:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_ERRREGION:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_NEEDFEED:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_ERRCARD:
/*
		case toi.consts.TeiNovelService.MESSAGE_TYPE_UPDATE:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_LOWCARDVER:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_VIEWLOCK:
*/
		case toi.consts.TeiNovelService.MESSAGE_TYPE_MAXRESTART:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_FREEZE:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_CALLBACK:
/*
		case toi.consts.TeiNovelService.MESSAGE_TYPE_CURTAIN:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_CARDTESTSTART:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_CARDTESTFAILD:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_CARDTESTSUCC:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_NOCALIBOPER:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_STBLOCKED:
		case toi.consts.TeiNovelService.MESSAGE_TYPE_STBFREEZE:
*/
      caProgramAvailable = false;
      break;

    case toi.consts.TeiNovelService.MESSAGE_TYPE_CANCEL:
		default:
      caProgramAvailable = true;
			break;
		}
    DumpLog("ZappingReportHandler::novelService event=" + event.messageType +
      ", programAvailable=" + caProgramAvailable);
  }

  this.setWayOfZapping = function(wayOfZapping)
  {
    if (wayOfZapping === self.ZAPPING_WAY_REMINDER ||
        wayOfZapping === self.ZAPPING_WAY_FROM_THIRDPARTY) {
      is.setObject(
        wayOfZappingName, wayOfZapping.toString(), is.STORAGE_VOLATILE);
      return;
    }

    zappingWay = wayOfZapping;
    if (zappingWay === self.ZAPPING_WAY_LIVETV) {
      if (is.isObjectDefined(wayOfZappingName)) {
        var way = is.getObject(wayOfZappingName) * 1;
        if (way === self.ZAPPING_WAY_REMINDER ||
            way === self.ZAPPING_WAY_FROM_THIRDPARTY) {
          zappingWay = way;
          is.setObject(
            wayOfZappingName,
            self.ZAPPING_WAY_OTHER.toString(),
            is.STORAGE_VOLATILE);
        }
      }
    }
    DumpLog("ZappingReportHandler::zappingWay=" + zappingWay);
  };

  this.stampLastServiceExitTime = function()
  {
    var date = new Date();
    exitTimeStamp = date.getTime();
    DumpLog("ZappingReportHandler::stampLastServiceExitTime=" + exitTimeStamp);
  };

  this.setCurrentServiceId = function(id)
  {
    serviceId = id;
    DumpLog("ZappingReportHandler::setCurrentServiceId=" + serviceId);
  };

  this.stampCurrentServiceEnterTime = function()
  {
    var date = new Date();
    eventTimeStamp = date.getTime();
    DumpLog("ZappingReportHandler::stampCurrentServiceEnterTime=" + eventTimeStamp);
  };

  this.setFavoriteListModeFlag = function(flag)
  {
    if (favoriteListMode != flag) {
      favoriteListMode = flag;
      DumpLog("ZappingReportHandler::setFavoriteListModeFlag=" + favoriteListMode);
    }
  };

  this.setChannelActivedStatus = function(status)
  {
    channelActived = status;
    DumpLog("ZappingReportHandler::setChannelActivedStatus=" + channelActived);
  };

  this.sendReoprt = function()
  {
    var authorizedStatus = self.ZAPPING_PROGRAM_ACTIVED_NO;
    if (frontServiceLocked && caProgramAvailable) {
      switch (channelActived) { 
      case self.ZAPPING_PROGRAM_ACTIVED_YES:
      case self.ZAPPING_PROGRAM_ACTIVED_NO:
      case self.ZAPPING_PROGRAM_ACTIVED_UNKNOWN:
        authorizedStatus = channelActived;
        break;
      default:
        authorizedStatus = self.ZAPPING_PROGRAM_ACTIVED_UNKNOWN;
      }
    }

    if (is.isObjectDefined(srvIdName)) {
      lastServiceId = is.getObject(srvIdName) * 1;
    }
    else {
      lastServiceId = -1;
    }
    is.setObject(srvIdName, serviceId.toString(), is.STORAGE_VOLATILE);

    if (typeof(vblInternal.Zapping) != 'undefined') {
      vblInternal.Zapping(
        eventTimeStamp, exitTimeStamp, serviceId, lastServiceId, zappingWay,
        favoriteListMode ? 'Yes' : 'No',
        authorizedStatus);
    }
    resetParameters();
  };

  this.initEventListener = function()
  {
    toi.frontendService.addEventListener(
      toi.frontendService.ON_LOCK_STATE_CHANGED,
      onFrontServiceLockStateChange);
  	toi.novelService.addEventListener(
    	toi.consts.TeiNovelService.ON_SHOW_BUY_MESSAGE,
    	onNovelServiceMessageTypeChange);
    DumpLog("ZappingReportHandler::initEventListener");
  };

  this.deinitEventListener = function()
  {
    toi.frontendService.removeEventListener(
      toi.frontendService.ON_LOCK_STATE_CHANGED,
      onFrontServiceLockStateChange);
    toi.novelService.removeEventListener(
      toi.consts.TeiNovelService.ON_SHOW_BUY_MESSAGE,
      onNovelServiceMessageTypeChange);
    DumpLog("ZappingReportHandler::deinitEventListener");
  };
}

function getAppNameByAppId(appId)
{
  var appName = DOWNLOADAPP_APPNAME_UNKNOWN;
  if (appId !== null) {
    try {
      var appDownloadService = toi.downloadableAppManageService;
      var appInfo = appDownloadService.getAppInfoById(appId);
      appName = appInfo.appName;
    }
    catch (e) {
      DumpLog("getAppNameByAppId err:" + e);
      try {
        if (appId != DOWNLOADAPP_MAINMENU_ID) {
          var is = toi.informationService;
          if (is.isObjectDefined(gIsObjectConst.DOWNLOADAPP_APP_LIST)) {
            var appListString = is.getObject(gIsObjectConst.DOWNLOADAPP_APP_LIST);
            if (appListString != null && appListString != "") {
              var appList = eval(appListString);
              for (var i in appList) {
                if (appList[i].appId === appId) {
                  appName = appList[i].appName;
                  break;
                }
              }
            }
          }
        }
        else {
          appName = DOWNLOADAPP_APPNAME_MAINMENU;
        }
      }
      catch (e) {
        DumpLog("getAppNameByAppId err:" + e);
      }
    }
  }
  return appName;
}

function getAppNameByLaunchName(launchName)
{
  var appName = DOWNLOADAPP_APPNAME_UNKNOWN;
  if (launchName != null) {
    try {
      var appDownloadService = toi.downloadableAppManageService;
      var appInfo = appDownloadService.getAppInfoByName(launchName);
      appName = appInfo.appName;
    }
    catch (e) {
      DumpLog("getAppNameByLaunchName err:" + e);
      var appId = null;
      switch (launchName) {
      case DOWNLOADAPP_MAINMENU_NAME:
        appId = DOWNLOADAPP_MAINMENU_ID;
        break;
      case DOWNLOADAPP_LIVETV_NAME:
        appId = DOWNLOADAPP_LIVETV_ID;
        break;
      case DOWNLOADAPP_EPG_NAME:
        appId = DOWNLOADAPP_EPG_ID;
        break;
      case DOWNLOADAPP_SETTING_NAME:
        appId = DOWNLOADAPP_SETTING_ID;
        break;
      case DOWNLOADAPP_SYSTEM_SETTING_NAME:
        appId = DOWNLOADAPP_SYSTEM_SETTING_ID;
        break;
      case DOWNLOADAPP_MAILBOX_NAME:
        appId = DOWNLOADAPP_MAILBOX_ID;
        break;
      default:
        break;
      }

      if (appId != null) {
        appName = getAppNameByAppId(appId);
      }
    }
  }
  return appName;
}

