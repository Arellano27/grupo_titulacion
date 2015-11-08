/**************************************************************************/
    /************** FUNCI�N PARA VALIDAR TODO TIPO DE FORMULARIO **************/
    /**************************************************************************/
  
               function send_form(form, removeModalBody)
                { 
                    formSubmitted = form;
                   
		            var modal_size = '';
                            var form_user = $( "#form-"+form );
                            var form_data = form_user.serialize();
                            var form_action = form_user.attr('action');
      
                            
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
             
       

