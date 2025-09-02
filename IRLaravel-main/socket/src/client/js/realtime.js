$(document).ready(function() {
    var socket = io.connect('http://localhost:3333');

    socket.on('connect', function () {
        $(document).on('click', '#join', function(){
            var room = $('#room').val();

            socket.emit('join_room', {room: room});
        });
        
        $(document).on('click', '#start', function(){
            $(this).attr('disabled', 'disabled');
            
            var room = $('#room').val();
            
            socket.emit('create_pick_turn', {
                root_number: 5,
                number: 5,
                room: room, 
                total_player: 16,
                league_id: 1,                            
                event: 'turn_receive',
                pick_round: 0,    
                show_pick_round: 1,    
                league: [
                    {
                        id: 1,
                        current: true,
                        next: false,
                        player: 4,
                        pick_order: 0,
                        due_next_time: 0,
                        due_next_time_max: 0
                    },
                    {
                        id: 2,
                        current: false,
                        next: true,
                        player: 4,
                        pick_order: 0,
                        due_next_time: 5,
                        due_next_time_max: 5
                    },
                    {
                        id: 3,
                        current: false,
                        next: false,
                        player: 4,
                        pick_order: 0,
                        due_next_time: 10,
                        due_next_time_max: 10
                    },
                    {
                        id: 4,
                        current: false,
                        next: false,
                        player: 4,
                        pick_order: 0,
                        due_next_time: 15,
                        due_next_time_max: 15
                    }
                ]                    
            });
        });
        
        $(document).on('click', '#end', function(){
            $(this).attr('disabled', 'disabled');
            
            var room = $('#room').val();
            var data = JSON.parse($('#data').val());
            
            data.endturn = true;
            socket.emit('end_turn', data);
        });

        socket.on('turn_receive', function (data) {
            $('#end').removeAttr('disabled');
            
            var team = 0;
            var process = '';
//            var dueNextTime = '';
            
            $.each(data.league, function(key, item) {
                if(item.current === true) {
                    team = item.id;                    
                    
                    process += ' <button style="background: green">' + item.id + ' Time: ' + item.due_next_time + '<button> ';
                } else {
                    process += ' <button style="background: red">' + item.id + ' Time: ' + item.due_next_time + '<button> ';
                }
                
//                dueNextTime += 'Team_' + item.id + ' => TIME: ' + item.due_next_time + '   MAX: ' + item.due_next_time_max + "</br>";
            });
            
            $('#data').val(JSON.stringify(data));
            console.log('turn_receive', data);
            if(data.number > 0) {
                $('#count_down').empty().html('Counter: ' + data.number + ' - Team: ' + team + ' - Round: ' + data.show_pick_round + "</br>");
                $('#process').empty().append(process);
//                $('#time').append('Current team: ' + team + ' =>>>>>> ' + dueNextTime + "</br>");                
            } else {
                $('#count_down').empty().html('Loading...' + "</br>");
            }            
        });
        
        socket.on('pick_turn_finish', function (data) {       
            console.log('pick_turn_finish', data);
            $('#count_down').empty().text('Finished');      
        });
        
        socket.on('refresh_ui', function (data) {        
            console.log('refresh_ui', data);
        });
    });

    socket.on('error', function (error) {
        console.log(error);
    });
});
