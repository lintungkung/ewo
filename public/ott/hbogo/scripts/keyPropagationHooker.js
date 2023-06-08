/*            Copyright 2013 ARRIS Group, Inc. All rights reserved.
 *
 * This program is confidential and proprietary to ARRIS Group, Inc. (ARRIS),
 * and may not be copied, reproduced, modified, disclosed to others,
 * published or used, in whole or in part, without the express prior written
 * permission of ARRIS.
 *
 */

function redirectToUrl(url, appName)
{
  DumpLog("desire to swicth current app goto: " + url);

  if (url === undefined)
    return;

  if (appName === undefined) {
    appName = DOWNLOADAPP_APPNAME_UNKNOWN;
  }
  toi.informationService.setObject(gIsObjectConst.APP_SWITCH_NAME, appName,
      toi.consts.ToiInformationService.STORAGE_VOLATILE);

  toi.informationService.setObject(gIsObjectConst.APP_SWITCH_URL, url,
      toi.consts.ToiInformationService.STORAGE_VOLATILE);
}

function redirectToUrlInternal(url) {
  DumpLog("desire to swicth current app goto: " + url);

  if (url === undefined)
    return;
  window.location.assign(url);
}

