function cargandoSitio(opciones, mensaje, duracion) {
  $("#cargandoSitio").show();
   if(opciones!=null) {
    var opts = opciones;
  }
  else {
    var opts = {
      lines: 13, // The number of lines to draw
      length: 11, // The length of each line
      width: 5, // The line thickness
      radius: 17, // The radius of the inner circle
      corners: 1, // Corner roundness (0..1)
      rotate: 0, // The rotation offset
      color: '#FFF', // #rgb or #rrggbb
      speed: 1, // Rounds per second
      trail: 60, // Afterglow percentage
      shadow: false, // Whether to render a shadow
      hwaccel: false, // Whether to use hardware acceleration
      className: 'spinner', // The CSS class to assign to the spinner
      zIndex: 2e9, // The z-index (defaults to 2000000000)
      top: 'auto', // Top position relative to parent in px
      left: 'auto' // Left position relative to parent in px
    };
  }
  var target = $("#loaderSitio");
  var spinner = new Spinner(opts).spin(target);
  if(mensaje==null){
    mensaje = "Cargando, por favor espere";
  }
  if(duracion==null) {
    duracion=null;
  }
  var notification = iosOverlay({
    text: mensaje,
    duration: duracion,
    spinner: spinner
  });

  return notification;
}

function sitioCargado(notificacion){
   if(notificacion!=null){
      notificacion.hide();
   }
   $("#cargandoSitio").fadeOut(200);
}

function lanzarModal(titulo, textoCuerpo, textoBoton) {
  var myModal = $('#myModalGeneral');
  
  myModal.modal('hide');
  
  modalTitle = myModal.find('#modal-title-general');
  modalTitle.html(titulo);
  
  modalBody = myModal.find('#modal-body-general');
  modalBody.html(textoCuerpo);
  
  modalBody = myModal.find('#modal-button-general');
  modalBody.html(textoBoton);
  
  myModal.modal('show');
}

    function mostrarAlumnos(id,section,cuenta){
       
        var obj_data = {"parametro1" : id, "parametro2" : "valor2"};
        $.ajax
        ({
            type: 'post',
            url: section,
            data: obj_data,
            dataType: "json",
            beforeSend: function( )
            { $( "#content-"+id ).html("Cargando, espere por favor...");
                $( "#content-"+id ).html( "<div id='loading-bar-spinner-relative'><div class='spinner-icon'></div></div>" );
            },
            success: function(data) 
            {
                console.log(id);
		if(data.error === true){
                    $( "#loading-bar-spinner-relative" ).remove();
                    msg_alert = alert_bootstrap( id, 'Atenci&oacute;n', data.msg, 'sm', 'alert');
                    $( "#content-"+id ).append( msg_alert );
                    $('#modal-'+id).modal('show');
                }else{
                    $( "#content-"+id ).html(data.html);
                    }
            }
        });
    }
	
	 function confirm(form, item)
    {    
        var msg_alert = "", msg = "", functions = "";
        
        $( "#modal-"+form ).remove();
        
        switch(form)
        {
            case 'ingresonotas':
                
                alert("55");
                msg = "<center>"
                        + " Esta Seguro que desea guardar sus notas?"
                      +"</center>";
                
                functions = ["formularioCargar('"+form+"','"+item+"')"];
                 msg_alert= alert_bootstrap( form, 'Confirmaci&oacute;n', msg, 'md', 'confirm', functions);
                
                break;
        }
             //alert(msg_alert+"22");
        $( "#form-ingresonotas" ).append( msg_alert );
        $('#modal-'+form).modal('show');
        
        return false;
    }
    
           function formularioCargar(id,section){
	
        
          close_modal(id);
		//var obj_data = {'tipo':$("#tipo_agendamiento").val()};
                var obj_data = {'tipo':"hola"};
                 
		$.ajax
        ({
            type: 'post',
            url: section,
            data: obj_data,
            dataType: "html",
            beforeSend: function( )
            {
                $( "#content-"+id ).html( "<div id='loading-bar-spinner-relative'><div class='spinner-icon'></div></div>" );
            },
            success: function(data) 
            {
				$( "#content-"+id ).html( data );
            }
        });
        return false;
	} 
        
   function send_form(form, removeModalBody)
    { 
        formSubmitted = form;
        
        $.validator.setDefaults
        ({
            submitHandler: function()
            { 
				var mensaje_cedula = '';
				
				mensaje_cedula = valida_cedula();
			
				if(mensaje_cedula != '')
				{
					$( "#modal-"+form ).remove();
					$( "#content-"+form ).css('display','');

					msg_alert = alert_bootstrap( form, 'Atenci&oacute;n', mensaje_cedula, 'sm', 'alert');
					$( "#form-"+form ).append( msg_alert );
					$('#modal-'+form).modal('show');
				}
				else
				{
					var modal_size = '';
					var form_user = $( "#form-"+form );
					var form_data = form_user.serialize();
					var form_action = form_user.attr('action');
					var deleteModalBody = removeModalBody || 'S';

					var loading_bar = '<div id="loading-bar-spinner">'
										+ '<div class="spinner-icon"></div>'
									+ '</div>';
					
					var backdrop_modal = '<div id="modal-'+form+'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">'
											+ '<div class="modal-dialog '+modal_size+'">'
											+ '</div>'
										+ '</div>';
									
					var msg_alert = '';
					
					$.ajax
					({
						type: 'post',
						url: form_action,
						data: form_data,
						dataType: "json",
						beforeSend: function( )
						{
							if(deleteModalBody == 'S')
							{
								deleteModalOverBody();
							}
							
							$( "#modal-"+form ).remove();
							
							if($("#content-main-"+form).length)
							{
								$( "#content-main-"+form ).append( backdrop_modal );
								$( "#content-main-"+form ).append( loading_bar );
							}
							else
							{
								$( "#content-"+form ).append( backdrop_modal );
								$( "#content-"+form ).append( loading_bar );
							}
							
							$('#modal-'+form).modal('show');
						},
						success: function(data) 
						{
							modal_size = data.btnSize || 'sm';
							
							$( "#modal-"+form ).remove();

							if(data.error) //SI EXISTE ALGÚN ERROR
							{
								$( "#content-"+form ).css('display','');

								msg_alert = alert_bootstrap( form, 'Atenci&oacute;n', data.msg, modal_size, 'alert');
								$( "#form-"+form ).append( msg_alert );
								$('#modal-'+form).modal('show');
                                                                
							}
							else if(data.anotherDivError) //SI EXISTE ALGÚN ERROR
							{
								$( "#content-"+form ).css('display','');

								msg_alert = alert_bootstrap( form, 'Atenci&oacute;n', data.msg, modal_size, 'alert');
								$( "#content-main-"+form ).append( msg_alert );
								$('#modal-'+form).modal('show');
							}
							else if(data.redirect) //SI SE REQUIERE UNA REDIRECCIÓN HA ALGUNA PÁGINA ESPECÍFICA
							{	
								location.href = data.msg;
							}
							else if(data.withoutModal) //CARGAR EL CONTENIDO SIN MOSTRAR MODALS
							{	
								$( "#content-"+form ).html( data.html );
							}
							else if(data.modalOverBody) //MODAL SOBRE EL CUERPO DE LA PAGINA
							{
								var title = data.title;
								var content = data.html;
								var type = data.typeModalOverBody || 'alert';
								var size = data.sizeModalOverBody || 'md';

								createModalOverBody(title, content, size, type);
							}
							else if(data.isHtml) //CARGAR EL CONTENIDO ANTES DE MOSTRAR EL MODAL
							{	
								msg_alert = alert_bootstrap( form, 'Confirmaci&oacute;n', data.msg, modal_size, 'alert');
								
								$( "#content-"+form ).html( data.html );
								$( "#form-"+form ).append( msg_alert );
								$('#modal-'+form).modal('show');
							}
							else //PRIMERO MOSTRAR EL MODAL Y LUEGO RECARGA EL CONTENIDO AL CERRAR EL MODAL
							{
								if(data.withFunction)
								{
								   functions = [data.function];
								}
								else
								{
								   functions = ["reload_content('"+form+"', '"+ servidor + data.section +"')"]; 
								}
								
								msg_alert = alert_bootstrap( form, 'Confirmaci&oacute;n', data.msg, modal_size, 'alert', functions);

								$( "#form-"+form ).append( msg_alert );
								$('#modal-'+form).modal('show');
							}
							
							$( "#loading-bar-spinner" ).remove();
						},
						error: function()
						{
							$( "#loading-bar-spinner" ).remove();
							$( "#modal-"+form ).remove();
							
							var title = 'Error';
							var content = 'Nos encontramos en mantenimiento de nuestros servidores, por favor intenta nuevamente m&aacute;s tarde.';
							var type = 'alert';
							var size = 'sm';

							createModalOverBody(title, content, size, type);
						}
					});
				}
            }
        });

        $().ready(function()
        {
            $("#form-"+form).validate();
        });
        
        return false;
    }