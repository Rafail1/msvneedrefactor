/* global Mustache, Chat */

function serverChat() {
    this.who = "server";
    this.notifySoundMessage = new Audio("/app/chat/audio/server-message.mp3");
    this.notifySoundNew = new Audio("/app/chat/audio/new-chat.mp3");
    this.chatsDiv = $("#chats");
    this.readed = [];
}

serverChat.prototype = Object.create(Chat.prototype);
serverChat.prototype.constructor = serverChat;

serverChat.prototype.start = function () {
    this.started = true;
    this.rendered = true;
    this.getArchive({action:"getArchive"});

};
serverChat.prototype.addEventListeners = function (id) {
    if (id) {
        var self = this;
        $("#" + id + " .client-window").on("click", function () {
            self.readed[id] = true;
        })
    }
};


serverChat.prototype.check = function () {
    var self = this;
    this.lastTime = this.getLastTime();
    var data = {action: 'check', lastTime: JSON.stringify(this.lastTime)};
    this.request(data, function () {
        self.answer(this);

        setTimeout(function () {
            self.check();
        }, 100);
    });
};


serverChat.prototype.delete = function (id) {
    var self = this;
    var data = {action: "remove", id: id};
    this.request(data, function () {
        $("#" + id).remove();
        delete self.messages[id];
    });
};

serverChat.prototype.notifyInterval = function (notifySound, i) {
    $("#" + i).addClass("new_message");
    if (!this.readed[i] && !$("#" + i + " .client-window").is(":focus")) {
        this.notify(notifySound);
        var self = this;
        setTimeout(function () {
            self.notifyInterval(notifySound, i);
        }, 2000);
    } else {
        $("#" + i).removeClass("new_message");
    }
};
serverChat.prototype.answer = function (data) {
    for (var i in data) {
        if (this.messages[i]) {
            this.setOnline(data[i].online, i);
            var notify = false;
            for (var m in data[i].messages) {
                if (data[i].messages[m].who !== this.who) {
                    notify = true;
                }
                this.addMessage(data[i].messages[m], i);
            }
            if (notify) {
                this.readed[i] = false;
                this.notifyInterval(this.notifySoundMessage, i);
            }

        } else {
            this.messages[i] = data[i].messages;
            this.addChat(data, i);

            this.readed[i] = false;
            this.notifyInterval(this.notifySoundNew, i);

            this.rendered[i] = true;
        }
    }
};

serverChat.prototype.addChat = function (data, i) {
    var chat = {id: i, server: true, messages: data[i].messages};
    var self = this;
    $.get('/app/chat/tpl/chat.tpl', function (template) {
        var rendered = Mustache.render(template, chat);
        self.chatsDiv.append(rendered);
        self.addEventListeners(i);
        $('.scroller').each(function () {
            $(this).scrollTop($(this).find(".chat-window").height());
        });
    });
};
serverChat.prototype.render = function (data) {

    for (var i in data) {
        if (!this.messages[i]) {
            this.messages[i] = data[i].messages;
        }
        if (!this.rendered[i]) {
            this.addChat(data, i);
            this.rendered[i] = true;
        }
    }


};