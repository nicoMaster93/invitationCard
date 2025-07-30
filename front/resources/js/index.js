import config from '../../modules/config/config.js';
import template from '../../modules/template/template.js';

class app {
    constructor(){
        this.build();
    }
    /**
     * @type { void } - Inicia el template de la aplicacion
     */
    build(){
        (new template(config));
    }
}(new app)