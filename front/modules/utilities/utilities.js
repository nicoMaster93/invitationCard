/**
 * maneja las alertas de la aplicacion
 * @param { string } msj - Mensaje de la alerta
 * @param { string } type - [info - warning] Tipo del color de la alerta
 * @param { number } time - tiempo de espera de la alerta default 3000
 * 
*/
const message = (msj="",type="info",time=3000) => {
    let bodyMsj = `
                <div id="alert-app" class="animate__animated animate__fadeInRight alert-app ${type} ">
                    <div class="alert-app-msj" >${msj}</div>
                </div>
                `;
    $('body').append(bodyMsj)
    setTimeout(() => {
        $(`#alert-app`).removeClass('animate__fadeInRight')
        $(`#alert-app`).addClass('animate__fadeOutRight')
    }, time);
    
    setTimeout(() => {
        $(`#alert-app`).remove()
    }, (time+300));
}

const validarFecha = (fecha)  => {
    const fechaActual = new Date();
    const fechaLimite = new Date(fecha);
    
    return fechaActual.getTime() < fechaLimite.getTime();
  }

export { message, validarFecha }