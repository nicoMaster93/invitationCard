import fetch from '../fetch/fetch.js'
import template from '../template/template.js';
import { message as msj } from '../utilities/utilities.js';

export default class modalNotAssitance {
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
                    <label for="not-assit-message">
                      Lamento que no puedas asistir
                      <span class="emojie">&#128546;</span>
                      <span class="emojie">&#128546;</span>
                      <br>Puedes dejarme un mensajito para tenerte presente en este día 
                    </label> 
                    <textarea class="textarea-not-assit" id="not-assit-message"></textarea>
                    <div class="sec-btn-modal">
                      <button id="btn-save" class="btn-next btn-loguin" type="button">Guardar</button>
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
          let message = $(`#not-assit-message`).val();
          if(message!=""){
              fetch.post('createMessageFromNosAssitance', {id,message},true).then( resp => {
                if(resp.code == 200){
                    msj(resp.message,"info",4000);
                    (new template())
                }else{
                    msj(resp.message,"warning");
                }
              })
          }else{
            msj("Dejanos un corto mensajito","warning");
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