var servidor = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2])+'/'+((location.href.split('/'))[3])+'/'+((location.href.split('/'))[4])+'/'+((location.href.split('/'))[5])+'/';
var servidorUrl = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2])+'/'+((location.href.split('/'))[3])+'/';
servidorUrl ='http://localhost/desarrollo/appAcademico/';
function popup_home(action, titulo)
{


    $.ajax({
        type: 'get',
        url: action,
        beforeSend: function( )
        {
            var loading_bar = '<div id="loading-bar-spinner" style="display: none">'
                    + '<div class="spinner-icon"></div>'
                    + '</div>';

            $("body").append(loading_bar);
            $("#loading-bar-spinner").css('display', '');
        },
		success: function(html)
        {
            $( "#loading-bar-spinner" ).remove();
            
             createModalOverBody(titulo, html, 'lg');
            //setTimeout(function(){ $(window).trigger('resize'); }, 100);
        }
    });
    event.preventDefault(); 
}

    function alert_bootstrap( id, title, msg, size, type, functions )
    {
        var size = size || "lg";
        var modal_size = '';
        var button_size = '';
        var buttons = '';
        var close = '';
        var functions_buttons = functions || false;
        
        switch(size)
        {
            case 'xsm':
                modal_size = 'modal-xsm';
                button_size = 'btn-sm';
                break;

            case 'sm':
                modal_size = 'modal-sm';
                button_size = 'btn-sm';
                break;

            case 'md':
                modal_size = 'modal-md';
                button_size = 'btn-md';
                break;

            case 'lg':
                modal_size = 'modal-lg';
                button_size = 'btn-md';
                break;

            case 'xlg':
                modal_size = 'modal-xlg';
                button_size = 'btn-md';
                break;

            default:
                modal_size = 'width-'+size;
                button_size = 'btn-md';
                break;
        }

        switch(type)
        {
            case 'alert':

                if(functions_buttons)
                {
                    close = '<button type="button" class="close" onclick="'+functions_buttons[0]+'">'
                                + '<img class="close-alert" src="'+servidorUrl+'web/images/close.png" />'
                            + '</button>';
                    
                    buttons = '<div class="col-xs-4 col-xs-offset-4">'
                                + '<button type="button" class="btn btn-miclaro btn-miclaro-red '+button_size+'" onclick="'+functions_buttons[0]+'">'
                                    + 'Aceptar'
                                    //+ '<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>'
                                + '</button>'
                            + '</div>';
                }
                else
                {
                    close = '<button type="button" class="close"  data-dismiss="modal" onclick="close_modal2(\''+id+'\')">'
                                + '<img class="close-alert" src="'+servidorUrl+'web/images/close.png" />'
                            + '</button>';
                    
                    buttons = '<div class="col-xs-4 col-xs-offset-4">'
                                + '<button type="button" class="btn btn-miclaro btn-miclaro-red '+button_size+'" onclick="close_modal2(\''+id+'\')">'
                                    + 'Aceptar'
                                    //+ '<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>'
                                + '</button>'
                            + '</div>';
                }

                break;



            case 'confirm':

                if(functions_buttons)
                {
                    if(functions_buttons.length == 2)
                    {
                        close = '<button type="button" class="close" onclick="'+functions_buttons[0]+'">'
                                    + '<img class="close-alert" src="'+servidorUrl+'web/images/close.png" />'
                                + '</button>';
                    
                        buttons = '<div class="col-xs-6 col-xs-offset-3">'
                                    +'<div class="col-xs-5">'
                                         +'<button type="button" class="btn btn-miclaro btn-miclaro-gris '+button_size+'" onclick="'+functions_buttons[1]+'">'
                                             + 'Aceptar'
                                             //+ '<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>'
                                         +'</button>'
                                    + '</div>'
                                    +'<div class="col-xs-5 col-xs-offset-2">'
                                         +'<button type="button" class="btn btn-miclaro btn-miclaro-red '+button_size+'" onclick="'+functions_buttons[0]+'">'
                                             + 'Cancelar'
                                             //+ '<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>'
                                         +'</button>'
                                    + '</div>'
                                 + '</div>';
                    }
                    else
                    {
                        close = '<button type="button" class="close" onclick="close_modal2(\''+id+'\')">'
                                    + '<img class="close-alert" src="'+servidorUrl+'web/images/close.png" />'
                                + '</button>';
                        if(id==='ingresonotas'){
                        buttons = '<div class="col-xs-6 col-xs-offset-3">'
                                  + '<div class="col-xs-5">'
                                        +'<button type="submit" class="btn btn-miclaro btn-miclaro-red '+button_size+'" onclick="'+functions_buttons[0]+'">'
                                            + 'Aceptar'
                                        +'</button>'
                                  + '</div>'
                                  + '<div class="col-xs-5 col-xs-offset-2">'
                                        +'<button type="button" class="btn btn-miclaro btn-miclaro-gris '+button_size+'" onclick="close_modal(\''+id+'\')">'
                                            + 'Cancelar'
                                        +'</button>'
                                  + '</div>'
                                + '</div>';
                    }else{
                         buttons = '<div class="col-xs-6 col-xs-offset-3">'
                                  + '<div class="col-xs-5">'
                                        +'<button type="submit" class="btn btn-miclaro btn-miclaro-red '+button_size+'" onclick="'+functions_buttons[0]+'">'
                                            + 'Aceptar'
                                        +'</button>'
                                  + '</div>'
                                  + '<div class="col-xs-5 col-xs-offset-2">'
                                        +'<button type="button" class="btn btn-miclaro btn-miclaro-gris '+button_size+'" onclick="close_modal2(\''+id+'\')">'
                                            + 'Cancelar'
                                        +'</button>'
                                  + '</div>'
                                + '</div>';
                         }
                    }
                }

                break;
        }

        if(type == 'advertises')
        {
            if(close == '')
            {
                close = '<button type="button" class="close" onclick="close_modal2(\''+id+'\')">'
                            + '<img class="close-alert" src="'+servidorUrl+'web/images/close.png" />'
                        + '</button>';
            }
            
            var alert = '<div id="modal-'+id+'" class="modal fade" tabindex="-1" data-focus-on="input:first" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">'
                            + '<div class="modal-dialog '+modal_size+'">'
                                + '<div class="modal-content">'
                                    + '<div class="modal-header modal-header-alert">'
                                        + close
                                        + '<span class="ico-title ico-title-medium ico-title-alert"></span>'
                                        + '<h5 class="modal-title"> '+ title +' </h5>'
                                    + '</div>'
                                    + '<div class="modal-body without-padding">'
                                        + msg
                                    + '</div>'
                                + '</div>'
                            + '</div>'
                        + '</div>';
        }
        else
        {
            if(close == '')
            {
                close = '<button type="button" class="close" onclick="close_modal2(\''+id+'\')">'
                            + '<img class="close-alert" src="'+servidorUrl+'web/images/close.png" />'
                        + '</button>';
            }
		if(id==='ingresonotas'){	
            var alert = '<div id="modal-'+id+'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="false"  aria-hidden="true">'
                            + '<div class="modal-dialog '+modal_size+'">'
                                + '<div class="modal-content">'
                                    + '<div class="modal-header modal-header-alert">'
                                        + close
                                        + '<span class="ico-title ico-title-medium ico-title-alert"></span>'
                                        + '<h5 class="modal-title"> '+ title +' </h5>'
                                    + '</div>'
                                    + '<div class="modal-body">'
                                        + msg
                                    + '</div>'
                                    + '<div class="modal-footer">'
                                        + buttons
                                    + '</div>'
                                + '</div>'
                            + '</div>'
                        + '</div>';
            }else {
                var alert = '<div id="modal-'+id+'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">'
                            + '<div class="modal-dialog '+modal_size+'">'
                                + '<div class="modal-content">'
                                    + '<div class="modal-header modal-header-alert">'
                                        + close
                                        + '<span class="ico-title ico-title-medium ico-title-alert"></span>'
                                        + '<h5 class="modal-title"> '+ title +' </h5>'
                                    + '</div>'
                                    + '<div class="modal-body">'
                                        + msg
                                    + '</div>'
                                    + '<div class="modal-footer">'
                                        + buttons
                                    + '</div>'
                                + '</div>'
                            + '</div>'
                        + '</div>';
            }
        }

        return alert;
    }
	
	 function close_modal2(form)
    {
        $( "#menu" ).css('z-index','9999');
		
        var n = form.indexOf("internal");
        
        if(n < 0)
        {
            $( "body" ).css("overflow-y", "scroll");
            $( "body" ).css("padding-right","");
            $("#alert").css("padding-right","");
        }
        
        $( "#modal-"+form ).modal('hide');
        $( "#modal-"+form ).remove();
		$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
    }
    
    
            function close_modal(form)
    {
        
        $( "#menu" ).css('z-index','9999');
		
        var n = form.indexOf("internal");
        
       /* if(n < 0)
        {
            $( "body" ).css("overflow-y", "scroll");
            $( "body" ).css("padding-right","");
            $("#alert").css("padding-right","");
        }*/
        
        $( "#modal-"+form ).modal('hide');
        $( "#modal-"+form ).remove();
    }
    
         function adjustModalMaxHeightAndPosition()
    {
        $('.modal').each(function()
        {
            if($(this).hasClass('in') == false)
            {
                $(this).show();
            };
            
            var contentHeight = $(window).height() - 60;
            var headerHeight = $(this).find('.modal-header').outerHeight() || 2;
            var footerHeight = $(this).find('.modal-footer').outerHeight() || 2;
            
            $(this).find('.modal-content').css(
            {
                'max-height': function ()
                {
                    return contentHeight;
                }
            });

            $(this).find('.modal-body').css(
            {
                'max-height': function ()
                {
                    return (contentHeight - (headerHeight + footerHeight));
                }
            });

            $(this).find('.modal-dialog').css(
            {
                'margin-top': function ()
                {
                    return -($(this).outerHeight() / 2);
                },
                'margin-left': function ()
                {
                    return -($(this).outerWidth() / 2);
                }
            });
            
            if($(this).hasClass('in') == false)
            {
                $(this).hide();
            };
        });
    };
    
    
    function destroy_modal(form)
    {
        $("body").removeClass("modal-open");
        $("body").css("padding-right","");
        $("#alert").css("padding-right","");
        $("#modal-"+form ).modal('hide');
    }