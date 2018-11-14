
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

    var loadResources = function() {
        Page.step = STEP_IDLE;
        console.debug("loadResources.");
        resource = new Resource(function(percent) {
            console.debug(percent);
            if (percent == 24) {
                console.debug(resource.heroes);
            }
        });
        resource.load_all();
        // connector.refreshGameList();
    }
});


