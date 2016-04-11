<?php if (filter_input(INPUT_GET, "pass", FILTER_VALIDATE_INT) !== 748159263) {
    die();
}
?>
<html>
    <head>
        <title>Chat</title>
        <meta charset="utf8">
         <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet"> 
         <link href="/app/chat/style/style.css" rel="stylesheet">
        <script src="/assets/js/libs/jquery-1.11.0.min.js"></script>
        <script src="/assets/js/mustache.js"></script>
        <script src="/app/chat/js/Chat.js"></script>
        <script src="/app/chat/js/ServerChat.js"></script>
        <link href="/assets/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    </head>
    <body>
        <div id="chats" class="serverChat"></div>
        
        <script>
            function sendServerMessage(id) {
                if(!id || !$("#"+id+" .client-window").length) return;
                var client = $("#"+id+" .client-window"),
                server = $("#"+id+" .chat-window");
                sChat.send(client.val(), id);
                client.val("");
            }
            $(document).on("keyup", function(e){
                if(e.keyCode === 13 && e.target.className === "client-window") {
                    var id = $(e.target).closest(".chat").attr("id");
                    sendServerMessage(id);
                };
            })
            
            var sChat = new serverChat();
            sChat.start();
            
        </script>
        
    </body>
</html>