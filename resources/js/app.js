import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import './secciones/sidebar';
import './secciones/profesores';
import './secciones/cursos';
import './secciones/aulas';
import './secciones/disponibilidad';
import './secciones/asignaciones';
import './secciones/horarios';