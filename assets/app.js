/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

//import $ from 'jquery';

import $ from "jquery";

window.$ = window.jQuery = require('jquery');
// any CSS you import will output into a single css file (app.scss in this case)

import './styles/app.scss';

import { Tooltip, Toast, Popover } from "bootstrap";

// start the Stimulus application
//import './bootstrap';
import './js/ajax.js';

import './js/formSortie.js'
import './js/game.js'



