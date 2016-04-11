/* global Mustache, Chat */

function clientChat(client_id) {
    this.messages = [];
    this.client_id = client_id;
    this.chat_url = "/app/chat/controller.php";
    this.lastNotify = 0;
    this.who = "client";
    this.lastTime = 0;
    this.notifySoundMessage = new Audio("/app/chat/audio/client-message.mp3");
    this.notifySoundOpen = new Audio("/app/chat/audio/open.mp3");
    this.timeout = null;
}

clientChat.prototype = Object.create(Chat.prototype);
clientChat.prototype.constructor = clientChat;

clientChat.prototype.start = function () {
    var self = this;
    $.get('/app/chat/tpl/chat.tpl', function (template) {
        var rendered = Mustache.render(template, {id: self.client_id, client: true});
        $("#chat_raf").html(rendered);
        self.addEventListeners();
        self.chatWindow = $("#" + self.client_id);
        self.getArchive({action:"getArchive", client_id:self.client_id});
    });

    $('.scroller').each(function () {
        $(this).scrollTop($(this).find(".chat-window").height());
    });

};
clientChat.prototype.addEventListeners = function () {
    $(".chat-close").on("click", function () {
        $(this).closest("#chat_raf").removeClass("activated");
    })
    $(".chat-logo").on("click", function () {
        clearTimeout(this.timeout);
        $("#chat_raf").addClass("activated activated_earled");
    })
};

clientChat.prototype.check = function () {
    var self = this;

    var data = {action: 'check', client_id: this.client_id, lastTime: this.getLastTime()[this.client_id]};
    this.request(data, function () {
        self.answer(this);

        setTimeout(function () {
            self.check();
        }, 100);
    });
};

clientChat.prototype.answer = function (data) {
    for (var i in data.messages) {
        this.addMessage(data.messages[i], this.client_id);
        this.notify(this.notifySoundMessage);
    }
    this.setOnline(data.online, this.client_id);

};

clientChat.prototype.render = function (data) {
    for (var i in data.messages) {
        this.addMessage(data.messages[i], this.client_id);
    }
    this.setOnline(data.online, this.client_id)
};