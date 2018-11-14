function Connector(address, listener) {
    this.listener = listener;
    this.ws = new WebSocket(address);
    var that = this;
    this.ws.onopen = function(evt) {
        console.debug(evt);
        that.listener.onopen(evt);
    }
    this.ws.onmessage = function(evt) {
        var obj = eval('(' + evt.data + ')');
        console.debug(obj);
        that.listener.onmessage(obj);
    }

    this.ws.onclose = function(evt) {
        that.listener.onclose(evt);
    }

    this.ws.onerror = function(evt, e) {
        that.listener.onerror(evt, e);
    }
}

Connector.prototype.send = function(obj) {
    var text = JSON.stringify(obj);
    console.debug("send: " + text);
    this.ws.send(text);
}

Connector.prototype.login = function(token) {
    this.send({
         op: 'login',
         token: token,
    });
}

Connector.prototype.createGame = function(title) {
    this.send({
         op: 'create',
         title: title,
    });
}

Connector.prototype.refreshGameList = function() {
    this.send({
         op: 'refresh',
    });
}

Connector.prototype.joinGame = function(gameid) {
    this.send({
              op: 'join',
              game: gameid,
    });
}

Connector.prototype.leaveGame = function() {
    this.send({
         op: 'leave',
    });
}

Connector.prototype.startGame = function() {
    this.send({
              op: 'start',
    });
}

Connector.prototype.selectHero = function(name) {
    send({
         op: 'select',
         data: name,
    });
}

