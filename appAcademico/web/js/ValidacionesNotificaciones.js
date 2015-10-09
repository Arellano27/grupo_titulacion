/**************************************************************************/
/************** Valida que inputs no esten vacios **************/
/**************************************************************************/
 
 $(document).ready(function(){
     
     $("#Enviar").click(function (){
          
          var Tipo = $("#TipoMensaje").val();
          var Asunto = $("#Asunto").val();
          var Mensaje = $("#mensaje").val();
          
          if(Tipo == 0){
              alert("Seleccione Un tipo de mensaje!");            
              return false
          }
          if(Asunto == ""){
              alert("Escriba un Asunto");
              return false
          }
          if(Mensaje == ""){
              alert("Escriba un mensaje!");
              return false
          }
        
        return true;
     });
     
 });