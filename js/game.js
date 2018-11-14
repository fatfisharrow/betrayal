
var connector = null;
var resource = null;

$(document).ready(function() {
    init_page();
    connector = new Connector("ws://127.0.0.1:19504", {
        onopen: function(evt) {
            connector.login();
        },
        onmessage: function(obj) {
            if (Page.step == STEP_LOGIN) {
                if (obj.op == "login") {
                    if (obj.data == "success") {
                        Page.step = STEP_LOADING;
                        loadResources();
                    }
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


