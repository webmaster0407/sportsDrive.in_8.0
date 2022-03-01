<!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>S</b>AP</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>SportsDrive</b> Panel</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
       {{-- <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown dropdown-notifications" style="display: block;">
                    <a href="#notifications-panel" class="dropdown-toggle" data-toggle="dropdown">
                        <i data-count="0" class="glyphicon glyphicon-bell notification-icon"></i>
                    </a>

                    <div class="dropdown-container">
                        <div class="dropdown-toolbar">
                            <h3 class="dropdown-toolbar-title">Notifications (<span class="notif-count">0</span>)</h3>
                        </div>
                        <ul class="dropdown-menu">
                        </ul>
                        <div class="dropdown-footer text-center">
                            <a href="/administrator/list-notifications">View All</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>--}}
    </nav>

{{--
<script src="//js.pusher.com/3.1/pusher.min.js"></script>
<script type="text/javascript">
    var notificationsWrapper   = $('.dropdown-notifications');
    var notificationsToggle    = notificationsWrapper.find('a[data-toggle]');
    var notificationsCountElem = notificationsToggle.find('i[data-count]');
    var notificationsCount     = parseInt(notificationsCountElem.data('count'));
    var notifications          = notificationsWrapper.find('ul.dropdown-menu');

    if (notificationsCount <= 0) {
        notificationsWrapper.hide();
    }

    var pusher = new Pusher('7e8d15d941bd6d52054d', {
        cluster: "ap2",
        encrypted: true
    });
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;
    // Subscribe to the channel we specified in our Laravel Event
    var channel = pusher.subscribe('status-liked');

    // Bind a function to a Event (the full Laravel class)
    channel.bind('App\\Events\\StatusLiked', function(data) {
        var existingNotifications = notifications.html();
        var avatar = Math.floor(Math.random() * (71 - 20 + 1)) + 20;
        var newNotificationHtml = `
          <li class="notification active">
              <div class="media">
                <div class="media-left">
                  <div class="media-object">
                    <img src="https://api.adorable.io/avatars/71/`+avatar+`.png" class="img-circle" alt="50x50" style="width: 50px; height: 50px;">
                  </div>
                </div>
                <div class="media-body">
                  <strong class="notification-title">`+data.message+`</strong>
                  <!--p class="notification-desc">Extra description can go here</p-->
                </div>
              </div>
          </li>
        `;
        notifications.html(newNotificationHtml + existingNotifications);
        notificationsCount += 1;
        notificationsCountElem.attr('data-count', notificationsCount);
        notificationsWrapper.find('.notif-count').text(notificationsCount);
        notificationsWrapper.show();
        console.log(data.visitDetails);
        if(data.type == "visitor_online" && data.visitDetails){
               var newVisitorsData = "<tr role=\"row\" class=\"odd\" id='tr_"+data.visitDetails.id+"'>\n" +
                   "                              <td><a href=\"/administrator/visitors-details/"+data.visitDetails.id+"\">"+data.visitDetails.ip_address+"</a></td>\n" +
                   "                              <td>"+data.visitCustName+"</td>\n" +
                   "                               <td>"+data.visitDetails.city+"</td>\n" +
                   "                              <td>"+data.visitDetails.region+"</td>\n" +
                   "                              <td>"+data.visitDetails.latitude+"</td>\n" +
                   "                              <td>"+data.visitDetails.longitude+"</td>\n" +
                   "                              <td>"+data.visitDetails.countryName+"</td>\n" +
                   "                              <td>"+data.visitDetails.created_at+"</td>\n" +
                   "                              <td>"+data.visitDetails.updated_at+"</td>\n" +
                   "                          </tr>"
            var visitorsWrapper   = $('#visitorsTable');
            var visitors          = visitorsWrapper.find('tbody');
            var existingVisitors = visitors.html();
            visitors.html(newVisitorsData + existingVisitors);
            $("#tr_"+data.visitDetails.id).effect("highlight", {}, 30000);
        }
    });
</script>--}}
