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

if (element) {
  element.addEventListener('submit', event => {
    event.preventDefault();
  
    postData()
  });
}

document.addEventListener('DOMContentLoaded', function(){
  const contactButton = document.getElementById('contactButton');
  
  if (contactButton) {
    
    contactButton.addEventListener('click', function(e) {
      e.preventDefault();
  
      const contactForm = document.querySelector('#contactForm')
      
      contactForm.classList.toggle('hidden')
    })
  
  }

})

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
