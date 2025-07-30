import fetch from '../fetch/fetch.js'
import template from '../template/template.js';
import { message } from '../utilities/utilities.js';

export default class modalCompanion {
    static config;
    static listGuest;
    constructor(config){
        this.config = config;
        this.listGuest = []
        this.build();
    }
    async build(){
        $(`#app`).append(await this.dom())
        /**
         * asigno animaciones y eventos
        */
       $(`#list-guests`).select2();
        this.events();
        
    }
    async dom(){
        const listGuest = await fetch.get('getAllGuests');
        this.listGuest = listGuest;
        const elm = 
        `<div class="animate__animated animate__fadeIn modal-user-container">
            <div class="body" id="modal">
                ${ this.loadImage('img-tr img-modal-header-lf','oso-2.png') }
                ${ this.loadImage('img-tr img-modal-header-rgt','oso-3.png') }
                <div class="text-bold">
                    <label for="">
                        Agrega tu acompañante
                        <input class="input-guest" id="name-guest">
                    </label> 
                    <div class="sec-btn-modal">
                      <button id="btn-save" class=" btn-loguin" type="button">Guardar</button>
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
      const elements = [
        ["btn-save", (e) => {
          e.preventDefault();
          let id = window.location.href.split('?')[1];
          let name = $(`#name-guest`).val();
          if(name!=""){
              const conf = this.config;  
              fetch.post('createCompanioFromGuests', {id,name},true).then( resp => {
                if(resp.code == 200){
                    message(resp.message,"info");
                    (new template(conf))
                }else{
                    message(resp.message,"warning",6000);
                }
              })
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

}