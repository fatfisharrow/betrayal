
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
            gamelist: [
            ],
            playerid: 0,
            nickname: '',
            resources: null,
            players: null,
            room: {
                title: "",
                id: 0,
            },
            game: {
            },
        },
        methods: {
            idle_joinGame: function(evt) {
                var gid = $(evt.currentTarget).attr("gid");
                console.debug(gid);
                connector.joinGame(gid);
            },
            room_nextHero: function(evt) {
                console.debug("nextHero.");
                connector.nextHero();
            },
        },
        mounted: function() {
        },
        updated: function() {
        },
    });
    return Page;
}

