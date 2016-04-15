/* global Mustache */

function Chat() {}

Chat.prototype = {
    constructor: Chat(),
    messages : [],
    chat_url : "/app/chat/controller.php",
    lastNotify : 0,
    
    request: function (data, callbalck) {
        if(!data) {
            console.error("No data");
            return;
        }
        data.who = this.who;
        $.ajax({
            url: this.chat_url,
            data: data,
            type: "post",
            success: function (res) {
                if (!res)
                    return;
                res = JSON.parse(res);
                if (res) {
                    callbalck.apply(res);
                }
            },
            error: function (res) {
                console.log(res);
            }
        });
    },
    
    notify: function (notifySound) {
        var now = new Date().getTime();
        if (now - this.lastNotify > 2000) {
            this.lastNotify = new Date().getTime();
            notifySound.play();
        }
    },
   
    getArchive: function (data) {
        var self = this;
        this.request(data, function () {
            self.render(this);
            self.check();
        });
    },
    getLastTime : function () {
        var result = {};
        for (var i in this.messages) {
            if (this.messages[i].length) {
                result[i] = this.messages[i][this.messages[i].length - 1].time;
            } else {
                result[i] = 0;
            }
        }

        return result;
    },

    send: function (message, id) {
        if (!message){
            return;
        }
        
        var self = this;
        this.request({action:"send", message:message, client_id: id}, function () {
            self.addMessage(this, id);
        });
    },
   
    setOnline: function (online, id) {
        var el = $("#" + id + " .status");
        if (online) {
            el.text("online");
            el.addClass("online");
        } else {
            el.text("offline");
            el.removeClass("online");
        }
    },
    newMessage: function (message) {
        return "<p class='" + message.who + " message' data-id='" + message.id + "'><span class='icon-chat glyphicon glyphicon-comment'></span>" + message.text + "</p>";
    },
    
    addMessage: function (message, id) {
        if(!this.messages[id]) {
            this.messages[id] = [];
        }
        this.messages[id].push(message);
        var el = $("#"+id+" .chat-window");
        el.append(this.newMessage(message));
        el.closest(".chat").find(".scroller").scrollTop(el.height());
        if ($("#raf_chat").length && $("#raf_chat").hasClass("activated_earled") && !$("#raf_chat").hasClass("activated")) {
            $("#raf_chat").addClass("activated");
        }
    }
};