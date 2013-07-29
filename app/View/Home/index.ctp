<style>
    svg {
        margin-left: -18px;
    }
</style>

<script>

    $(document).ready(function() {
        //Handling chat window
        //will eventually need to expand to other firebase accounts
        window.dataRef = new Firebase('https://tv48.firebaseio.com/');
        window.timer_id = 0;
        window.notepad_open = false;

        window.dataRef.limit(15).on('child_added', function (snapshot) {
          var message = snapshot.val();
          $('<div/>').text(message.text).prepend($('<b/>')
            .text(message.name+': ')).appendTo($('#footer'));
          $('#footer')[0].scrollTop = $('#footer')[0].scrollHeight;
        });

        //special case for IE
        if (Function('/*@cc_on return document.documentMode===10@*/')()){
          if ($(window).innerWidth() < 1200) {
            $('#footer-tab').animate({'bottom': '+=15'}, 100);
            $('#footer').animate({'bottom': '+=15'}, 100);
          }
        }

        $('#footer-tab').click(function() {
          //open up chat window
          if (window.notepad_open) {
            $(this).animate({'bottom': '-=510'}, 1000);
            $('#footer').animate({'bottom': '-=510'}, 1000);
            $('#input').animate({'bottom': '-=510'}, 1000);
            window.notepad_open = false;
          } else {
            $(this).animate({'bottom': '+=510'}, 1000);
            $('#footer').animate({'bottom': '+=510'}, 1000);
            $('#input').animate({'bottom': '+=510'}, 1000);
            window.notepad_open = true;
          }
        });

        $('#input').keypress(function (e) {
          //bind enter key to submit message
          if (e.keyCode == 13) {
            var name = '<?php echo $this->Session->read("Auth.User.full_name"); ?>';
            var text = $('#input').val();
            window.dataRef.push({name:name, text:text});
            $('#input').val('');
          }
        });
        //End chat window code
    })
        
</script>
<?php if ($this->Session->read('Auth.User') && in_array('landlord', $this->Session->read('User.roles'))): ?>

    <div class="row-fluid">
        <div class="span3">
            <a href='/sensors/lighting'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='/sensors/heating'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='/sensors/electricity'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        
        <div class="span3">
            <a href='/home/manage'><div class="border">
                <div id="management" class="circle">
                    <div class="opaque">
                        <div class="label-text">Manage</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->Session->read('Auth.User') && !in_array('landlord', $this->Session->read('User.roles'))): ?>
    <div class="row-fluid">
        <div class="span4">
            <a href='/sensors/lighting'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span4">
            <a href='/sensors/heating'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span4">
            <a href='/sensors/electricity'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!$this->Session->read('Auth.User')): ?>
    <style>
        body {
            margin: 0px;
            padding: 0px;
            min-width: 900px !important;
        }

        html {
            margin: 0px;
            padding: 0px;
            min-width: 900px !important;
        }

        .cover-bg {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .motto {
            border-radius: 20px; 
            height: 300px; 
            width: 80%; 
            background-color: #ddd; 
            opacity: 1; 
            color: #333; 
            font-size: 90px; 
            text-align: center;
        }

        .third {
            display: inline-block;
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
            width: 33%;
            margin-left: 0;
            margin-right: 0;
        }

        .span_content {
            display: block;
            position: relative;
            left: 33.3333%;
            margin-left: -50px;
            width: 250px;
            height: 150px;
            background-color: #46a546;
            border-radius: 10px;
            color: #eee;
            font-size: 50px;
        }

        .span_content a:hover {
            color: #eee !important;
        }

        .minh {
        }
    </style>
    <script>
        if ($(window).innerWidth < 900) {
            setTimeout(function() {$('.cover_bg').css('margin-top', '-75px');}, 10);
        }
    </script>
    <div class='cover_bg' style='background-color: #46a546; height: 500px; width: 100%;'>
        <div class='offset' style='height: 90px;'>
        </div>
        <div class='centered motto'>
        <div id='placeholder' style='height: 50px; margin: 0; padding: 0'>
        </div style='margin: 0; padding: 0;'>

            CORE: COOPkot
            <br>
            <div style='font-size: 50px;'>
            Truly Sustainable Student Housing
            </div>
        </div>
    </div>
    <div>
        <div class="third">
            <div class='span_content'>
            <div class='offset' style='height: 45px;'></div>
                <a href='http://www.thinkcore.be/coopkot.php'>About</a>
                <p class='minh'>&nbsp</p>
            </div>
        </div>
        <div class="third">
            <div class='span_content'>
            <div class='offset' style='height: 45px;'></div>
                <a href='#'>Demo</a>
                <p class='minh'>Coming Soon</p>
            </div>
        </div>
        <div class="third">
            <div class='span_content'>
            <div class='offset' style='height: 45px;'></div>
                <a href='http://www.thinkcore.be/'>CORE</a>
                <p class='minh'>&nbsp</p>
            </div>
        </div>
    </div>
<?php endif; ?>


<!-- <img src='images/left.png' style='width: 5%; float: left;'></img> -->
<!-- <div id='chart' style='height: 350px; width: 85%; display: block; margin-right: auto; margin-left: auto; display: block; float: none;'>
    <svg></svg>
</div>
 -->
<?php if ($this->Session->read('Auth.User')): ?>
    <div id='footer-tab'>
    notepad
    </div>
    <div id="footer">
    </div>
    <input id='input' placeholder='Type message and press enter to send.'>
<?php endif; ?>