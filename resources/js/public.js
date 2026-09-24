import $ from 'jquery';
import 'bootstrap';
import Wizard from './modules/wizard.js';

window.$ = window.jQuery = $;

document.addEventListener('DOMContentLoaded', () => {
    new Wizard();
});


import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
