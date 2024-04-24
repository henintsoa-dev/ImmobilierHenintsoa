// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import { postData } from './js/common/Form/request';

const element = document.getElementById('property-form');

element.addEventListener('submit', event => {
  event.preventDefault();
  // actual logic, e.g. validate the form
  // console.log('Form submission cancelled.');

  postData()
});

//document.getElementById('property-form').addEventListener('submit', postData())
// document.getElementById('submit_button').addEventListener('click', postData)

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
