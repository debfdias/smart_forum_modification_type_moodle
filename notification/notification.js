Notification.requestPermission();
        // request permission on page load
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== "granted"){

    Notification.requestPermission();
    alert(Notification.permission);
  }
});

function notifyMe(name,type,link) {
    if(type=='like'){
     type=" curtiu seu post.";
    }else{
     type=" quer conversar com você no chat.";
    }
  if (!Notification) {
    alert('Desktop notifications not available in your browser. Try Chromium.');
    return;
  }

  if (Notification.permission !== "granted")
    Notification.requestPermission();

  else {

    var notification = new Notification('Notificação Octopus', {
      icon: '../oct_icon.jpg',
      body: name+type,
      tag: name,
    });

    notification.onclick = function () {
      window.open(link);
    };

  }

}

function getContent(user_id)
    {
        var queryString = { 'user_id' : user_id };
        $.get ( 'notification/push_notification.php' , queryString , function ( data )
        {
            var obj = jQuery.parseJSON( data );

            for (var k in obj)
            {
                var comment = "<p>" + obj[k].message + "</p>";
    //            var timestamp = obj[k].timestamp;
                $( '#response' ).append( comment );
                //notifyMe(obj[k].name,"a",obj[k].body);

            }

            // reconecta ao receber uma resposta do servidor
            getContent( user_id );
        });
    }
