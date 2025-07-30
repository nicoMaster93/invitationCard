import fetch from '../fetch/fetch.js'
import template from '../template/template.js';
import { message } from '../utilities/utilities.js';

export default class modalUser {
    static config;
    static listGuest;
    constructor(config){
        this.config = config;
        this.listGuest = []
        this.build();
    }
    async build(){
      let dom = await this.dom();
      $(`#app`).append(dom)
      /**
       * asigno animaciones y eventos
      */
      $(`.select-guests`).select2();
      this.events();
        
    }
    async dom(){
        const listGuest = await fetch.get('getAllGuests');
        this.listGuest = listGuest;
        const elm = 
        `<div class="animate__animated animate__fadeIn modal-user-container">
            <div class="body" id="modal">
                ${ this.loadImage('img-tr img-modal-header-lf','oso-3.png') }
                ${ this.loadImage('img-tr img-modal-header-rgt','oso-2.png') }
                <div class="text-bold">
                    <select class="select-guests" id="list-guests">
                      <option value="" >Busca tu nombre</option>
                    ${
                        (listGuest.code == 200) && listGuest.result.map((value, id) => `<option value="${id}" >${value.guest}</option>` )
                    }
                    </select>
                    <div class="sec-btn-modal loguin">
                      <button id="btn-loguin" class="btn-next btn-loguin" type="button">Ingresar <span class="material-loguin"> login </span></button>
                      <button id="btn-modal-back" class="btn-modal-back" type="button">Cerrar</button>
                    </div>
                </div>
                ${ this.loadImage('img-tr img-modal-bottom-lft','moon-baby.png') }
                ${ this.loadImage('img-tr img-modal-bottom-rgt','moon-baby.png') }
            </div>
        </div>
        `;
        return elm;
    }
    /**
     * Carga una imagen con la clase especificada.
     * @param {number} className - La clase de la imagen que se desea cargar.
     * @return {HTMLImageElement} - devuelve un elemento imagen HTML
     */
    loadImage(className,img){
        return `<div class="${className} animate__animated " id="${img.split(".")[0]}" >
                    <img src="./resources/images/${img}" />
                </div>`;
    }
    /**
     * Controla los eventos de la clase
     * @return {void} - no devuelve nada
     */
    events() {
      const loguin = this.loguin;
      const listGuest = this.listGuest;
      const elements = [
        ["btn-loguin", (e) => {
          e.preventDefault();
          let id = $(`#list-guests`).val();
          if(id!=""){
            if(loguin(id)){
              message(`Bienvenido a mi Baby Shower <br><span>${listGuest.result[id]['guest']}</span>`, 'info');
              this.config['session'] = listGuest.result[id];
              (new template(this.config))
            }
          }else{
            message("Primero debes seleccionar tu nombre","warning");
          }
        }],
        ["btn-modal-back", (e) => {
          e.preventDefault();
          $(".modal-user-container").removeClass("animate__fadeIn").addClass("animate__fadeOutDown");
          setTimeout(() => {
              $(".modal-user-container").remove();
          }, 1500);
        }],
        
      ];
      /* Ejecuto los eventos de click */
      elements.map((element) => {
        $(`#${element[0]}`).click(element[1])
      })
    }

    loguin(id){
      let newUrl = window.location.href.split('?')[0];
          newUrl = newUrl + '?' + id ;
      window.history.pushState({path: newUrl}, '', newUrl);
      return true;
    }

}