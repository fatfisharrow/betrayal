
var Page = null;
var STEP_LOGIN = 0;
var STEP_LOADING = 1;
var STEP_IDLE = 2;
var STEP_ROOM = 3;
var STEP_GAME = 4;

var init_page = function() {
    Page = new Vue({
        el: "#main-wrapper",
        data: {
            step: STEP_LOGIN,
        },
        mounted: function() {
        },
        updated: function() {
        },
    });
    return Page;
}

