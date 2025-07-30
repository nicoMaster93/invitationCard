import modalUser from "../modal/modal-user.js";
import modalCompanion from "../modal/modal-companios.js";
import fetch from '../fetch/fetch.js'
import { message, validarFecha } from "../utilities/utilities.js";
import config from "../config/config.js";
import modalNotAssitance from "../modal/modal-no-assistance.js";

export default class template {
    static config;
    constructor(conf){
        this.config = (conf === undefined ? config : conf );
        this.miAudio = document.getElementById('mi-audio');
        this.build();
    }
    introEnvelop(){
      if($(`#bearBaby`).length == 0 && $("#envelope-container").length > 0){
        $("#envelope-container").append( this.loadImage('img-tr img-center-envelop','oso-2.png','bearBaby') )
        $("#envelope-container").append( this.loadImage('img-tr img-envelop-fly','sobre.png','envelopeFlies') )
        /* entradas de las imagenes */
        $(`#bearBaby`).addClass("animate__rotateIn");
        $(`#envelopeFlies`).addClass("animate__fadeInRightBig");
        
        /* porngo el comentario del osito */
        const comm = `<div class="coment-click animate__animated animate__bounceIn">
          Hola!, Es muy especial que estes aquí; Dame un click y abre la invitación
        </div>`;
        setTimeout(() => {
          $(`#bearBaby`).append(comm);
        }, 1000);

        $(`#bearBaby`).click(() => {
          $(this)
          .removeClass('animate__swing')
          .addClass('animate__swing');
          this.playMusic(0);
          $(".coment-click").fadeOut(300)
          $("#envelope-container").fadeOut(900)
          
          let timer = (dom, time) => {
            setTimeout(() => {
              $(dom).remove();
            }, time);
          };

          timer("#envelope-container",1200);
          /**
           * asigno animaciones 
          */
          this.animatedDom();
          this.events();
        });

      }else{
        this.animatedDom();
        this.events();
      }

    }
    build(){
        $(`#app`).html('');
        $(`#app`).html(this.dom());
        
        this.introEnvelop()
        /* Valido si existe session */
        this.session();
        
    }
    dom(){
        let elm = 
        `<div class="container-inv">
            ${ this.loadImage('img-tr img-center-header','oso-1.png') }
            <div class="body" id="page1">
                <div class="text-bold">
                  <div id="title-sheet" class="tr title animate__animated ">
                    ${this.config['app-name']}
                  </div>
                  <div id="baby-name" class="tr sub-title baby-name animate__animated ">
                    ${this.config.babyShort}
                  </div>
                </div>
                <button id="btn-next" class="tr btn-next animate__animated ">
                  Siguiente <span class="material-symbols-outlined"> arrow_forward </span>
                </button>
            </div>
            ${ this.loadImage('img-tr left-bottom','blue-car.png') }
            ${ this.loadImage('img-tr rigth-bottom','baby.png') }
        </div>
        `;
        return elm;
    }
    /**
     * Carga una imagen con la clase especificada.
     * @param {number} className - La clase de la imagen que se desea cargar.
     * @return {HTMLImageElement} - devuelve un elemento imagen HTML
     */
    loadImage(className,img,_id){
        return `<div class="${className} animate__animated " id="${(_id ?? img.split(".")[0])}" >
                    <img src="./resources/images/${img}" />
                </div>`;
    }
    /**
     * Anima el Dom de la aplicacion
     * @return {void} - no devuelve nada
     */
    animatedDom() {
      const elements = [
        ["oso-1", "animate__swing", "animate__swing"],
        ["moon-baby", "animate__shakeX", "animate__swing"],
        ["blue-car", "animate__fadeInLeft", "animate__swing"],
        ["baby", "animate__lightSpeedInRight", "animate__swing"],
        ["title-sheet", "animate__slideInLeft","animate__swing"],
        ["baby-name", "animate__slideInRight","animate__swing"],
        ["btn-next", "animate__slideInLeft", "animate__swing"],
      ];
      $(`#bodyInv`).fadeIn(300);
      elements.reduce((promise, [id, className, classNamePermanent]) => {
        return promise.then(() => {
          const $el = $(`#${id}`);
          if (!$el.hasClass(className)) {
            $el.addClass(`${className} active`);
            return new Promise(resolve => {
              setTimeout(() => {
                $el.removeClass(className);
                $el.addClass(`${classNamePermanent}`);
                resolve();
              }, 500);
            });
          } else {
            return Promise.resolve();
          }
        });
      }, Promise.resolve());

    }
    /**
     * Controla los eventos de la clase
     * @return {void} - no devuelve nada
     */
    events() {
      const elements = [
        ["btn-next", (e) => {
          e.preventDefault();
          (new modalUser(this.config))
        }],
        
      ];
      /* Ejecuto los eventos de click */
      elements.map((element) => {
        $(`#${element[0]}`).click(element[1])
      })
    }
    
    playMusic(time=0){
      
      let reproducirDesde = time; // establece el punto de reproducción en 10 segundos
      const miAudio = document.getElementById('mi-audio');
      if (miAudio.paused || miAudio.ended) {
        miAudio.play();
        setTimeout(() => {
          miAudio.currentTime = reproducirDesde;
        }, 200);
      }else{
        miAudio.pause();
      }
    }
    /**
     * Recibe la variable de configuracion
     * @return {void } - no devuele datos
     */
    async session(){
      const id = window.location.href.split('?');
      if(id[1] !== undefined){
        const data = await fetch.get('getAllGuests',{id:id[1]});
        this.config.session = data;
        const user = data.result;
        if(data.code == 200){
          let elm = `<div class="text-bold ss">
                      <div class="sub-title animate__animated animate__slideInRight ">
                        ${user.guest}
                      </div>
                      
                      <div class="parrafo animate__animated animate__slideInLeft ">
                        Un nuevo ser llega para llenar de amor y alegría nuestros días.
                      </div>
                      <div class="parrafo animate__animated animate__slideInLeft ">
                        Queremos que seas parte de nuestra alegría.
                      </div>
                      <div class="parrafo animate__animated animate__slideInLeft ">
                        <b>${this.config['date-event']}</b>
                      </div>
                      <br />
                      <div class="sub-title sm lft animate__animated animate__slideInLeft ">
                        Lugar del evento
                      </div>
                      <div class="parrafo animate__animated animate__slideInLeft ">
                        Conjunto Portal de Jvargas - Salón social
                        <br />
                        <a href="${this.config.location}" class="target-link" target="_blank">Cra 60 # 67b - 49</a>
                      </div>
                      <div class="container-map">
                        <iframe src="${this.config.location}" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" gestureHandling="none"></iframe>
                      </div>
                      <div class="parrafo">
                        <div class="no-forget">
                          ¡No olvides!
                        </div>
                      </div>
                      <div class="parrafo">
                        <div class="secPresents">
                        ${ this.loadImage('img-tr img-present active','present.png') }
                        <span class="material-plus"> add </span>
                        ${ this.loadImage('img-tr img-present-diaper active','diaper.png') }
                        </div>
                      </div>
                      <br />
                      <div class="sub-title sm animate__animated animate__slideInLeft ">
                        Lista de Acompañantes
                      </div>
                      ${
                        validarFecha(this.config['max-date']) ? (`
                        <div class="parrafo animate__animated animate__slideInRight ">
                          Si quieres venir con acompañantes puedes agregarlos a continuación.
                        </div>
                        <div class="parrafo animate__animated animate__slideInLeft ">
                          <button class="btn-add" id="btn-add-compa">
                            Agregar Acompañante
                          </button>
                        </div>
                        `) : ''
                      }
                      <div class="table_guest animate__animated animate__slideInUp ">
                        <ul>
                          ${ this.getCompanions() }
                        </ul>
                      </div>
                      <br />
                      <div class="parrafo animate__animated animate__slideInLeft ">
                        ${
                          (user.confirm_assistance == null && validarFecha(this.config['max-date'])) ? 
                          `
                            ¿ Confirmar tu asistencia ?
                            <div class="sec-confim-assitance" >
                              <button class="btn-confirm yes" id="btn-confirm-add">
                                Si <span class="emoji">&#x1F60A;</span> 
                              </button>
                              <button class="btn-confirm not" id="btn-confirm-not">
                                No <span class="emoji">&#128546;</span>
                              </button>
                            </div>
                          ` : ``
                        }
                        
                      </div>
                      ${
                        validarFecha(this.config['max-date']) ? 
                        `<div class="parrafo animate__animated animate__slideInRight ">
                            <span class="text-import small">Faltando 2 días para el evento, ya no podrás agregar acompañanates ni confirmar tu asistencia.</span>
                        </div> 
                        <br /><br />
                        <button class="btn-back" id="btn-close" >Volver al inicio</button>
                        <br />` :
                        `<button class="btn-back" id="btn-close" >Volver al inicio</button>`
                      }
                      
                      <br />
                      <br />
                      <br />
                    </div>`
          $(`#page1`).html(elm);
          
          /* Valido la asistencia de la sesion */

          $('#btn-add-compa').click(() => {
            (new modalCompanion(this.config))
          })
          $('[data-delete]').click(  async function(){
              let id_guest = id[1];
              let id_compa = $(this).data('delete');
              const del = await fetch.delete('deleteCompanion',{id_guest, id_compa},true);
              if(del.code == 500){
                message(del.message,"warning");
              }else{
                $(this).parent('li').remove()
              }
          })

          $('#btn-confirm-add').click( async function() {
            message("Enviando confirmación",'info',2000)
            let resp = await fetch.post("confirmAssistance",{id:id[1]},true);
            if(resp.code == 200){
              message(resp.message,'info',5000)
            }else{
              message(resp.message,'warning')
            }
            (new template())
          })
          $('#btn-confirm-not').click( async function() {
            (new modalNotAssitance())
          })
          $('#btn-close').click( function(){  
            let newUrl = window.location.href.split('?')[0];
            window.history.pushState({path: newUrl}, '', newUrl);
            (new template())
          })
          
        }
        /**
           * asigno animaciones 
           */
          this.animatedDom();
          this.events();
      }
    }
    getCompanions(){
      const user = this.config.session.result;
      const compas = user.companions.map((value,index) => {
        return (`<li>${value.guest}  <button data-delete="${index}" >Borrar</button> </li>`)
       });
       if(compas.length == 0){
        return "<li>Aun no has agregado ningun acompañante</li>";
       }
       return compas.join("\n");
    }

}