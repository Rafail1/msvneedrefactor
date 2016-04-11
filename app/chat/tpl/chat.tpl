<div id="{{id}}" class="chat container">
    {{#server}}
    <i class="chat-remover glyphicon glyphicon-off" onclick="sChat.delete('{{id}}');"></i>
    {{/server}}
    {{#client}}
    <i class="chat-close glyphicon glyphicon-arrow-down"></i>
    {{/client}}
    <div class="chat-logo row">
        <img class="pull-left" src="/assets/img/logo.png">
        <div class="pull-right">
            <span class="status"></span>
        </div>
    </div>
    <div class="scroller row">
        <div class="chat-window">
            {{#client}}
            <p class="server message" data-id="0"><span class="icon-chat glyphicon glyphicon-comment"></span>Здравствуйте! Могу ли я Вам чем-то помочь?</p>
            {{/client}}
            
            {{#messages}}<p class="{{who}} message" data-id="{{id}}"><span class="icon-chat glyphicon glyphicon-comment"></span>{{text}}</p>{{/messages}}
        </div>
    </div>
    <div class="textarea row">
        <i class="fa fa-comment"></i>
        <textarea class="client-window" placeholder="Для отправки нажмите Enter"></textarea>
    </div>
</div>
