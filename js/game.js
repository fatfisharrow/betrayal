
var connector = null;
var resource = null;

$(document).ready(function() {
    init_page();
    connector = new Connector("ws://127.0.0.1:19504", {
        onopen: function(evt) {
            connector.login();
        },
        onmessage: function(obj) {
            console.debug("current step = " + Page.step);
            console.debug("op = " + obj.op);

            if (Page.step == STEP_LOGIN) {
                if (obj.op == "login") {
                    Page.playerid = obj.data.id;
                    Page.nickname = obj.data.nick;
                    Page.step = STEP_LOADING;
                    loadResources();
                }
            } else if (Page.step == STEP_LOADING) {
            } else if (Page.step == STEP_IDLE) {
                if (obj.op == "creategame") {
                    if (obj.data == "success") {
                        Page.step = STEP_ROOM;
                    }
                } else if (obj.op == "games") {
                    Page.gamelist = obj.data;
                }
            } else if (Page.step == STEP_ROOM) {
                if (obj.op == "gameinfo") {
                    Page.room.id = obj.data.id;
                    Page.room.title = obj.data.title;
                    Page.players = obj.data.players;

                    for (var k in Page.players) {
                        var identify = Page.players[k].mHero.identify;
                        for (var i in Page.resources.heroes) {
                            if (Page.resources.heroes[i].identify == identify) {
                                Page.players[k].heroindex = i;
                            }
                        }
                    }

                }
            } else if (Page.step == STEP_GAME) {
            } else {
            }
        },
        onclose: function(evt) {
        },
        onerror: function(evt, e) {
        }
    });

    var loadComplete = function() {
        console.debug(resource.heroes);
        Page.resources = resource;
        Page.step = STEP_IDLE;
    }

    var loadResources = function() {
        console.debug("loadResources.");
        resource = new Resource(function(percent) {
            console.debug(percent);
            if (percent == 24) {
                loadComplete();
            }
        });
        resource.load_all();
        // connector.refreshGameList();
    }
});


