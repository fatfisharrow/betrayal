

function Resource(callback) {
    this.heroes = [];
    this.callback = callback;
    this.percent = 0;
    this.identify = null;
    this.title = "";
}

Resource.prototype.report = function() {
    this.callback(this.percent);
}

Resource.prototype.init_heroes = function(heroes) {
    for (var k in heroes) {
        var hero = {
            identify: heroes[k].identify,
            portrait: heroes[k].portrait
        };
        this.heroes.push(hero);
    }

    var that = this;

    console.debug(this.heroes);

    for (var k in heroes) {
        var url = g_app_url + "/" + heroes[k].profile;
        console.debug("fetch hero profile: " + url);

        $.ajax({
            url: url,
            type: "get",
            data: {},
            cache: false,
            success: function(res) {
                // console.debug("complete.");
                var profile = res; // eval("(" + res + ")");
                console.debug(profile);
                for (var i in that.heroes) {
                    // console.debug("compare " + that.heroes[i].identify + " vs " + profile.identify);
                    if (that.heroes[i].identify == profile.identify) {
                        var portrait = that.heroes[i].portrait;
                        that.heroes[i] = profile;
                        that.heroes[i].portrait = portrait;

                        that.heroes[i].image = new Image();
                        that.heroes[i].image.src = g_app_url + "/" + portrait;
                        that.heroes[i].image.onload = function() {
                            that.percent++;
                            that.report();
                        }
                        that.percent++;
                        that.report();
                        break;
                    }
                }
            },
        });
    }
}

Resource.prototype.load_all = function() {
    var that = this;

    var url = g_app_url + "/gamedata/betrayal/client/game.json";
    $.ajax({
        url: url,
        type: "get",
        data: {},
        cache: false,
        success: function(res) {
            // console.debug("complete");
            // console.debug(res);
            var game = res; // eval("(" + res + ")");
            console.debug(game);

            that.identify = game.identify;
            that.title = game.title;
            that.init_heroes(game.resources.heroes);
        }
    });
}


