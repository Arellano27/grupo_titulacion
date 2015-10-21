jQuery(document).ready(function() {
		

		function drop(ev) {
		    ev.preventDefault();
		    var data = ev.dataTransfer.getData("text");
		    ev.target.appendChild(document.getElementById(data));
		    alert("hola mundo");
		}

		function allowDrop(ev) {
		    ev.preventDefault();
		}

		// function drag(ev) {
		//     ev.dataTransfer.setData("text", ev.target.id);
		// }
	// function ini_events(ele,url) {
 //          ele.each(function (url) {
 //            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
 //            // it doesn't need to have a start or end
 //            var eventObject = {
 //              title: $.trim($(this).text()), // use the element's text as the event title
 //              url: url
 //            };

 //            // store the Event Object in the DOM element so we can get to it later
 //            $(this).data('eventObject', eventObject);

 //            // make the event draggable using jQuery UI
 //            $(this).draggable({
 //              zIndex: 1070,
 //              revert: true, // will cause the event to go back to its
 //              revertDuration: 0  //  original position after the drag
 //            });

 //          });
 //        }

 //        ini_events($('#external-events div.external-event'));
 //
 			var currentMousePos = {
	    x: -1,
	    y: -1
	};
		jQuery(document).on("mousemove", function (event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });

		/* initialize the external events
		-----------------------------------------------------------------*/

		$('#external-events div.external-event').each(function() {

			// store data so the calendar knows to render an event upon drop
			$(this).data('event', {
				title: $.trim($(this).text()), // use the element's text as the event title
				stick: true // maintain when user navigates (see docs on the renderEvent method)
			});

			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});

		});

	$('#calendar').fullCalendar({
		header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
		},
		buttonText: {
            today: 'today',
            month: 'month',
            week: 'week',
            day: 'day'
		},
		editable: true,
		droppable: true,
		// eventReceive: function(event){
		// 		var title = event.title;
		// 		var start = event.start.format("YYYY-MM-DD");
		// 		// $("#eventos_drop").each(function(){
		// 		// 	alert(this.name);
		// 		// });
		// 		// alert(event.id);
		// 		console.info("============");
		// 		console.debug(event);
	 //            var id_evento = $(event).attr("name");
	 //            insertar_eventos(start,id_evento);
		// },
		drop: function(date, allDay){

				var start = date.format("YYYY-MM-DD");

	            var id_evento = $(this).attr("name");
	            insertar_eventos(start,id_evento);
		},
	});
 /* ADDING EVENTS */
        var currColor = "#3c8dbc"; //Red by default
        //Color chooser button
        var colorChooser = $("#color-chooser-btn");
        $("#color-chooser > li > a").click(function (e) {
          e.preventDefault();
          //Save color
          currColor = $(this).css("color");

          //Add color effect to button
          $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
        });

        $("#add-new-event").click(function (e) {
          e.preventDefault();
          //Get value and make sure it is not null

          var val = $("#new-event").val();
          if (val.length == 0) {
            return;
          }

          //Create events
          var event = $("<div />");
          event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event ui-draggable");
          event.html(val);

          var evento_color = val+"|"+currColor;

          $('#external-events').prepend(event);

			crear_evento(evento_color);




          //Add draggable funtionality
          // ini_events(event,'www.google.com');

          //Remove event from text input
          $("#new-event").val("");
        });
});

