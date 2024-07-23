// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import { Page } from './js/common/Page/Page';
// import { postData } from './js/common/Form/request';

Page.init()
// const element = document.getElementById('property-form');

// if (element) {
//   element.addEventListener('submit', event => {
//     event.preventDefault();
  
//     postData()
//   });
// }

// document.addEventListener('DOMContentLoaded', function() {
//   document
//       .querySelectorAll('div.images div.col-lg-6')
//       .forEach((image) => {
//           addTagFormDeleteLink(image)
//       })
  
//   document
//       .querySelectorAll('.add_item_link')
//       .forEach(btn => {
//           btn.addEventListener("click", addFormToCollection)
//       });

// })

// function addFormToCollection(e) {
//   const maximum = document.getElementById('photoMax').value
//   const fileUploadInputsNumber = document.querySelectorAll('.file-upload').length
  
//   if (maximum == fileUploadInputsNumber) {
//       return
//   }

//   const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

//   const item = document.createElement('div');
//   item.classList.add('col-lg-6');
  
//   const label = document.createElement('label')
//   label.classList.add('btn')
//   label.classList.add('w-100')
//   label.classList.add('mb-0')
//   label.classList.add('p-0')
//   label.classList.add('overflow-hidden')
//   label.classList.add('image-preview')

//   collectionHolder.appendChild(item);

//   label.innerHTML = collectionHolder
//       .dataset
//       .prototype
//       .replace(
//       /__name__/g,
//       collectionHolder.dataset.index
//       );

//   item.appendChild(label);

//   const img = document.createElement('img')
//   img.src = '/images/no-img.png'
//   img.classList.add('w-100')
//   img.classList.add('h-auto')
//   img.classList.add('card-img-top')
  
//   label.prepend(img);
  
//   collectionHolder.dataset.index++;

//   // add a delete link to the new form
//   addTagFormDeleteLink(item);
// }

// function addTagFormDeleteLink(item) {
//   const removeFormButton = document.createElement('button');
//   removeFormButton.classList.add('btn')
//   removeFormButton.classList.add('btn-danger')
//   removeFormButton.classList.add('w-100')
//   removeFormButton.classList.add('mb-3')
//   removeFormButton.innerText = 'Supprimer';

//   item.append(removeFormButton);

//   removeFormButton.addEventListener('click', (e) => {
//       e.preventDefault();
//       // remove the li for the tag form
//       item.remove();
//   });
// }

// document.addEventListener('DOMContentLoaded', function() {
  
//   const imagesContainer = document.querySelector('.images')

//   if (imagesContainer) {
//     imagesContainer.addEventListener('change', function() {
        
//         document.querySelectorAll('.file-upload').forEach((image) => {
  
//             const file = image.files;
                
//             if (file.length > 0) {
//                 const fileReader = new FileReader();
//                 let fileContainer = image.parentNode;
  
//                 while ( !fileContainer.classList.contains('col-lg-6') ) {
//                     fileContainer = fileContainer.parentNode
//                 }
  
//                 const img = fileContainer.getElementsByTagName('img')
                    
//                 fileReader.onload = event => {
//                     img[0].setAttribute('src', event.target.result);
//                 }
  
//                 fileReader.readAsDataURL(file[0]);
//             }
//         })
    
//     })
//   }

// })
