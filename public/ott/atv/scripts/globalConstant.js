/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

var globalConst = {
  PORTAL_URL : "../tvportal/index.html",
  LIVETV_URL:"../livetv/index.html",
  EPG_URL : "../epg/index.html",
  SETTING_URL : "../setting/index.html",
  SYSTEM_SETUP_URL : "../systemsetting/index.html",
  MAILBOX_URL : "../mailbox/index.html",
  FTI_SETTING_NETWORKSCAN_URL : "../background/fti/FTI_networkscan.html"
};

var gIsObjectConst = {
  APP_SWITCH_URL: "cfg.custom.appswitch.url",
  APP_SWITCH_NAME: "cfg.custom.appswitch.appname",
  LAST_CHN_IDX: "cfg.custom.lastchn",
  BARKER_URL: "cfg.custom.barker.url",
  BARKER_ID: "cfg.custom.barker.id",
  PARENTAL_RATING: "cfg.custom.parentalrating",
  PARENTAL_PIN: "cfg.custom.pin.parental",
  PARENTAL_TIME: "cfg.custom.parentaltime",
  PURCHASE_PIN: "cfg.custom.pin.purchase",
  ENGINEER_PIN: "cfg.custom.pin.engineer",
  CONFIG_RATIO: "cfg.custom.aspectratio",
  CONFIG_RESOLUTION: "cfg.custom.resolution",
  CONFIG_AUDIOLANG: "cfg.media.audio.languagepriority", //TOI defined
  CONFIG_DOLBY: "cfg.custom.dolby",
  UI_PREFERRED_LANG: "cfg.custom.ui.language",
  PLATFORM_UI_PREFERRED_LANG: "cfg.locale.ui",//TOI defined
  MSG_AUTOFADE_TIME: "cfg.custom.ui.msgfadetime",
  FAVORITE_CHANNELS: "cfg.custom.channels.favorite",
  LOCKED_CHANNELS: "cfg.custom.channes.locked",
  FACTORY_RESET_FLAG: "cfg.custom.factoryreset.flag",
  REMINDER_VERSION: "cfg.custom.reminder.version",
  REMINDER_LIST: "cfg.custom.reminderlist",
  ADD_REMINDER: "cfg.custom.reminder.add", // volatile
  REMOVE_REMINDER: "cfg.custom.reminder.remove", // volatile
  IS_OTA_UPGRADING: "cfg.custom.otaupgrade.flag",  
  IS_FORCE_SCAN: "cfg.custom.forcescan.flag",    
  BASIC_TRANSPONDER_FREQ: "cfg.custom.basictransponder.freq",  
  BASIC_TRANSPONDER_QAM: "cfg.custom.basictransponder.qam",    
  BASIC_TRANSPONDER_SYM: "cfg.custom.basictransponder.sym",  
  BASIC_TRANSPONDER_NETWORKID: "cfg.custom.basictransponder.networkid",    
  SPECIAL_KEY_FLAG: "cfg.custom.specialkey.code",      
  STBID: "rawflashdata.stb.id",
  MMC_CLOSE_MMU: "cfg.custom.mmc.close",  //for prompt menu close
  APP_VERSION:"rawflashdata.app.ver", //TOI defined
  LOADER_VERSION:"rawflashdata.loader.ver", //TOI defined
  HIGHEST_PRIOR_BOUQUET_ID: "cfg.custom.bouquetId",
  FTI_FLAG: "cfg.custom.fticompleted.flag",
  FTI_ACTIVED_BOUQUET_ID: "cfg.custom.fti.bid",
  FTI_SECTION1_COMPLETED: "cfg.custom.ftisection.completed",
  NAC_FLAG: "rawflashdata.newappcheck.flag", //NAC
  SFC_FLAG: "rawflashdata.startupfailed.count",//SFC
  HARD_VERSION: "rawflashdata.hw.ver", //TOI defined
  NITSCAN_MODE: "cfg.dvb.scan.mode",
  NITSCAN_NETWORKID: "cfg.dvb.scan.networkid",
  SMS_SERVER_URL: "cfg.sms.server.url",
  SMS_SERVER_TIMEOUT: "cfg.sms.server.timeout",
  SMS_APP_ZIPCODE: "cfg.custom.sms.zipcode",
  CA_OPERATOR_ID: "cfg.custom.ca.operator.id",
  ANTI_PIRATE_ROLE:"var.antipirate.role",
  ANTI_PIRATE_STATUS:"var.antipirate.status",
  ANTI_PIRATE_MASTERCARD_NO:"var.antipirate.master.cardnum",  
  IS_SCRAMBLE_STREAM:"cfg.stb.descrambleStatus",  
  NETWORKSCAN_NETWORKID: "cfg.custom.networkscan.networkid",
  STANDBY_MODE:"rawflashdata.standby.flag",
  NETWORKSCAN_SYMBOLRATE: "cfg.custom.networkscan.symbolrate",
  NETWORKSCAN_QAM: "cfg.custom.networkscan.qam",
  EPG_VIDEOWINDOW_ON: "cfg.custom.epg.videowindow.on", // volatile
  MMU_MAINMENU_ON: "cfg.custom.mainmenu.on", // volatile
  SYSTEM_STARTUP_TIME_SECONDS: "var.uptime.seconds",
  IS_LOCK_SERVICE: "cfg.custom.lockservice.flag",
  CURRENT_APP_INDEX: "cfg.custom.currentapp.index", // volatile
  AUTO_REBOOT_FLAG: "cfg.auto.reboot.flag",
  AUTO_REBOOT_STANDBY_INTERVAL: "cfg.auto.reboot.interval",
  AUTO_REBOOT_MIN_TIME: "cfg.auto.reboot.min.time",
  AUTO_REBOOT_MAX_TIME: "cfg.auto.reboot.max.time",
  DOWNLOADAPP_APP_LIST:"var.custom.app.list",
  MMC_VISIBLE : "cfg.custom.mmc.visible",
  VBL_SERVICEID: "cfg.custom.vbl.serviceid",
  VBL_ZAPPINGWAY: "cfg.custom.vbl.wayofzapping"
};
var DOWNLOADAPP_APPNAME_UNKNOWN = "Unknown";
var DOWNLOADAPP_APPNAME_FTI = "FTI";
var DOWNLOADAPP_APPNAME_MAINMENU = "TV Portal";

var DOWNLOADAPP_MAINMENU_ID = 100000;
var DOWNLOADAPP_MAINMENU_NAME = "tvportal";
var DOWNLOADAPP_LIVETV_ID = 100001;
var DOWNLOADAPP_LIVETV_NAME = "livetv";
var DOWNLOADAPP_EPG_ID = 100002;
var DOWNLOADAPP_EPG_NAME = "epg";
var DOWNLOADAPP_SETTING_ID = 100003;
var DOWNLOADAPP_SETTING_NAME = "setting";
var DOWNLOADAPP_SYSTEM_SETTING_ID = 100004;
var DOWNLOADAPP_SYSTEM_SETTING_NAME = "systemsetting";
var DOWNLOADAPP_MAILBOX_ID = 100005;
var DOWNLOADAPP_MAILBOX_NAME = "mailbox";

var DEFAULT_BOUQUET_ID = 25149;
