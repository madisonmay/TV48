$(document).ready(function() {
    //Function for populating graph string
    var permitted = "<button class='btn spaced permitted' id='room%room_id%' style='display: none;'>%location%</button>" 
    var restricted = "<button class='btn spaced restricted' id='room%room_id%' style='display: none;'>%location%</button>"
    var entry = "<tr id='%id%' class='hideable'><td style='text-align: center;'>%name%</td> \
                 <td style='text-align: center;'><a href='#' class='show centered hidden-buttons'>Show</a></td> \
                 <td style='text-align: center;'><a href='#' class='show centered hidden-buttons'>Show</a></td></tr>";
    var match = false;

    Array.prototype.sortBy = function(p){
        //sorts an array by property p
        return this.sort(function(a,b){
            return (a[p] > b[p]) ? 1 : (a[p] < b[p]) ? -1 : 0;
        });
    }

    String.prototype.strip = function(){
        //strips all whitespace from string
        return this.replace(/ /g,'');
    }

    function template(string,data){
        return string.replace(/%(\w*)%/g,function(m,key){
          return data.hasOwnProperty(key)?data[key]:"";
        });
    }

    window.lights.sortBy('location');
    window.tenants.sortBy('name');

    //nesting this deeply could be an issue
    for (var i = 0; i < window.tenants.length; i++) {
        var tenant = window.tenants[i]
        var tenant_id = tenant['id'];
        var name = tenant['name'];
        var rooms = tenant['rooms'];
        if (!rooms) {
            rooms = [];
        } else {
            rooms =  rooms.slice(1).slice(0, rooms.length-2).strip().split(',');
            for (var j = 0; j < rooms.length; j++) {
                //string length
                sl = rooms[j].length;
                rooms[j] = rooms[j].slice(1, sl-1);

            }
            console.log(rooms);
        }

        $('#table-body').append(template(entry, {'name': name, 'id': tenant_id}));
        for (var j = 0; j < window.lights.length; j++) {
            var light = window.lights[j];
            match = false;
            for (var k = 0; k < rooms.length; k++) {
                if (parseInt(rooms[k]) == parseInt(light['streamId'])) {
                    match = true;
                }
            }

            if (match) {
                $('#' + tenant_id).children('td:eq(1)').append(template(permitted, 
                    {'location': light['location'], 'room_id': light['streamId']}));   
            } else {
                $('#' + tenant_id).children('td:eq(2)').append(template(restricted, 
                    {'location': light['location'], 'room_id': light['streamId']}));
            }
        }
    }

    //selectively hide and show content to make the interface a bit cleaner
    $('.show').on('click', function() {
        if ($(this).hasClass('hidden-buttons')) {
            $(this).siblings('.btn').each(function() {
                $(this).css('display', 'inline-block');
            });
            $(this).html("Hide")
        } else {
            $(this).siblings('.btn').each(function() {
                $(this).css('display', 'none');
            });
            $(this).html("Show")
        }
        $(this).toggleClass('hidden-buttons');
    })
    
    //make buttons green on hover
    $('.spaced').mouseover(function() {
        $(this).addClass('btn-success')
    });
    $('.spaced').mouseout(function() {
        $(this).removeClass('btn-success')
    });


    function currentRooms(parent_id) {
        //$(this).attr('id').slice(4)
        var user_rooms = []
        $('#' + parent_id).children('td:eq(1)').children('.btn').each(function() {
            user_rooms.push(parseInt($(this).attr('id').slice(4)));
        });
        return user_rooms;
    }
    //toggle between permitted and restricted
    $('.btn').on('click', function() {
        var parent_id = $(this).parent().parent().attr('id');
        console.log(parent_id);
        $(this).toggleClass("restricted");
        $(this).toggleClass("permitted");
        if ($(this).hasClass('permitted')) {
            $('#' + parent_id).children('td:eq(1)').append($(this));
            var user_rooms = currentRooms(parent_id);
            if ($('#' + parent_id).children('td:eq(1)').children('a').hasClass('hidden-buttons')) {
                $('#' + parent_id).children('td:eq(1)').children('a').click();    
            }
            $.post('editPermissions.php', {'id': parent_id, 'rooms': user_rooms}, function(data) {
                console.log(data);
            });
        } else if ($(this).hasClass('restricted')) {
            $('#' + parent_id).children('td:eq(2)').append($(this));
            var user_rooms = currentRooms(parent_id);
            if ($('#' + parent_id).children('td:eq(2)').children('a').hasClass('hidden-buttons')) {
                $('#' + parent_id).children('td:eq(2)').children('a').click();    
            }
            $.post('editPermissions.php', {'id': parent_id, 'rooms': user_rooms}, function(data) {
                console.log(data);
            });
        }
    })


})