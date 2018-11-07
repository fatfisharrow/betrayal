
$(document).ready(function() {
    init_page();

    // var ws = new WebSocket("ws://114.215.82.75:19504");
    var ws = new WebSocket("ws://127.0.0.1:19504");

    var loadResources = function() {
        Page.step = STEP_IDLE;
        refresh();
    }

    ws.onopen = function(evt) {
        console.debug(evt);

        login();
    }

    ws.onmessage = function(evt) {
        var obj = eval('(' + evt.data + ')');
        console.debug(obj);

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
            }
        } else if (Page.step == STEP_ROOM) {
        } else if (Page.step == STEP_GAME) {
        } else {
        }
    }

    ws.onclose = function(evt) {
    }

    ws.onerror = function(evt, e) {
    }

    var send = function(obj) {
        var text = JSON.stringify(obj);
        console.debug(text);
        console.debug("send: " + text);
        ws.send(text);
    }

    var login = function() {
        send({
             op: 'login',
             token: 'aabbcc',
        });
    }

    var create = function() {
        send({
             op: 'create',
             title: 'test game.',
        });
    }

    var refresh = function() {
        send({
             op: 'refresh',
        });
    }

    var join = function() {
        send({
             op: 'refresh',
             game: 10000,
        });
    }

    var leave = function() {
        send({
             op: 'leave',
        });
    }

    var start = function() {
        send({
             op: 'start',
        });
    }

    var select_hero = function() {
        send({
             op: 'select',
             data: 'brandon',
        });
    }

});


