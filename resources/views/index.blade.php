<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('assets/vendors/iconfonts/mdi/css/materialdesignicons.css') }}">
        <meta property="csrf-token" content="{{ csrf_token() }}"/>

        <!-- Styles -->
        
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            
                <div class="top-right links">
                    <a href="#">Home</a>
                </div>
           

            <div class="content">
                    <div class="card grey lighten-3 chat-room">
                            <div class="card-body">
                          
                              <!-- Grid row -->
                              <div class="row px-lg-2 px-2">
                                <!-- Grid column -->
                                <div class="col-md-6 col-xl-8 pl-md-3 px-lg-auto px-0">
                          
                                  <div class="chat-message">
                          
                                    <ul class="list-unstyled chat">
                                      <div id="lead-notify-container">

                                      </div>
                                      
                                      
                                      <li class="white">
                                        <div class="form-group basic-textarea">
                                          <textarea class="form-control pl-2 my-0" id="exampleFormControlTextarea2" rows="3" placeholder="Type your message here..."></textarea>
                                        </div>
                                      </li>
                                      <button type="button" class="btn btn-info btn-rounded btn-sm waves-effect waves-light float-right send-message-button">Send</button>
                                    </ul>
                          
                                  </div>
                          
                                </div>
                                <!-- Grid column -->
                          
                              </div>
                              <!-- Grid row -->
                          
                            </div>
                          </div>

            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="{{ URL::asset('/js/socket.io.js') }}"></script>
        <script src="{{ URL::asset('/js/mustache.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script type="text/template" id="each-message-template">
            <li class="d-flex justify-content-between mb-4">
                <img src="https://secure.gravatar.com/avatar/?s=50&d=mm&r=g" alt="avatar" class="avatar rounded-circle mr-2 ml-lg-3 ml-0 z-depth-1" height="50" width="50">
                <div class="chat-body white p-3 ml-2 z-depth-1">
                    <div class="header">
                    <strong class="primary-font">@{{sender}}</strong>
                    <small class="pull-right text-muted"><i class="far fa-clock"></i> @{{time}}</small>
                    </div>
                    <hr class="w-100">
                    <p class="mb-0">
                        @{{message}}
                    </p>
                </div>
            </li>
        </script>
        
        <script>
            $(function() {
                const _token = document.head.querySelector("[property=csrf-token]").content;

                $(".send-message-button").click( function(){
                    if ($('#exampleFormControlTextarea2').val() != '') {
                        $.ajax({
                            url: '{!! route('sendMessage') !!}',
                            dataType: 'json',
                            data: {
                                message: $('#exampleFormControlTextarea2').val(),
                                roomId: '{{$roomId}}',
                                _token: _token
                            },
                            type:'post',
                            success: function(response) {
                                if (response == 'success') {
                                    console.log('Messge Sent Successfully');
                                    $('#exampleFormControlTextarea2').val('');
                                } else {
                                    
                                }
                            }
                        });
                    }
                });

                var socket = io.connect('{{env('nodeServer')}}', {
                    resource: 'B/socket.io',
                    'force new connection': true
                });
                const userRoom = "private:room:" + '{{$roomId}}'
                
                socket.on('connect', function (data) {
                    socket.emit('join-private-room', userRoom);
                });
                socket.on('private-room-message', function (data) {
                    console.log(data.params)
                    $("#lead-notify-container").append(
                            Mustache.to_html($("#each-message-template").html(), {
                                        message: data.params.message,
                                        sender: data.params.sender,
                                        time : moment(data.params.time)
                                    }
                            ));
                });

                
            });

        </script>
    </body>
</html>
