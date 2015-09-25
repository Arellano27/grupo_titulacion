function mensaje_cisc( tipo, mensaje ){

	if(tipo == 'error'){
		$.growl.error({ message: mensaje });
	}

	if(tipo == 'notice'){
		$.growl.notice({ message: mensaje });
	}

	if (tipo == 'warning') {
		$.growl.warning({ message: mensaje });
	}
}