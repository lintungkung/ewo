function switchToThirdApp(a, b, c) {
    cns.AppManage.go(c)
}
function jsEvent(a) {
    try {
        console.log("[jsEvent] androidEvent =" + a);
        var b = JSON.parse(a);
        cns.Event.eventCallBack(b)
    } catch (a) {
        console.log("[jsEvent] parse error " + a.message)
    }
}
function onKeyOk(a) {
    var b = document.getElementById("app") ? document.getElementById("app").contentWindow : null;
    console.log("[main] onKeyOk start location.href = " + a),
    b && (console.log("[main] onKeyBack start location.href = " + location.href),
    b.OnKeyOK())
}
function onKeyBack() {
    var a = document.getElementById("app") ? document.getElementById("app").contentWindow : null
      , b = window.localStorage.getItem("backUrl");
    if (console.log("[main] onKeyBack start backUrl=" + b),
    a)
        console.log("[main] onKeyBack start location.href = " + location.href),
        a.OnKeyBack();
    else {
        var c = document.createEvent("HTMLEvents");
        c.initEvent("keydown", !0, !0),
        c.keyCode = 8,
        document.dispatchEvent(c),
        console.log("callJS edollar end***********")
    }
}
!function() {
    function a() {
        console.log.apply(console, arguments)
    }
    var b = {}
      , c = {};
    b.Event = {
        _eventHandles: {},
        _eventHandlesCount: 0,
        _listenerArray: [],
        _cb: null,
        _listenerType: "system",
        eventCallBack: function(a) {
            var b = "";
            switch (a.type) {
            case 1:
                b = "media";
                break;
            case 2:
                b = "ca";
                break;
            case 3:
                b = "netWork";
                break;
            case 4:
                b = "channel";
                break;
            case 5:
                b = "tuner"
            }
            this._dispatchEvent(b, a.code, a.data)
        },
        _dispatchEvent: function(b, c, d) {
            a("[cnsEvent] dispatch type=" + b + ",code=" + c + ",result=" + d);
            var e = Array.prototype.slice.apply(arguments);
            if (3 != e.length)
                throw "[cnsEvent]Invalid Arguments, when distributing event!";
            var f = this._eventHandles[e[0]];
            if (void 0 !== f && f.length > 0)
                for (var g = 0, h = f.length; g < h; g++)
                    try {
                        f[g][0].call(f[g][1], e[1], e[2] ? JSON.stringify(e[2]) : null)
                    } catch (b) {
                        a("[cnsEvent]One of the eventType of '" + e[0] + "' is invalid: " + b.message)
                    }
            else
                a("[cnsEvent]The eventCode '" + e[1] + "' of eventType '" + e[0] + "' has not been registered.");
            var i = 1;
            switch (b) {
            case "media":
                i = 1;
                break;
            case "ca":
                i = 2;
                break;
            case "netWork":
                i = 3;
                break;
            case "channel":
                i = 4;
                break;
            case "tuner":
                i = 5
            }
            var j = document.getElementById("app").contentWindow;
            if (console.log("[cns.Event] _cb is" + this._cb),
            this._cb && j && j[this._cb] && j[this._cb].call(this, i, c, d ? JSON.stringify(d) : null),
            console.log("[cns.Event] _cb over"),
            console.log("[cns.Event] this._listenerArray is " + this._listenerArray.length),
            this._listenerArray.length > 0)
                for (var k = 0, l = this._listenerArray.length; k < l; k++)
                    try {
                        console.log("[cns.Event] dispatchEvent _listenerArray i is" + k),
                        this._listenerArray[k].call(this, i, c, d ? JSON.stringify(d) : "{}")
                    } catch (a) {
                        this._listenerArray.splice(k, 1),
                        console.log("[cns.Event] error is " + a.message)
                    }
        },
        listener: function(a) {
            console.log("[cns.Event] listener start"),
            this._listenerArray = [],
            this._listenerArray.push(a)
        },
        addListener: function(b) {
            var c = b.callback
              , d = b.eventType;
            a("[cnsEvent] event addListener type is" + d),
            "stb" != d ? (this._listenerType = "system",
            this._isFu(c),
            this._eventHandles[d] || (this._eventHandles[d] = []),
            this._eventHandles[d].push([b.callback, b.context]),
            this._eventHandlesCount++,
            a("[cnsEvent] eventCount =" + this._eventHandlesCount)) : (this._listenerType = "stb",
            this._cb = c)
        },
        removeEventListener: function(b, c) {
            if (a("_eventType is" + b),
            !(b in this._eventHandles))
                throw "[cnsEvent]Remove listener failed, because of invalid eventType";
            var d = this._eventHandles[b];
            if (void 0 !== d)
                for (var e = 0, f = d.length; e < f; e++)
                    if (c.toString().replace(/\s+/g, "") == d[e][0].toString().replace(/\s+/g, "")) {
                        this._eventHandles[b].splice(e, 1),
                        this._eventHandlesCount--;
                        break
                    }
            a("[cnsEvent]The listener of '" + b + "' has been removed.," + this._eventHandlesCount)
        },
        _isFu: function(a) {
            if ("function" != typeof a)
                throw "[cnsEvent]Function required as arguments!"
        }
    },
    Object.defineProperties(b.Event, {
        TYPE_MEDIAPLAYER: {
            get: function() {
                return "TYPE_MEDIAPLAYER"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_START_OK: {
            get: function() {
                return "PLAYER_START_OK"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_START_ERROR: {
            get: function() {
                return "PLAYER_START_ERROR"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_FINISH: {
            get: function() {
                return "PLAYER_FINISH"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_ERROR: {
            get: function() {
                return "PLAYER_PLAY_ERROR"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_BUFFERING_START: {
            get: function() {
                return "PLAYER_BUFFERING_START"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_BUFFERING_PROGRESS: {
            get: function() {
                return "PLAYER_BUFFERING_PROGRESS"
            },
            enumerable: !0,
            configurable: !1
        },
        MEDIAPLAYER_BUFFERING_END: {
            get: function() {
                return "PLAYER_BUFFERING_END"
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_NETWORK: {
            get: function() {
                return "TYPE_NETWORK"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_CONNECT_SUCCESS: {
            get: function() {
                return "NETWORK_CONNECT_SUCCESS"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_CONNECT_FAIL: {
            get: function() {
                return "NETWORK_CONNECT_FAIL"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_CONNECT_PLUGIN: {
            get: function() {
                return "NETWORK_CONNECT_PLUGIN"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_CONNECT_PLUGOUT: {
            get: function() {
                return "NETWORK_CONNECT_PLUGOUT"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_PING_SUCCESS: {
            get: function() {
                return "NETWORK_PING_SUCCESS"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_PING_FAIL: {
            get: function() {
                return "NETWORK_PING_FAIL"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_SCAN_AP_SUCCESS: {
            get: function() {
                return "NETWORK_SCAN_AP_SUCCESS"
            },
            enumerable: !0,
            configurable: !1
        },
        NETWORK_DATA_LINK_INVALID: {
            get: function() {
                return "NETWORK_DATA_LINK_INVALID"
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_TUNER: {
            get: function() {
                return "TYPE_TUNER"
            },
            enumerable: !0,
            configurable: !1
        },
        TUNER_LOCKED: {
            get: function() {
                return "DVB_TUNER_LOCKED"
            },
            enumerable: !0,
            configurable: !1
        },
        TUNER_UNLOCKED: {
            get: function() {
                return "DVB_TUNER_UNLOCK"
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_CA: {
            get: function() {
                return "TYPE_CA"
            },
            enumerable: !0,
            configurable: !1
        },
        CA_CARD_PLUGOUT: {
            get: function() {
                return "CA_CARD_PLUGOUT"
            },
            enumerable: !0,
            configurable: !1
        },
        CA_CARD_PLUGIN: {
            get: function() {
                return "CA_CARD_PLUGIN"
            },
            enumerable: !0,
            configurable: !1
        },
        CA_BUYMESSGE_DISPLAY: {
            get: function() {
                return "CA_BUYMESSGE_DISPLAY"
            },
            enumerable: !0,
            configurable: !1
        },
        CA_BUYMESSGE_HIDE: {
            get: function() {
                return "CA_BUYMESSGE_HIDE"
            },
            enumerable: !0,
            configurable: !1
        },
        CHANNEL_NAME_CHANGED: {
            get: function() {
                return "DVB_PROG_NAME_CHANGE"
            },
            enumerable: !0,
            configurable: !1
        },
        CHANNEL_PMTPID_CHANGED: {
            get: function() {
                return "DVB_PMTPID_CHANGE"
            },
            enumerable: !0,
            configurable: !1
        },
        CHANNEL_LIST_CHANGE: {
            get: function() {
                return "CHANNEL_LIST_CHANGE"
            },
            enumerable: !0,
            configurable: !1
        },
        CHANNEL_GROUP_CHANGE: {
            get: function() {
                return "DVB_PROG_GROUP_CHANGE"
            },
            enumerable: !0,
            configurable: !1
        }
    });// jEvt.setEventCallback("jsEvent");

    var d = function(c) {
        this.__handle = c,
        this.__playStatus = "connecting",
        this.__playUrl = "",
        this.__callBack = function(c, d) {
            switch (a("[cnsMediaPlayer] listener callBack event is" + c),
            c) {
            case b.Event.MEDIAPLAYER_START_OK:
                this.__playStatus = "play";
                break;
            case b.Event.MEDIAPLAYER_FINISH:
                this.__playStatus = "finished";
                break;
            case b.Event.MEDIAPLAYER_BUFFERING_START:
                this.__playStatus = "buffering";
                break;
            case b.Event.MEDIAPLAYER_ERROR:
                this.__playStatus = "playError"
            }
        }
        ,
        b.Event.addListener({
            type: "system",
            eventType: "media",
            callback: this.__callBack,
            context: this
        })
    };
    d.prototype = {
        addListener: function(a) {
            "function" == typeof a && b.Event.addListener({
                type: "system",
                eventType: "media",
                callback: a,
                context: this
            })
        },
        removeListener: function(a) {
            b.Event.removeEventListener("media", a)
        },
        isRunning: function() {
            return this.handle >= 0
        },
        play: function() {
            var c = {
                type: "",
                urls: []
            };
            if (this.isRunning() && "string" == typeof arguments[0]) {
                var d, e, f = arguments[0];
                (d = /^\/.+$/.exec(f)) ? e = "file" : (d = /^file:\/\/(\/.+)$/.exec(f)) ? (e = "file",
                f = d[1]) : (d = /^(https?):\/\/(.+)$/.exec(f)) ? (e = d[1],
                f = d[2]) : (d = /^udp:\/\/.+$/.exec(f)) ? (e = "udp",
                f = f.split("://")[1]) : (d = /^rtsp:\/\/.+$/.exec(f)) ? (e = "rtsp",
                f = f.split("://")[1]) : (d = /^rtmp:\/\/.+$/.exec(f)) ? (e = "rtmp",
                f = f.split("://")[1]) : f = null,
                f && (c.type = e,
                c.urls.push({
                    url: f
                }))
            } else if (this.isRunning() && arguments[0]instanceof b.Channel) {
                c.type = "dvb";
                for (var g = 0; g < 3 && g < arguments.length; g++)
                    c.urls.push({
                        frequency: arguments[g].frequency,
                        symbolRate: arguments[g].symbolRate,
                        modulation: arguments[g].modulation,
                        serviceId: arguments[g].serviceId,
                        tsId: arguments[g].tsId,
                        networkId: arguments[g].networkId,
                        audioPID: arguments[g].audioPID,
                        videoPID: arguments[g].videoPID,
                        videoDecodeType: arguments[g].videoDecodeType,
                        audioDecodeType: arguments[g].audioDecodeType,
                        pcrPID: arguments[g].PCRPID,
                        programType: arguments[g].type
                    })
            }
            if (a("[cnsMediaPlayer] currentPlayer playUrl=" + JSON.stringify(c)),
            c.type && c.urls.length > 0) {
                if (jPlayer.setMediaSource(JSON.stringify(c)))
                    return 0;
                this.__playUrl = c.urls[0].url;
                var h = jPlayer.play();
                return this.__playStatus = h ? "playError" : "play",
                !h
            }
            return 0
        },
        stop: function() {
            if (this.isRunning()) {
                var a = !jPlayer.stop();
                return a && (this.__playStatus = "stop"),
                a
            }
            return 0
        },
        pause: function() {
            if (console.log("[main] pause"),
            this.isRunning()) {
                var a = !jPlayer.pause();
                return a && (this.__playStatus = "pause"),
                a
            }
            return 0
        },
        resume: function() {
            if (this.isRunning()) {
                console.log("[main] resume");
                var a = !jPlayer.resume();
                return a && (this.__playStatus = "play"),
                a
            }
            return 0
        },
        forward: function(a) {
            if (this.isRunning() && "number" == typeof a) {
                var b = Math.abs(a);
                if (0 !== a && b % 2 == 0 && b <= 32) {
                    var c = !jPlayer.forward(a);
                    return c && (this.__playStatus = "forward"),
                    c
                }
                if (1 === a)
                    return this.resume()
            }
            return 0
        },
        backward: function(a) {
            if (this.isRunning() && "number" == typeof a) {
                var b = Math.abs(a);
                if (0 !== a && b % 2 == 0 && b <= 32) {
                    var c = !jPlayer.backward(a);
                    return c && (this.__playStatus = "backward"),
                    c
                }
                if (1 === a)
                    return this.resume()
            }
            return 0
        },
        seek: function(b) {
            if (this.isRunning() && "number" == typeof b) {
                if (b = Math.min(Math.max(b, 0)),
                a("[cnsMediaPlayer] seek time=" + b + ",duration=" + this.duration),
                b <= this.duration) {
                    var c = !jPlayer.seek(b);
                    return c && (this.__playStatus = "seek"),
                    c
                }
                return 0
            }
            return 0
        },
        clearFrame: function() {
            return this.isRunning() ? !jPlayer.clearFrame() : 0
        },
        getCurrentPlayTime: function() {
            return this.isRunning() ? jPlayer.getCurrentPlayTime() : 0
        },
        getMute: function() {
            return this.isRunning() ? jPlayer.getMute() : 0
        },
        setMute: function(a) {
            return this.isRunning() && "boolean" == typeof a ? !jPlayer.setMute(a) : 0
        },
        getSoundTrack: function() {
            if (!this.isRunning())
                return a("[cnsMediaPlayer] getSoundTrack error"),
                b.MediaPlayer.SOUNDTRACK_LEFT;
            var c = jPlayer.getSoundTrack();
            switch (console.log("getSoundTrack is " + c),
            c) {
            case 1:
                return b.MediaPlayer.SOUNDTRACK_STEREO;
            case 2:
                return b.MediaPlayer.SOUNDTRACK_LEFT;
            case 3:
                return b.MediaPlayer.SOUNDTRACK_RIGHT;
            case 4:
                return b.MediaPlayer.SOUNDTRACK_MIX;
            default:
                return ""
            }
        },
        setSoundTrack: function(a) {
            if (this.isRunning() && "string" == typeof a) {
                var c = 0;
                switch (a) {
                case b.MediaPlayer.SOUNDTRACK_STEREO:
                    c = 1;
                    break;
                case b.MediaPlayer.SOUNDTRACK_LEFT:
                    c = 2;
                    break;
                case b.MediaPlayer.SOUNDTRACK_RIGHT:
                    c = 3;
                    break;
                case b.MediaPlayer.SOUNDTRACK_MIX:
                    c = 4
                }
                return !jPlayer.setSoundTrack(c)
            }
            return 0
        },
        getAudioType: function() {
            return this.isRunning() ? jPlayer.getAudioType() : ""
        },
        setAudioType: function(a) {
            return this.isRunning() && "string" == typeof a ? !jPlayer.setAudioType(a) : 0
        },
        getVolume: function() {
            return this.isRunning() ? jPlayer.getVolume() : 0
        },
        setVolume: function(a) {
            if (this.isRunning() && "number" == typeof a) {
                var b = parseInt(a);
                return b >= 0 && b <= 100 ? !jPlayer.setVolume(b) : 0
            }
            return 0
        },
        getSubtitle: function() {
            return !!this.isRunning() && jPlayer.getSubtitle()
        },
        setSubtitle: function(a) {
            return this.isRunning() && "boolean" == typeof a ? !jPlayer.setSubtitle(a) : 0
        },
        getPosition: function() {
            if (this.isRunning()) {
                var a = jPlayer.getVideoDisplayArea();
                try {
                    return JSON.parse(a)
                } catch (a) {
                    return {
                        x: -1,
                        y: -1,
                        z: -1,
                        width: -1,
                        height: -1
                    }
                }
            }
            return {
                x: -1,
                y: -1,
                z: -1,
                width: -1,
                height: -1
            }
        },
        setPosition: function(a, b, c, d, e) {
            var f = {
                x: a,
                y: b,
                z: c,
                width: d,
                height: e
            };
            return !jPlayer.setVideoDisplayArea(JSON.stringify(f))
        },
        getFullScreen: function() {
            return this.isRunning() ? jPlayer.getFullScreen() : (a("[cnsMediaPlayer] getFullScreen error"),
            !0)
        },
        setFullScreen: function() {
            return this.isRunning() ? !jPlayer.setFullScreen() : 0
        },
        getFrameMode: function() {
            return this.isRunning() ? jPlayer.getFrameMode() : (a("[cnsMediaPlayer] getFrameMode error"),
            !1)
        },
        setFrameMode: function(a) {
            return this.isRunning() && "boolean" == typeof a ? !jPlayer.setFrameMode(a) : 0
        },
        getAspectRatio: function() {
            if (this.isRunning()) {
                switch (jPlayer.getAspectRatio()) {
                case 1:
                    return b.MediaPlayer.ASPECTRATIO_4_3;
                case 2:
                    return b.MediaPlayer.ASPECTRATIO_16_9;
                case 3:
                    return b.MediaPlayer.ASPECTRATIO_AUTO;
                case 4:
                    return b.MediaPlayer.ASPECTRATIO_SQUARE;
                default:
                    return ""
                }
            }
            return a("[cnsMediaPlayer] getAspectRatio error"),
            ""
        },
        setAspectRatio: function(a) {
            if (this.isRunning() && "string" == typeof a) {
                var c = 0;
                switch (a) {
                case b.MediaPlayer.ASPECTRATIO_4_3:
                    c = 1;
                    break;
                case b.MediaPlayer.ASPECTRATIO_16_9:
                    c = 2;
                    break;
                case b.MediaPlayer.ASPECTRATIO_AUTO:
                    c = 3;
                    break;
                case b.MediaPlayer.ASPECTRATIO_SQUARE:
                    c = 4
                }
                return !jPlayer.setAspectRatio(c)
            }
            return 0
        },
        getAspectMatch: function() {
            if (this.isRunning()) {
                switch (jPlayer.getAspectMatch()) {
                case 1:
                    return b.MediaPlayer.ASPECTMATCH_AUTO;
                case 2:
                    return b.MediaPlayer.ASPECTMATCH_LETTERBOX;
                case 3:
                    return b.MediaPlayer.ASPECTMATCH_PANSCAN;
                case 4:
                    return b.MediaPlayer.ASPECTMATCH_COMBINED;
                default:
                    return ""
                }
            }
            return a("[cnsMediaPlayer] getAspectMatch error"),
            ""
        },
        setAspectMatch: function(a) {
            if (this.isRunning()) {
                var c = 0;
                switch (a) {
                case b.MediaPlayer.ASPECTMATCH_AUTO:
                    c = 1;
                    break;
                case b.MediaPlayer.ASPECTMATCH_LETTERBOX:
                    c = 2;
                    break;
                case b.MediaPlayer.ASPECTMATCH_PANSCAN:
                    c = 3;
                    break;
                case b.MediaPlayer.ASPECTMATCH_COMBINED:
                    c = 4
                }
                return !jPlayer.setAspectMatch(c)
            }
            return 0
        },
        getAudioPid: function() {
            if (this.isRunning()) {
                var a = jPlayer.getAudioPid();
                if (a)
                    try {
                        return JSON.parse(a)
                    } catch (a) {
                        return []
                    }
            }
            return []
        },
        setAudioPid: function(a, b) {
            return this.isRunning() && "string" == typeof a && "number" == typeof b ? !jPlayer.setAudioPid(a, b) : 0
        },
        release: function() {
            var c = Object.keys(e).length
              , d = 1;
            return 1 == c && (delete e[this.handle],
            d = jPlayer.release()),
            c > 1 && e.hasOwnProperty(this.handle) && (delete e[this.handle],
            a("[cnsMediaPlayer] __cachMedia =" + JSON.stringify(e)),
            d = 0),
            d || (this.handle = -1,
            this.__playStatus = "connecting",
            this.__playUrl = ""),
            b.Event.removeEventListener("media", this.__callBack),
            d
        }
    },
    Object.defineProperties(d.prototype, {
        handle: {
            get: function() {
                return this.__handle
            },
            enumerable: !0,
            configurable: !1
        },
        duration: {
            get: function() {
                return jPlayer.getMediaDuration()
            },
            enumerable: !0,
            configurable: !1
        },
        playStatus: {
            get: function() {
                return this.__playStatus
            },
            enumerable: !0,
            configurable: !1
        },
        playUrl: {
            get: function() {
                return this.__playUrl
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.MediaPlayerManage = {
        getMediaPlayers: function() {
            var a = [];
            for (var b in e)
                a.push(e[b]);
            return a
        }
    };
    var e = {};
    b.MediaPlayer = {
        __handles: 0,
        create: function(b) {
            var c = -1
              , f = null;
            return 0 == Object.keys(e).length ? (c = jPlayer.create(),
            console.log("[main] cns.MediaPlayer create")) : c = 0,
            a("[cns.MediaPlayer] create jPlayer rtn =" + c),
            c || (f = new d(++this.__handles),
            e[this.__handles] = f),
            a("[cns.MediaPlayer] create __cachMedia =" + JSON.stringify(e)),
            f
        },
        bind: function(b) {
            return a("[cns.MediaPlayer] __cachMedia =" + JSON.stringify(e) + "current handle=" + b),
            "string" == typeof b && "" !== b && (b = Number(b)),
            "number" == typeof b && b > 0 && e[b] && e[b].isRunning() ? e[b] : null
        }
    },
    Object.defineProperties(b.MediaPlayer, {
        SOUNDTRACK_STEREO: {
            get: function() {
                return "stereo"
            },
            enumerable: !0,
            configurable: !1
        },
        SOUNDTRACK_LEFT: {
            get: function() {
                return "left"
            },
            enumerable: !0,
            configurable: !1
        },
        SOUNDTRACK_RIGHT: {
            get: function() {
                return "right"
            },
            enumerable: !0,
            configurable: !1
        },
        SOUNDTRACK_MIX: {
            get: function() {
                return "mix"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTRATIO_4_3: {
            get: function() {
                return "4/3"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTRATIO_16_9: {
            get: function() {
                return "16/9"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTRATIO_AUTO: {
            get: function() {
                return "auto"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTRATIO_SQUARE: {
            get: function() {
                return "square"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTMATCH_AUTO: {
            get: function() {
                return "auto"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTMATCH_LETTERBOX: {
            get: function() {
                return "letterbox"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTMATCH_PANSCAN: {
            get: function() {
                return "panscan"
            },
            enumerable: !0,
            configurable: !1
        },
        ASPECTMATCH_COMBINED: {
            get: function() {
                return "combined"
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.Program = function(a) {
        this.__programs = a
    }
    ,
    Object.defineProperties(b.Program.prototype, {
        name: {
            get: function() {
                return this.__programs ? this.__programs.name : ""
            },
            enumerable: !0,
            configurable: !1
        },
        startTime: {
            get: function() {
                return this.__programs ? new Date(this.__programs.startTime.replace(/-/g, "/")) : ""
            },
            enumerable: !0,
            configurable: !1
        },
        endTime: {
            get: function() {
                return this.__programs ? new Date(this.__programs.endTime.replace(/-/g, "/")) : ""
            },
            enumerable: !0,
            configurable: !1
        },
        duration: {
            get: function() {
                return this.__programs ? this.__programs.duration : ""
            },
            enumerable: !0,
            configurable: !1
        },
        description: {
            get: function() {
                return this.__programs ? this.__programs.description : ""
            },
            enumerable: !0,
            configurable: !1
        },
        parentRating: {
            get: function() {
                return this.__programs ? this.__programs.parentRating : ""
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.Channel = function(a) {
        this.__channel = a
    }
    ,
    b.Channel.prototype = {
        getPF: function() {
            try {
                for (var c = [], d = JSON.parse(jChannelManager.getPF(this.__channel.networkId, this.__channel.tsId, this.__channel.serviceId)), e = 0; e < d.length; e++)
                    c.push(new b.Program(d[e]));
                return c
            } catch (b) {
                return a("[cns.Channel] getPF error " + b.message),
                [null, null]
            }
        },
        getLock: function() {
            return !(!this.__channel || !this.__channel.isLock)
        },
        getFav: function() {
            return !(!this.__channel || !this.__channel.isFav)
        },
        getAudioPids: function() {
            if (this.__channel)
                try {
                    return JSON.parse(jChannelManager.getAudioPid(this.__channel.networkId, this.__channel.tsId, this.__channel.serviceId))
                } catch (b) {
                    return a("[cns.Channel] getAudioPids error " + b.message),
                    []
                }
            return []
        },
        getSoundTrack: function() {
            if (!this.__channel)
                return "";
            switch (jChannelManager.getSoundTrack(this.__channel.networkId, this.__channel.tsId, this.__channel.serviceId)) {
            case 1:
                return b.MediaPlayer.SOUNDTRACK_STEREO;
            case 2:
                return b.MediaPlayer.SOUNDTRACK_LEFT;
            case 3:
                return b.MediaPlayer.SOUNDTRACK_RIGHT;
            case 4:
                return b.MediaPlayer.SOUNDTRACK_MIX;
            default:
                return ""
            }
        },
        setSoundTrack: function(a) {
            if (this.__channel && "string" == typeof a) {
                var c = 0;
                switch (a) {
                case b.MediaPlayer.SOUNDTRACK_STEREO:
                    c = 1;
                    break;
                case b.MediaPlayer.SOUNDTRACK_LEFT:
                    c = 2;
                    break;
                case b.MediaPlayer.SOUNDTRACK_RIGHT:
                    c = 3;
                    break;
                case b.MediaPlayer.SOUNDTRACK_MIX:
                    c = 4
                }
                return !jPlayer.setSoundTrack(this.__channel.networkId, this.__channel.tsId, this.__channel.serviceId, c)
            }
            return 0
        }
    },
    Object.defineProperties(b.Channel.prototype, {
        name: {
            get: function() {
                return this.__channel ? this.__channel.name : ""
            },
            enumerable: !0,
            configurable: !1
        },
        type: {
            get: function() {
                return this.__channel ? this.__channel.type : ""
            },
            enumerable: !0,
            configurable: !1
        },
        id: {
            get: function() {
                return this.__channel ? this.__channel.id : ""
            },
            enumerable: !0,
            configurable: !1
        },
        frequency: {
            get: function() {
                return this.__channel ? this.__channel.frequency : ""
            },
            enumerable: !0,
            configurable: !1
        },
        symbolRate: {
            get: function() {
                return this.__channel ? this.__channel.symbolRate : ""
            },
            enumerable: !0,
            configurable: !1
        },
        modulation: {
            get: function() {
                return this.__channel ? this.__channel.modulation : ""
            },
            enumerable: !0,
            configurable: !1
        },
        networkId: {
            get: function() {
                return this.__channel ? this.__channel.networkId : ""
            },
            enumerable: !0,
            configurable: !1
        },
        tsId: {
            get: function() {
                return this.__channel ? this.__channel.tsId : ""
            },
            enumerable: !0,
            configurable: !1
        },
        serviceId: {
            get: function() {
                return this.__channel ? this.__channel.serviceId : ""
            },
            enumerable: !0,
            configurable: !1
        },
        logicNumber: {
            get: function() {
                return this.__channel ? this.__channel.logicNumber : ""
            },
            enumerable: !0,
            configurable: !1
        },
        videoPID: {
            get: function() {
                return this.__channel ? this.__channel.videoPID : ""
            },
            enumerable: !0,
            configurable: !1
        },
        audioPID: {
            get: function() {
                return this.__channel ? this.__channel.audioPID : ""
            },
            enumerable: !0,
            configurable: !1
        },
        PCRPID: {
            get: function() {
                return this.__channel ? this.__channel.PCRPID : ""
            },
            enumerable: !0,
            configurable: !1
        },
        isFree: {
            get: function() {
                return this.__channel ? this.__channel.isFree : ""
            },
            enumerable: !0,
            configurable: !1
        },
        playUrl: {
            get: function() {
                return this.__channel ? "dvbc://" + this.__channel.networkId + "." + this.__channel.tsId + "." + this.__channel.serviceId : ""
            },
            enumerable: !0,
            configurable: !1
        },
        videoDecodeType: {
            get: function() {
                return this.__channel ? this.__channel.videoDecodeType : ""
            },
            enumerable: !0,
            configurable: !1
        },
        audioDecodeType: {
            get: function() {
                return this.__channel ? this.__channel.audioDecodeType : ""
            },
            enumerable: !0,
            configurable: !1
        },
        isFav: {
            get: function() {
                return this.__channel ? this.__channel.isFav : ""
            },
            enumerable: !0,
            configurable: !1
        },
        isLock: {
            get: function() {
                return this.__channel ? this.__channel.isLock : ""
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_ALL: {
            get: function() {
                return "all"
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_TV: {
            get: function() {
                return 1
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_SDTV: {
            get: function() {
                return 129
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_SKTV: {
            get: function() {
                return 129
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_HDTV: {
            get: function() {
                return 128
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_RADIO: {
            get: function() {
                return 2
            },
            enumerable: !0,
            configurable: !1
        },
        TYPE_DATA_BROADCAST: {
            get: function() {
                return 12
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.ChannelList = function(a) {
        this.__channels = a
    }
    ,
    b.ChannelList.prototype = {
        get: function(a) {
            return 0 === this.__channels.length ? null : "number" != typeof a ? null : a >= 0 && a < this.__channels.length ? this.__channels[a] : null
        },
        __find: function(a) {
            return 0 === this.__channels.length ? null : "function" != typeof a ? null : this.__channels.find(a) || null
        },
        getByLogicNumber: function(a) {
            return this.__channels && "number" == typeof a ? this.__find(function(b) {
                return b.logicNumber === a
            }) : null
        },
        getCurrentChannel: function() {
            var b = window.localStorage.getItem("JSF_CHANNELLIST_CURRENTCHANNEL_INFO");
            if (b)
                try {
                    var c = JSON.parse(b)
                      , d = this.__find(function(a) {
                        return a.serviceId == c.serviceId && a.networkId == c.networkId && a.tsId == a.tsId
                    });
                    if (!d && c) {
                        var d = this.__find(function(a) {
                            if (c.serviceId <= a.logicNumber)
                                return a
                        });
                        d || (d = this.get(0))
                    }
                    return d
                } catch (b) {
                    a("[cns.ChannelList] getCurrentChannel error " + b.message)
                }
            return null
        },
        setCurrentChannel: function(a) {
            return a instanceof b.Channel ? (window.localStorage.setItem("JSF_CHANNELLIST_CURRENTCHANNEL_INFO", '{"serviceId":' + a.serviceId + ',"networkId":' + a.networkId + ',"tsId":' + a.tsId + ',"frequency":' + a.frequency + ',"index":' + a.id + "}"),
            1) : 0
        },
        find: function(a) {
            if (a instanceof b.Channel)
                for (var c = 0; c < this.length; c++)
                    if (a.logicNumber == this.__channels[c].logicNumber)
                        return c;
            return -1
        }
    },
    Object.defineProperties(b.ChannelList.prototype, {
        length: {
            get: function() {
                return this.__channels ? this.__channels.length : 0
            },
            enumerable: !0,
            configurable: !1
        },
        lastChannel: {
            get: function() {
                return null
            },
            enumerable: !0,
            configurable: !1
        },
        nextChannel: {
            get: function() {
                return null
            },
            enumerable: !0,
            configurable: !1
        }
    });
    var f = {};
    b.ChannelManage = {
        getChannelList: function(c, d, e) {
            if ("[object Array]" !== Object.prototype.toString.call(c) || "[object Array]" !== Object.prototype.toString.call(d) || "number" != typeof e)
                return [];
            var g = {
                keyArray: c,
                valueArray: d,
                rule: e
            }
              , h = jChannelManager.getChannelList(JSON.stringify(g));
            console.log("cns.ChannelManager getChannelList data is " + h);
            try {
                f.channels = new b.ChannelList(JSON.parse(h).map(function(a) {
                    return new b.Channel(a)
                }));
                return this.__filterChannel(c, d, e)
            } catch (b) {
                a("[cns.ChannelManage] error" + b.message)
            }
        },
        __filterChannel: function(a, c, d) {
            for (var e = {
                tv: "",
                radio: "",
                broadcast: "",
                all: "",
                sd: "",
                hd: "",
                fav: "",
                bat: "",
                fta: "",
                hide: "",
                name: "",
                lock: ""
            }, g = [], h = 0, i = a.length; h < i; h++)
                switch (a[h]) {
                case b.ChannelManage.FILTER_KEY_CHANNELTYPE:
                    1 == c[h] ? e.tv = c[h] : 2 == c[h] ? e.radio = c[h] : 12 == c[h] ? e.broadcast = c[h] : "all" == c[h] ? e.all = c[h] : 129 == c[h] ? e.sk = c[h] : 128 == c[h] && (e.hd = c[h]);
                    break;
                case b.ChannelManage.FILTER_KEY_FAV:
                    e.fav = c[h];
                    break;
                case b.ChannelManage.FILTER_KEY_BAT:
                    e.bat = c[h];
                    break;
                case b.ChannelManage.FILTER_KEY_FTA:
                    e.fta = c[h];
                    break;
                case b.ChannelManage.FILTER_KEY_HIDE:
                    e.hide = c[h];
                    break;
                case b.ChannelManage.FILTER_KEY_LOCK:
                    e.lock = c[h];
                    break;
                case b.ChannelManage.FILTER_KEY_TIMESHIFT:
                    e.timeShift = c[h];
                    break;
                case b.ChannelManage.FILTER_KEY_NAME:
                    e.name = c[h]
                }
            if (e.all)
                return f.channels;
            for (var g = [], h = 0, j = f.channels.length; h < j; h++)
                switch (d) {
                case 0:
                    1 == e.tv && (22 != f.channels.get(h).type && 25 != f.channels.get(h).type || g.push(f.channels.get(h))),
                    2 == e.radio && 10 == f.channels.get(h).type && g.push(f.channels.get(h)),
                    12 == e.broadcast && (16 != f.channels.get(h).type && 152 != f.channels.get(h).type || g.push(f.channels.get(h))),
                    (f.channels.get(h).type == e.tv || f.channels.get(h).type == e.radio || f.channels.get(h).type == e.hd || f.channels.get(h).type == e.sk || f.channels.get(h).type == e.broadcast || e.fav === (f.channels.get(h).favor ? 1 : 0) || e.bat === f.channels.get(h).bat || e.fta === f.channels.get(h).freeCA || e.hide === (f.channels.get(h).userHide ? 1 : 0) || e.timeShift === (f.channels.get(h).timeShift ? 1 : 0) || (e.name ? -1 != f.channels.get(h).name.replace(/\s+/g, "").indexOf(e.name.replace(/\s+/g, "")) : 0) || e.lock === f.channels.get(h).lock) && g.push(f.channels.get(h));
                    break;
                case 1:
                    (e.tv ? e.tv == f.channels.get(h).type || 22 == f.channels.get(h).type || 25 == f.channels.get(h).type : 1) && (e.radio ? e.radio == f.channels.get(h).type || 10 === f.channels.get(h).type : 1) && (e.hd ? e.hd == f.channels.get(h).type : 1) && (e.sk ? e.sk == f.channels.get(h).type : 1) && (e.broadcast ? e.broadcast == f.channels.get(h).type || 16 == f.channels.get(h).type || 152 == f.channels.get(h).type : 1) && (e.fav ? (e.fav ? 1 : 0) == f.channels.get(h)._fav : 1) && (e.bat ? e.bat == f.channels.get(h).bat : 1) && (e.fta ? e.fta == f.channels.get(h).freeCA : 1) && (e.hide ? (e.hide ? 1 : 0) == f.channels.get(h)._userHide : 1) && (e.timeShift ? (e.timeShift ? 1 : 0) == f.channels.get(h).isTimeShift : 1) && (e.name ? -1 != f.channels.get(h).name.replace(/\s+/g, "").indexOf(e.name.replace(/\s+/g, "")) : 1) && (e.lock ? e.lock == f.channels.get(h).lock : 1) && g.push(f.channels.get(h))
                }
            return f.channels = new b.ChannelList(g.map(function(a) {
                return new b.Channel(a)
            })),
            f.channels
        },
        addListener: function(a) {
            "function" == typeof a && b.Event.addListener({
                type: "system",
                eventType: "channel",
                callback: a,
                context: this
            })
        },
        removeListener: function(a) {
            b.Event.removeEventListener("channel", a)
        }
    },
    Object.defineProperties(b.ChannelManage, {
        FILTER_KEY_CHANNELTYPE: {
            get: function() {
                return "channelType"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_FAV: {
            get: function() {
                return "fav"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_BAT: {
            get: function() {
                return "bat"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_HIDE: {
            get: function() {
                return "hide"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_FTA: {
            get: function() {
                return "fta"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_NAME: {
            get: function() {
                return "name"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_LOCK: {
            get: function() {
                return "lock"
            },
            enumerable: !0,
            configurable: !1
        },
        FILTER_KEY_TIMESHIFT: {
            get: function() {
                return "timeShift"
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.SystemInfo = {
        __serNo: "",
        __hwVer: "",
        __swVer: "",
        __loaderVer: "",
        __kernelVer: "",
        __middlewareVer: "",
        __swDate: "",
        __cpu: "",
        __stbModel: "",
        set: function(a, b) {
            return "string" == typeof a ? !jSystemInfo.set(a, b) : 0
        },
        get: function(a) {
            return "stirng" == typeof a ? jSystemInfo.get(a) : ""
        }
    },
    Object.defineProperties(b.SystemInfo, {
        serNo: {
            get: function() {
                return this.__serNo || (this.__serNo = jSystemInfo.getSerialNo()),
                this.__serNo
            },
            enumerable: !0,
            configurable: !1
        },
        hwVer: {
            get: function() {
                return this.__hwVer || (this.__hwVer = jSystemInfo.getHardwareVersion()),
                this.__hwVer
            },
            enumerable: !0,
            configurable: !1
        },
        swVer: {
            get: function() {
                return this.__swVer || (this.__swVer = jSystemInfo.getSoftwareVersion()),
                this.__swVer
            },
            enumerable: !0,
            configurable: !1
        },
        loaderVer: {
            get: function() {
                return this.__loaderVer || (this.__loaderVer = jSystemInfo.getLoaderVersion()),
                this.__loaderVer
            },
            enumerable: !0,
            configurable: !1
        },
        kernelVer: {
            get: function() {
                return this.__kernelVer || (this.__kernelVer = jSystemInfo.getKernelVersion()),
                this.__kernelVer
            },
            enumerable: !0,
            configurable: !1
        },
        middlewareVer: {
            get: function() {
                return this.__middlewareVer || (this.__middlewareVer = jSystemInfo.getMiddlewareVersion()),
                this.__middlewareVer
            },
            enumerable: !0,
            configurable: !1
        },
        swDate: {
            get: function() {
                return this.__swDate || (this.__swDate = jSystemInfo.getSoftwareDate()),
                this.__swDate
            },
            enumerable: !0,
            configurable: !1
        },
        cpu: {
            get: function() {
                return this.__cpu || (this.__cpu = jSystemInfo.getCPUType()),
                this.__cpu
            },
            enumerable: !0,
            configurable: !1
        },
        stbModel: {
            get: function() {
                return this.__stbModel || (this.__stbModel = jSystemInfo.getStbModel()),
                this.__stbModel
            },
            enumerable: !0,
            configurable: !1
        },
        ramSize: {
            get: function() {
                return jSystemInfo.getRamSize()
            },
            enumerable: !0,
            configurable: !1
        },
        flashSize: {
            get: function() {
                return jSystemInfo.getFlashSize()
            },
            enumerable: !0,
            configurable: !1
        },
        hdcp: {
            get: function() {
                return jSystemInfo.getHdcp()
            },
            enumerable: !0,
            configurable: !1
        },
        mac: {
            get: function() {
                return jSystemInfo.getMac()
            },
            enumerable: !0,
            configurable: !1
        },
        audioLanguage: {
            get: function() {
                switch (jSystemInfo.getAudioLanguage()) {
                case 1:
                    return "Chinese";
                case 2:
                    return "English";
                default:
                    return ""
                }
            },
            enumerable: !0,
            configurable: !1
        },
        dolbyMode: {
            get: function() {
                switch (jSystemInfo.getDolbyMode()) {
                case 1:
                    return "Bypass";
                case 2:
                    return "PCM";
                default:
                    return ""
                }
            },
            enumerable: !0,
            configurable: !1
        },
        resolution: {
            get: function() {
                switch (jSystemInfo.getResolution()) {
                case 1:
                    return "1080p";
                case 2:
                    return "1080i";
                case 3:
                    return "720p";
                case 4:
                    return "480p";
                case 5:
                    return "480i";
                default:
                    return ""
                }
            },
            enumerable: !0,
            configurable: !1
        },
        aspectRatio: {
            get: function() {
                switch (jSystemInfo.getAspectRadio()) {
                case 1:
                    return "4:3";
                case 2:
                    return "16:9";
                case 3:
                    return "Auto";
                default:
                    return ""
                }
            },
            enumerable: !0,
            configurable: !1
        },
        aspectCVRS: {
            get: function() {
                switch (jSystemInfo.getAspectCVRS()) {
                case 1:
                    return "FullScreen";
                case 2:
                    return "LetterBox";
                case 3:
                    return "Panscan";
                case 4:
                    return "pillarBox";
                default:
                    return ""
                }
            },
            enumerable: !0,
            configurable: !1
        },
        zipCode: {
            get: function() {
                try {
                    var b = JSON.parse(jCA.getOperators());
                    if (console.log("[cns.SystemInfo] operators is " + b.length),
                    b.length > 0) {
                        var c = JSON.parse(jCA.getAcList(b[0].operatorId))
                          , d = "20";
                        console.log("[cns.SystemInfo] acList is " + JSON.stringify(c));
                        for (var e = 0; e < c.length; e++)
                            if ("AC0" == c[e].name) {
                                d = c[e].value;
                                break
                            }
                        return d
                    }
                    return "20"
                } catch (b) {
                    return a("cns.CA.areaCode error =" + b.message),
                    "20"
                }
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.Setting = {
        setEnv: function(a, b) {
            "string" == typeof a && jSetting.setEnv(a, b)
        },
        getEnv: function(a) {
            if ("string" == typeof a) {
                var b = jSetting.getEnv(a);
                return b || null
            }
            return null
        },
        deleteEnv: function(a) {
            "string" == typeof a && jSetting.deleteEnv(a)
        },
        setLocalStorage: function(a, b) {
            "string" == typeof a && jSetting.setLocalStorage(a, b)
        },
        getLocalStorage: function(a) {
            if ("watchLevel" == a) {
                var b = jSetting.getLocalStorage(a);
                return b && (b = b.replace(/[^0-9]/gi, "")),
                b
            }
            if ("menuLanguage" == a)
                return jSetting.getLocalStorage(a);
            if ("string" == typeof a) {
                var c = jSetting.getLocalStorage(a);
                return c || null
            }
        },
        deleteLocalStorage: function(a) {
            "string" == typeof a && jSetting.deleteEnv(a)
        },
        setGlobalKeyMode: function(a) {
            "number" == typeof a && jSetting.setGlobalKeyMode(a)
        },
        getParentalPIN: function() {
            var a = jSetting.getParentalPIN();
            return a || "0000"
        },
        getPurchasePIN: function() {
            var a = jSetting.getPurchasePIN();
            return a || "0000"
        },
        wmRestart: function() {
            jSetting.wmRestart()
        }
    },
    b.IP = function(a) {
        this.__ip = a
    }
    ,
    Object.defineProperties(b.IP.prototype, {
        ip: {
            get: function() {
                return this.__ip ? this.__ip.ip : ""
            },
            enumerable: !0,
            configurable: !1
        },
        mask: {
            get: function() {
                return this.__ip ? this.__ip.mask : ""
            },
            enumerable: !0,
            configurable: !1
        },
        gateway: {
            get: function() {
                return this.__ip ? this.__ip.gateway : ""
            },
            enumerable: !0,
            configurable: !1
        },
        dnsArray: {
            get: function() {
                return this.__ip ? this.__ip.dnsArray : ""
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.NetWork = function(a) {
        this.__netWork = a
    }
    ,
    b.NetWork.prototype = {
        getConnectType: function() {
            switch (jNetworkManager.getConnectType(this.__netWork.name)) {
            case 1:
                return this.CONNECTTYPE_STATIC;
            case 2:
                return this.CONNECTTYPE_DHCP;
            case 3:
                return this.CONNECTTYPE_PPPOE;
            case 4:
                return this.CONNECTTYPE_PPPOECA;
            case 5:
                return this.CONNECTTYPE_DHCPPLUS;
            default:
                return ""
            }
        },
        getIPs: function() {
            var c = jNetworkManager.getIPs();
            try {
                var d = [];
                return JSON.parse(c).map(function(a) {
                    d.push(new b.IP(a))
                }),
                d
            } catch (b) {
                return a("[netWork] getIPs error " + b.message),
                []
            }
        },
        ping: function(a) {
            "string" == typeof a && jNetworkManager.ping(a)
        }
    },
    Object.defineProperties(b.NetWork.prototype, {
        name: {
            get: function() {
                return this.__netWork ? this.__netWork.name : ""
            },
            enumerable: !0,
            configurable: !1
        },
        deviceType: {
            get: function() {
                return this.__netWork ? this.__netWork.deviceType : ""
            },
            enumerable: !0,
            configurable: !1
        },
        mac: {
            get: function() {
                return this.__netWork ? this.__netWork.mac : ""
            },
            enumerable: !0,
            configurable: !1
        },
        plugStatus: {
            get: function() {
                return !!this.__netWork && jNetworkManager.getPhyStatus(this.__netWork.name)
            },
            enumerable: !0,
            configurable: !1
        },
        isConnected: {
            get: function() {
                return !!this.__netWork && jNetworkManager.isConnected(this.__netWork.name)
            },
            enumerable: !0,
            configurable: !1
        },
        CONNECTTYPE_STATIC: {
            get: function() {
                return "static"
            },
            enumerable: !0,
            configurable: !1
        },
        CONNECTTYPE_DHCP: {
            get: function() {
                return "dhcp"
            },
            enumerable: !0,
            configurable: !1
        },
        CONNECTTYPE_PPPOE: {
            get: function() {
                return "pppoe"
            },

            enumerable: !0,
            configurable: !1
        },
        CONNECTTYPE_PPPOECA: {
            get: function() {
                return "pppoeCA"
            },
            enumerable: !0,
            configurable: !1
        },
        CONNECTTYPE_DHCPPLUS: {
            get: function() {
                return "dhcp+"
            },
            enumerable: !0,
            configurable: !1
        },
        ENCRPTYPE_NONE: {
            get: function() {
                return "none"
            },
            enumerable: !0,
            configurable: !1
        },
        ENCRPTYPE_WEP: {
            get: function() {
                return "wep"
            },
            enumerable: !0,
            configurable: !1
        },
        ENCRPTYPE_WPAPSK: {
            get: function() {
                return "wpa psk"
            },
            enumerable: !0,
            configurable: !1
        },
        ENCRPTYPE_WPA2PSK: {
            get: function() {
                return "wpa2psk"
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.NetworkManage = {
        getNetworks: function() {
            var c = jNetworkManager.getNetworks();
            try {
                var d = [];
                return JSON.parse(c).map(function(a) {
                    d.push(new b.NetWork(a))
                }),
                d
            } catch (b) {
                a("[cns.ChannelManage] error" + b.message)
            }
        },
        addListener: function(a) {
            b.Event.addListener({
                type: "system",
                eventType: "netWork",
                callback: a,
                context: this
            })
        },
        removeListener: function(a) {
            b.Event.removeEventListener("netWork", a)
        }
    },
    b.App = function(a) {
        this.__app = a
    }
    ,
    Object.defineProperties(b.App.prototype, {
        id: {
            get: function() {
                return this.__app ? this.__app.id : ""
            },
            enumerable: !0,
            configurable: !1
        },
        name: {
            get: function() {
                return this.__app ? this.__app.name : ""
            },
            enumerable: !0,
            configurable: !1
        },
        url: {
            get: function() {
                return this.__app ? this.__app.url : ""
            },
            enumerable: !0,
            configurable: !1
        },
        version: {
            get: function() {
                return this.__app ? this.__app.version : ""
            },
            enumerable: !0,
            configurable: !1
        },
        dependVersion: {
            get: function() {
                return this.__app ? this.__app.dependVersion : ""
            },
            enumerable: !0,
            configurable: !1
        },
        showName: {
            get: function() {
                return this.__app ? this.__app.showName : ""
            },
            enumerable: !0,
            configurable: !1
        },
        category: {
            get: function() {
                return this.__app ? this.__app.category : ""
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.AppManage = {
        getAll: function() {
            try {
                var a = [];
                return JSON.parse(jAppManager.getAll()).map(function(c) {
                    a.push(new b.App(c))
                }),
                a
            } catch (a) {
                return []
            }
        },
        getByName: function(c) {
            if ("string" != typeof c)
                return null;
            try {
                return "edollar" == c ? new b.App({
                    id: "12",
                    name: "edollar",
                    url: "http://172.17.128.11/edollar/index.html",
                    version: "",
                    dependVersion: "",
                    showName: ["中文", "English"],
                    category: ""
                }) : "portal" == c ? new b.App({
                    id: "13",
                    name: "portal",
                    url: "portal",
                    version: "",
                    dependVersion: "",
                    showName: ["中文", "English"],
                    category: ""
                }) : "payment" == c ? new b.App({
                    id: "14",
                    name: "portal",
                    url: " http://172.17.128.11/payment/index.html",
                    version: "",
                    dependVersion: "",
                    showName: ["中文", "English"],
                    category: ""
                }) : new b.App(JSON.parse(jAppManager.getByName(c)))
            } catch (b) {
                return a("[cns.AppManage] getByName error =" + b.message),
                null
            }
        },
        getById: function(c) {
            if ("string" != typeof c)
                return null;
            try {
                return new b.App(JSON.parse(jAppManager.getById(c)))
            } catch (b) {
                return a("[cns.AppManage] getById error =" + b.message),
                null
            }
        },
        getByUrl: function(c) {
            if ("string" != typeof c)
                return null;
            try {
                return new b.App(JSON.parse(jAppManager.getByUrl(c)))
            } catch (b) {
                return a("[cns.AppManage] getById error =" + b.message),
                null
            }
        },
        getCurrent: function() {
            try {
                return new b.App(JSON.parse(jAppManager.getCurrent()))
            } catch (b) {
                return a("[cns.AppManage] getCurrent error =" + b.message),
                null
            }
        },
        go: function(b) {
            "string" == typeof b ? (console.log("[cns.AppManage] go url is " + b),
            b.indexOf("edollar") > -1 || b.indexOf("payment") > -1 ? window.location.href = b : b.indexOf("portal") > -1 && jAppManager.goPortal()) : a("[cns.AppManage] go error url")
        },
        goPortal: function() {
            jAppManager.goPortal()
        }
    },
    b.Tuner = {
        addListener: function(a) {
            "function" == typeof a && b.Event.addListener({
                type: "system",
                eventType: "tuner",
                callback: a,
                context: this
            })
        },
        removeListener: function(a) {
            b.Event.removeEventListener("tuner", a)
        }
    },
    b.CA = {
        getOperators: function() {
            try {
                return JSON.parse(jCA.getOperators())
            } catch (b) {
                return a("[cns.CA] getOperators error " + b.message),
                []
            }
        },
        getEntitles: function(a) {
            try {
                return JSON.parse(jCA.getEntitles(a))
            } catch (a) {
                return []
            }
        },
        addListener: function(a) {
            "function" == typeof a && b.Event.addListener({
                type: "system",
                eventType: "ca",
                callback: a,
                context: this
            })
        },
        removeListener: function(a) {
            b.Event.removeEventListener("ca", a)
        }
    },
    Object.defineProperties(b.CA, {
        name: {
            get: function() {
                try {
                    return JSON.parse(jCA.getProperties()).name
                } catch (b) {
                    return a("cns.CA.name error =" + b.message),
                    ""
                }
            }
        },
        cardId: {
            get: function() {
                try {
                    return JSON.parse(jCA.getProperties()).cardId
                } catch (b) {
                    return a("cns.CA.cardId error =" + b.message),
                    ""
                }
            }
        },
        areaCode: {
            get: function() {
                try {
                    var b = JSON.parse(jCA.getOperators());
                    if (b.length > 0) {
                        for (var c = JSON.parse(jCA.getAcList(b[0].operatorId)), d = 0, e = 0; e < c.length; e++)
                            if ("areaCode" == c[e].name) {
                                d = c[e].value;
                                break
                            }
                        return d
                    }
                    return 0
                } catch (b) {
                    return a("cns.CA.areaCode error =" + b.message),
                    0
                }
            }
        },
        provider: {
            get: function() {
                try {
                    return JSON.parse(jCA.getProperties()).provider
                } catch (b) {
                    return a("cns.CA.provider error =" + b.message),
                    ""
                }
            }
        },
        expireDate: {
            get: function() {
                try {
                    return JSON.parse(jCA.getProperties()).expireDate
                } catch (b) {
                    return a("cns.CA.expireDate error =" + b.message),
                    "2028-07-03 23:59:59"
                }
            }
        }
    }),
    b.TS = {},
    Object.defineProperties(b.TS, {
        MODULATION_QAM16: {
            get: function() {
                return "qam16"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULATION_QAM32: {
            get: function() {
                return "qam32"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULATION_QAM64: {
            get: function() {
                return "qam64"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULATION_QAM128: {
            get: function() {
                return "qam128"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULATION_QAM256: {
            get: function() {
                return "qam256"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULATION_QAM512: {
            get: function() {
                return "qam512"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULATION_QAM1024: {
            get: function() {
                return "qam1024"
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    b.AD = function() {
        var b = function(b) {
            var c = {
                actionType: function() {
                    var c = "";
                    try {
                        c = b.actionType
                    } catch (b) {
                        a("[cns.AD][cnsADAsset]Init duration error!.")
                    }
                    return c
                }(),
                actionValue: function() {
                    var c = "";
                    try {
                        c = b.actionValue
                    } catch (b) {
                        a("[cns.AD][cnsADAsset]Init actionUrl error!.")
                    }
                    return c
                }(),
                type: function() {
                    var c = "";
                    try {
                        c = b.type
                    } catch (b) {
                        a("[cns.AD][cnsADAsset]Init type error!.")
                    }
                    return c
                }(),
                url: function() {
                    var c = "";
                    try {
                        c = b.assetValue
                    } catch (b) {
                        a("[cns.AD][cnsADAsset]Init url error!.")
                    }
                    return c
                }()
            };
            Object.defineProperties(this, {
                actionType: {
                    value: c.actionType,
                    enumerable: !0,
                    writable: !1
                },
                actionValue: {
                    value: c.actionValue,
                    enumerable: !0,
                    writable: !1
                },
                url: {
                    value: c.url,
                    enumerable: !0,
                    writable: !1
                }
            })
        }
          , c = function(c) {
            var d = {
                assets: function() {
                    var d = [];
                    try {
                        var e = c.assetType;
                        for (var f in e)
                            for (var g = e[f], h = 0; h < g.length; h++) {
                                var i = g[h];
                                i.type = f;
                                var j = new b(i);
                                d.push(j)
                            }
                    } catch (b) {
                        a("[cns.AD][cnsADChild]Init assets error!.")
                    }
                    return d
                }(),
                duration: function() {
                    var b = "";
                    try {
                        b = Number(c.durationValue)
                    } catch (b) {
                        a("[cns.AD][cnsADChild]Init duration error!.")
                    }
                    return b
                }(),
                assetInterval: function() {
                    var b = "";
                    try {
                        b = Number(c.playModeValue)
                    } catch (b) {
                        a("[cns.AD][cnsADChild]Init duration error!.")
                    }
                    return b
                }(),
                playModeMainType: function() {
                    var b = "";
                    try {
                        b = c.playModeType
                    } catch (b) {
                        a("[cns.AD][cnsADChild]Init playMode error!.")
                    }
                    return b
                }(),
                playModeSubType: function() {
                    var b = "";
                    try {
                        b = c.playModeSubType
                    } catch (b) {
                        a("[cns.AD][cnsADChild]Init playMode error!.")
                    }
                    return b
                }()
            };
            Object.defineProperties(this, {
                assets: {
                    value: d.assets,
                    enumerable: !0,
                    writable: !1
                },
                assetInterval: {
                    value: d.assetInterval,
                    enumerable: !0,
                    writable: !1
                },
                duration: {
                    value: d.duration,
                    enumerable: !0,
                    writable: !1
                },
                playModeMainType: {
                    value: d.playModeMainType,
                    enumerable: !0,
                    writable: !1
                },
                playModeSubType: {
                    value: d.playModeSubType,
                    enumerable: !0,
                    writable: !1
                }
            })
        };
        return function(b) {
            var d = {
                blockName: function() {
                    var c = "";
                    try {
                        c = b.blockName
                    } catch (b) {
                        a("[cns.AD][cnsAD]Init blockName error!.")
                    }
                    return c
                }(),
                children: function() {
                    var d = [];
                    try {
                        for (var e = 0; e < b.children.length; e++) {
                            var f = new c(b.children[e]);
                            d.push(f)
                        }
                    } catch (b) {
                        a("[cns.AD][cnsAD]Init children error!.")
                    }
                    return d
                }(),
                childrenPeriod: function() {
                    for (var a = 0, c = 0; c < b.children.length; c++)
                        a += parseInt(b.children[c].durationValue);
                    return a
                }(),
                entryTimeStamp: function() {
                    var c = 0;
                    try {
                        c = new Date(b.entryTime).getTime()
                    } catch (b) {
                        a("[cns.AD][cnsAD]Init entryTimeStamp error!.")
                    }
                    return c
                }(),
                pathPrefix: function() {
                    var c = "";
                    try {
                        c = b.pathPrefix
                    } catch (b) {
                        a("[cns.AD][cnsAD]Init pathPrefix error!.")
                    }
                    return c
                }()
            };
            Object.defineProperties(this, {
                children: {
                    value: d.children,
                    enumerable: !0,
                    writable: !1
                },
                childrenPeriod: {
                    value: d.childrenPeriod,
                    enumerable: !0,
                    writable: !1
                },
                entryTimeStamp: {
                    value: d.entryTimeStamp,
                    enumerable: !0,
                    writable: !1
                },
                pathPrefix: {
                    value: d.pathPrefix,
                    enumerable: !0,
                    writable: !1
                }
            })
        }
    }(),
    b.ADManager = function() {
        var c = {
            ENTRY_POLLING_INTERVAL: 12096e5
        }
          , d = {
            cnsADArray: {},
            refreshAllAD: function() {
                a("refresh all ad");
                try {
                    for (var b in d.cnsADArray) {
                        d.cnsADArray[b].__Refresh__()
                    }
                } catch (b) {
                    a("ADManager.refreshAllAD() error : " + b)
                }
            }
        }
          , e = function(e, f) {
            var g = {
                blockName: f,
                assetIndex: -1,
                assetTimer: -1,
                changeChildTimer: -1,
                childIndex: -1,
                entryTimer: -1,
                isPlaying: !1,
                imageDom: function() {
                    return e.innerHTML = '<img src="" style="width: 100%;height: 100%;">',
                    e.firstChild
                }(),
                cnsAD: function() {
                    var c = {};
                    f || (f = g.blockName);
                    try {
                        var d = jAD.getCNSAdvInfoByBlockName(f);
                        a("[ADManage] cnsAD returnObj =" + c);
                        var e = "";
                        if (d)
                            return e = JSON.parse(d),
                            c = new b.AD(e)
                    } catch (a) {
                        return c
                    }
                }(),
                refreshAD: function() {
                    f || (f = g.blockName);
                    try {
                        var c = jAD.getCNSAdvInfoByBlockName(f);
                        if (a("[ADManage] cnsAD refreshAD returnObj =" + c),
                        c)
                            return c = new b.AD(JSON.parse(c))
                    } catch (b) {
                        return a("cnsAD.refreshAD(string blockName) error : " + b.message),
                        {}
                    }
                },
                show: function() {
                    g.isPlaying = !0;
                    var a = b.Setting.getLocalStorage("extendKey1");
                    if (a && "0" !== a && "00" !== a) {
                        g.showDefaultImage(a);
                        var c = g.initEntryTimer();
                        c > 0 && g.startAD(c)
                    }
                },
                showDefaultImage: function(a) {},
                initEntryTimer: function() {
                    var b = g.getEntryTimeDifference();
                    if (a("initEntryTimer difference=" + b + "**" + c.ENTRY_POLLING_INTERVAL),
                    b <= 0) {
                        clearTimeout(g.entryTimer);
                        var e = -b;
                        a("initEntryTimer temp=" + e + "**" + c.ENTRY_POLLING_INTERVAL),
                        e > c.ENTRY_POLLING_INTERVAL ? d.refreshAllAD() : g.entryTimer = setTimeout(function() {
                            g.startAD(0),
                            g.entryTimer = -1
                        }, e)
                    }
                    return b
                },
                getEntryTimeDifference: function(a) {
                    return a || (a = (new Date).getTime()),
                    a - g.cnsAD.entryTimeStamp
                },
                forceGetCurrentChild: function() {
                    var a, b = g.getEntryTimeDifference(), c = g.cnsAD.children, d = g.cnsAD.childrenPeriod, e = b / 1e3 % d, f = e;
                    for (a = 0; a < c.length; a++)
                        if ((f -= c[a].duration) <= 0) {
                            f += c[a].duration;
                            break
                        }
                    return {
                        currentChildIndex: a,
                        passedTimeOfChild: f
                    }
                },
                initAssetIndex: function(b) {
                    try {
                        var c = g.getCurrentChild()
                          , d = -1;
                        return "bytime" == c.playModeSubType ? (g.changeAsset(Math.floor(b / c.assetInterval)),
                        d = b % c.assetInterval) : (g.assetIndex = 0,
                        d = c.assetInterval),
                        {
                            remainingShowTime: d
                        }
                    } catch (b) {
                        a("cnsAD.initAssetIndex(number passedTime) error : " + b.message)
                    }
                },
                initChildrenIndex: function(a) {
                    var b = g.forceGetCurrentChild();
                    return g.childIndex = b.currentChildIndex,
                    {
                        passedTimeOfChild: b.passedTimeOfChild
                    }
                },
                initChildTimer: function(a) {
                    var b = g.cnsAD.children;
                    if (b.length > 1) {
                        var c = a
                          , d = b[g.childIndex]
                          , e = 1e3 * (d.duration - c);
                        g.changeChildTimer = setTimeout(g.nextChild, e)
                    }
                },
                showImage: function() {
                    try {
                        var b = g.imageDom
                          , c = g.cnsAD
                          , d = c.children[g.childIndex]
                          , e = d.assets[g.assetIndex];
                        a("[AD] showImage path =" + c.pathPrefix + ",url=" + e.url),
                        b.src = e.url
                    } catch (b) {
                        a("cnsAD.showImage() error : " + b.message)
                    }
                },
                startBroadcast: function(a) {
                    a || (a = g.getCurrentChild().assetInterval);
                    var b = function() {
                        g.nextAsset();
                        var a = g.getCurrentChild().assetInterval;
                        g.assetTimer = setTimeout(b, 1e3 * a, a)
                    };
                    g.assetTimer = setTimeout(b, 1e3 * a)
                },
                changeAsset: function(a) {
                    g.assetIndex = (g.assetIndex + a) % g.getCurrentChild().assets.length,
                    g.assetIndex < 0 && (g.assetIndex = 0)
                },
                changeChild: function(a) {
                    clearTimeout(g.assetTimer),
                    g.childIndex = (g.childIndex + a) % g.cnsAD.children.length,
                    g.assetIndex = 0,
                    g.showImage(),
                    g.startBroadcast(0)
                },
                getCurrentChild: function() {
                    return g.cnsAD.children[g.childIndex]
                },
                nextAsset: function() {
                    try {
                        var b = g.getCurrentChild();
                        if (b && "interval" == b.playModeMainType)
                            g.changeAsset(1);
                        else if (b && "random" == b.playModeMainType) {
                            var c = Math.floor(Math.random() * g.cnsAD.children[g.childIndex].assets.length);
                            g.changeAsset(c)
                        }
                        g.showImage()
                    } catch (b) {
                        a("cnsAD.nextAsset() error : " + b)
                    }
                },
                nextChild: function() {
                    g.changeChild(1),
                    g.changeChildTimer = setTimeout(g.nextChild, 1e3 * g.getCurrentChild().duration)
                },
                startAD: function(a) {
                    var b = g.initChildrenIndex(a);
                    g.initChildTimer(b.passedTimeOfChild),
                    b = g.initAssetIndex(b.passedTimeOfChild),
                    g.isPlaying && (g.showImage(),
                    g.startBroadcast(b.remainingShowTime))
                },
                stop: function(a) {
                    clearTimeout(g.assetTimer),
                    clearTimeout(g.changeChildTimer),
                    clearTimeout(g.entryTimer),
                    g.assetTimer = -1,
                    g.changeChildTimer = -1,
                    g.entryTimer = -1,
                    a ? (g.refreshAD(),
                    g.isPlaying && g.show()) : g.isPlaying = !1
                },
                resume: function() {
                    g.isPlaying = !0;
                    var a = g.initEntryTimer();
                    if (a > 0) {
                        var b = g.initChildrenIndex(a);
                        g.initChildTimer(b.passedTimeOfChild),
                        g.startBroadcast()
                    }
                },
                keyEnter: function() {
                    var c = g.cnsAD.children[g.childIndex].assets[g.assetIndex]
                      , d = "";
                    switch (a("[ADManage] ad keyEnter actionType=" + c.actionType),
                    c.actionType) {
                    case "image":
                        break;
                    case "webpage":
                        d = c.actionValue.indexOf("http") > -1 || c.actionValue.indexOf("HTTP") > -1 ? c.actionValue : "file:///" + g.cnsAD.pathPrefix + c.actionValue;
                        break;
                    case "app":
                        var e = c.actionValue;
                        d = b.AppManage.getByName(e).url
                    }
                    a("[ADManage] keyEnter url =" + d),
                    d || a('[ADManage]there is no "' + e + '" in this STB.')
                }
            };
            Object.defineProperties(this, {
                __HasH__: {
                    value: (new Date).getTime(),
                    enumerable: !1,
                    writable: !1
                },
                __Refresh__: {
                    value: function() {
                        g.stop("no")
                    },
                    enumerable: !1,
                    writable: !1
                },
                show: {
                    value: function() {
                        g.show()
                    },
                    enumerable: !0,
                    writable: !1
                },
                keyEnter: {
                    value: function() {
                        g.keyEnter()
                    },
                    enumerable: !1,
                    writable: !1
                },
                pause: {
                    value: function() {
                        g.stop()
                    },
                    enumerable: !0,
                    writable: !1
                },
                resume: {
                    value: function() {
                        g.resume()
                    },
                    enumerable: !0,
                    writable: !1
                },
                stop: {
                    value: function() {
                        g.stop()
                    },
                    enumerable: !0,
                    writable: !1
                }
            })
        };
        return {
            createAD: function(b, c) {
                if (2 !== arguments.length)
                    return a("cns.ADManager arguments length <2"),
                    null;
                if (!b.nodeType || 1 !== b.nodeType)
                    return a("cns.ADManager arguments nodeType error"),
                    null;
                try {
                    var f = new e(b,c);
                    return d.cnsADArray[f.__HasH__] = f,
                    f
                } catch (b) {
                    return a("cns.ADManager.createAD error :" + b.message),
                    null
                }
            },
            destroy: function(b) {
                try {
                    d.cnsADArray[b.__HasH__] && (d.cnsADArray[b.__HasH__].stop(),
                    d.cnsADArray[b.__HasH__] = null)
                } catch (b) {
                    a("cns.ADManager.destroy error :" + b.message)
                }
            },
            destroyAll: function() {
                for (var a in d.cnsADArray)
                    d.cnsADArray[a].stop();
                d.cnsADArray = {}
            }
        }
    }(),
    b.logService = {
        init: function() {
            console.log("[cns.logService] init")
        },
        push: function() {
            console.log("[cns.logService] push")
        },
        version: function() {
            return console.log("[cns.logService] version"),
            "v2.1"
        }
    },
    b.Log = {
        setLevel: function(module, a) {
            var c = 4
              , d = 1;
            switch (module) {
            case b.Log.MODULE_UI:
                c = 1;
                break;
            case b.Log.MODULE_PORTING:
                c = 2;
                break;
            case b.Log.MODULE_MIDDLEWARE:
                c = 3;
                break;
            case b.Log.MODULE_ALL:
            default:
                c = 4
            }
            a >= -1 && a <= 4 && (d = a + 2),
            jLog.setLevel(c, d)
        },
        getLevel: function(module) {
            var a = -1;
            switch (module) {
            case b.Log.MODULE_UI:
                a = 1;
                break;
            case b.Log.MODULE_PORTING:
                a = 2;
                break;
            case b.Log.MODULE_MIDDLEWARE:
                a = 3;
                break;
            case b.Log.MODULE_ALL:
                a = 4
            }
            return -1 != a ? jLog.getLevel(a) - 2 : -2
        },
        v: function(b) {
            "string" == typeof b ? jLog.v(b) : a("cns.Log v error")
        },
        d: function(b) {
            "string" == typeof b ? jLog.d(b) : a("cns.Log d error")
        },
        i: function(b) {
            "string" == typeof b ? jLog.i(b) : a("cns.Log i error")
        },
        w: function(b) {
            "string" == typeof b ? jLog.w(b) : a("cns.Log w error")
        },
        e: function(b) {
            "string" == typeof b ? jLog.e(b) : a("cns.Log e error")
        }
    },
    Object.defineProperties(b.Log, {
        MODULE_UI: {
            get: function() {
                return "ui"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULE_PORTING: {
            get: function() {
                return "porting"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULE_MIDDLEWARE: {
            get: function() {
                return "middleware"
            },
            enumerable: !0,
            configurable: !1
        },
        MODULE_ALL: {
            get: function() {
                return "all"
            },
            enumerable: !0,
            configurable: !1
        },
        LEVEL_CLOSE: {
            get: function() {
                return -1
            },
            enumerable: !0,
            configurable: !1
        },
        LEVEL_VERBOSE: {
            get: function() {
                return 0
            },
            enumerable: !0,
            configurable: !1
        },
        LEVEL_DEBUG: {
            get: function() {
                return 1
            },
            enumerable: !0,
            configurable: !1
        },
        LEVEL_INFO: {
            get: function() {
                return 2
            },
            enumerable: !0,
            configurable: !1
        },
        LEVEL_WARN: {
            get: function() {
                return 3
            },
            enumerable: !0,
            configurable: !1
        },
        LEVEL_ERROR: {
            get: function() {
                return 4
            },
            enumerable: !0,
            configurable: !1
        }
    }),
    c.player = {
        __player: null,
        create: function(c) {
            a("[stb] player create position is " + c);
            var d = b.MediaPlayerManage.getMediaPlayers();
            this.__player = d.length > 0 ? d[0] : b.MediaPlayer.create(c),
            a("[stb] player create player handle is " + this.__player.__handle);
            try {
                if (this.__player) {
                    if (c) {
                        var e = JSON.parse(c);
                        this.__player.setPosition(e.wx, e.wy, e.wz, e.ww, e.wh)
                    }
                    return this.__player.handle
                }
                return a("[stb] player is null"),
                -1
            } catch (b) {
                return a("[stb] player create error is " + b.message),
                -1
            }
        },
        start: function(b, c) {
            try {
                if (a("[stb] start handle is " + b + ", param is " + c),
                b > 0) {
                    var d = JSON.parse(c);
                    d.volume && this.__player.setVolume(Number(d.volume));
                    var e = d.url ? d.url : ""
                      , f = this.__player.play(e);
                    return a("[stb] start player play rtn is " + f),
                    f ? this.__player.handle : -1
                }
                return a("[stb] start handle is bad"),
                -1
            } catch (b) {
                return a("[stb] start error " + b.message),
                -1
            }
        },
        pause: function(a) {
            return a > 0 ? !this.__player.pause() : -1
        },
        resume: function(a) {
            return a > 0 ? !this.__player.resume() : -1
        },
        forward: function(a, b) {
            return a > 0 ? b > 0 ? !this.__player.forward(Number(b)) : !this.__player.backward(Number(b)) : -1
        },
        seek: function(a, b) {
            return a > 0 ? !this.__player.seek(Number(b)) : -1
        },
        set: function(a, b) {
            try {
                if (a > 0) {
                    var c = JSON.parse(b);
                    return c.volume ? !this.__player.setVolume(c.volume) : -1
                }
                return -1
            } catch (a) {
                return -1
            }
        },
        get: function(b, c) {
            switch (c) {
            case "volume":
                if (b > 0) {
                    var d = this.__player.getVolume();
                    return JSON.stringify({
                        volume: d
                    })
                }
                return JSON.stringify({
                    volume: -1
                });
            case "duration":
                if (b > 0) {
                    var e = this.__player.duration;
                    return JSON.stringify({
                        duration: e
                    })
                }
                return JSON.stringify({
                    duration: 0
                });
            case "position":
                if (b > 0) {
                    var f = this.__player.getCurrentPlayTime();
                    return JSON.stringify({
                        position: f
                    })
                }
                return JSON.stringify({
                    position: 0
                });
            case "playStatus":
                if (b > 0) {
                    var g = this.__player.playStatus;
                    switch (a("[stb.player] playStatus is" + g),
                    g) {
                    case "stop":
                        a("[stb.player] stop"),
                        g = 0;
                        break;
                    case "play":
                        a("[stb.player] play"),
                        g = 1;
                        break;
                    case "pause":
                        a("[stb.player] pause"),
                        g = 2;
                        break;
                    case "connecting":
                        a("[stb.player] connecting"),
                        g = 3;
                        break;
                    case "buffering":
                        a("[stb.player] buffering"),
                        g = 4;
                        break;
                    case "finished":
                        a("[stb.player] finished"),
                        g = 5;
                        break;
                    case "playError":
                        a("[stb.player] playError"),
                        g = 6;
                        break;
                    default:
                        a("[stb.player] default"),
                        g = 1
                    }
                    return JSON.stringify({
                        playStatus: g
                    })
                }
                return JSON.stringify({
                    playStatus: 6
                })
            }
        },
        mute: function(a) {
            return a > 0 ? !this.__player.setMute(!0) : -1
        },
        unmute: function(a) {
            return a > 0 ? !this.__player.setMute(!1) : -1
        },
        stop: function(b) {
            if (a("[stb] stop handle is " + b),
            b > 0) {
                return !this.__player.stop()
            }
        },
        destroy: function(a) {
            if (a > 0) {
                var b = this.__player.release();
                return b = b ? 1 : -1
            }
            return -1
        }
    },
    c.evt = {
        setEventCallback: function(a) {
            b.Event.addListener({
                type: "system",
                eventType: "stb",
                callback: a,
                context: this
            })
        }
    },
    c.ca = {
        cardID: function() {
            console.log("[main] stb ca start");
            try {
                var a = JSON.parse(jCA.getProperties());
                return console.log("[stb.ca] ca is " + JSON.stringify(a)),
                JSON.stringify({
                    innerCardID: a.innerId,
                    cardID: a.cardId
                })
            } catch (a) {
                return console.log("[stb.ca] cardID error is " + a.message),
                JSON.stringify({
                    innerCardID: "560521",
                    cardID: "8122604231672135"
                })
            }
        },
        pinVerify: function(a, b) {
            return console.log("[main]pinVerify type is " + a + "pwd is " + b),
            jCA.checkPin(a, b)
        },
        getCrmID: function() {
            console.log("[main] stb ca getCrmID");
            var a = jSetting.getSoId();
            return console.log("[stb.ca] getCrmID is " + a),
            JSON.stringify({
                soId: a
            })
        },
        getDeviceSN: function() {
            console.log("[main] stb ca getDeviceSN");
            var a = jSystemInfo.getSerialNo();
            return console.log("[stb.ca] serNo is " + a),
            JSON.stringify({
                serNo: a
            })
        }
    },
    c.data = {
        getSystem: function(a) {
            return b.Setting.getLocalStorage(a)
        }
    },
    c.wm = {
        gotoSTB: function(a, c) {
            b.AppManage.goPortal()
        }
    },
    b.log = b.Log,
    window.cns = b,
    window.stb = c
}();
