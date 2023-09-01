const flash = document.getElementById('flash')
const flashClose = document.getElementById('flash-close')

flashClose.addEventListener('click', element => {
    flash.style.display = 'none';
})





const bars = document.getElementsByClassName('fa-bars')[0];
const navbar = document.getElementsByClassName('nav-links')[0];

bars.addEventListener('click', element => {
    navbar.classList.toggle('show-nav-links');
})





const filterIcon = document.getElementById('filterIcon')
const filterForm = document.getElementById('filterForm')

filterIcon.addEventListener('click', element => {
    if(filterForm.style.display == "block") {
        filterForm.style.display = "none";
    }
    else {
        filterForm.style.display = "block";
    }
})





function onClick(element) {
    document.getElementById('modal-img').src = "/img/posts/" + element.title;
    document.getElementById('img-modal').style.display = "block";
}






// const coeurPlein = document.getElementById('solid-off');
// const coeurVide = document.getElementById('regular-on');

// window.addEventListener('load', element => {
//     coeurPlein.style.left = '-20px';
//     coeurVide.style.left = '10px';
// }); 

// coeurVide.addEventListener('mouseover', function() {
//     coeurVide.animate([
//     { transform: 'translateX(-40px)' }],
//     { duration: 600});
//     coeurPlein.animate([
//     { transform: 'translateX(30px)' }],
//     { duration: 600});
// })





let cards = document.getElementsByClassName('carte');

for (let j = 1; j <= cards.length; j++) {
    let annSlider = document.getElementById(`first-view-${j}`);
    let prevImg = document.getElementById(`left-${j}`);
    let nextImg = document.getElementById(`right-${j}`);
    let annImgs = [];
    let activeImage = 0;

    for (let childNum = 0; childNum < annSlider.children.length; childNum++) {
        if (annSlider.children[childNum].localName == 'img') {
            annImgs.push(annSlider.children[childNum]);
        }
    }
    window.addEventListener('load', element => {
        for (let Test = 0; Test < cards.length; Test++) {
            annImgs[0].classList.add('activeImg');
        }
        for (let dogIndex = 0; dogIndex < cards.length; dogIndex++) {
            let dogInfos = document.getElementsByClassName('dogInfos');
            dogInfos[dogIndex].style.display = 'none';
        }
    })
    if (prevImg !== null) {
        prevImg.addEventListener('click', element => {
            if (activeImage <= 0) {
                activeImage = annImgs.length;
            }
            activeImage--;
            for (let i = 0; i < annImgs.length; i++) {
                annImgs[i].classList.remove('activeImg');
            }
            annImgs[activeImage].classList.add('activeImg');
        })
    }
    if (nextImg !== null) {
        nextImg.addEventListener('click', element => {
            activeImage++;
            if (activeImage >= annImgs.length) {
                activeImage = 0;
            }
            for (let ind = 0; ind < annImgs.length; ind++) {
                annImgs[ind].classList.remove('activeImg');
            }
            annImgs[activeImage].classList.add('activeImg');
        })
    }
}





// let gallery = document.getElementById('gallery-grid');
// let imagesGallery = document.getElementsByClassName('gallery-img');
// let currentImg = 0;

// window.addEventListener('load', element => {
//     imagesGallery[0].classList.add('currentGalImg');
//     imagesGallery[1].classList.add('currentGalImg');
//     imagesGallery[2].classList.add('currentGalImg');
// })
// setInterval(function() {
//     currentImg++;
//     if (imagesGallery[currentImg + 2] >= imagesGallery[imagesGallery.length]) {
//         imagesGallery[currentImg + 2] = imagesGallery[0];
//         imagesGallery[0].classList.add('currentGalImg');
//         imagesGallery[currentImg + 1] = imagesGallery[imagesGallery.length];
//         imagesGallery[imagesGallery.length].classList.add('currentGalImg');
//     }
//     if (imagesGallery[currentImg + 1] >= imagesGallery[imagesGallery.length]) {
//         imagesGallery[currentImg + 2] = imagesGallery[1];
//         imagesGallery[1].classList.add('currentGalImg');
//         imagesGallery[currentImg + 1] = imagesGallery[0];
//         imagesGallery[0].classList.add('currentGalImg');
//     }
//     if (imagesGallery[currentImg] == imagesGallery[imagesGallery.length]) {
//         imagesGallery[currentImg + 2] = imagesGallery[2];
//         imagesGallery[2].classList.add('currentGalImg');
//         imagesGallery[currentImg + 1] = imagesGallery[1];
//         imagesGallery[1].classList.add('currentGalImg');
//     }
//     imagesGallery[currentImg - 1].classList.remove('currentGalImg');
//     imagesGallery[currentImg] = imagesGallery[currentImg + 1];
//     imagesGallery[currentImg].classList.add('currentGalImg');
//     imagesGallery[currentImg + 1] = imagesGallery[currentImg + 2];
//     imagesGallery[currentImg + 1].classList.add('currentGalImg');
//     imagesGallery[currentImg + 2] = imagesGallery[currentImg + 3];
//     imagesGallery[currentImg + 2].classList.add('currentGalImg');
// }, 4000);





function showInfo(element) {
    let divIndex = element.title;
    let infoModal = document.getElementById(`info-modal-${divIndex}`);
    let secondView = document.getElementById(`second-view-${divIndex}`);
    infoModal.style.display = "block";
    secondView.style.display = "block";
    for(let i = 0 ; i < document.getElementsByClassName('carte').length ; i++) {
        document.getElementsByClassName(`carte`)[i].style.display = "none";
    }
}

function closeModal() {
    for (let j = 0; j <= document.getElementsByClassName('carte').length; j++) {
        let lastJ = document.getElementsByClassName('carte').length - 1;
        if(document.getElementById(`info-modal-${j}`) !== null && document.getElementById(`second-view-${j}`) !== null) {
            document.getElementById(`info-modal-${j}`).style.display = "none";
            document.getElementById(`second-view-${j}`).style.display = "none";
        }
        if(j == document.getElementsByClassName('carte').length) {
            document.getElementsByClassName(`carte`)[lastJ].style.display = "block";
        }
        else {
            document.getElementsByClassName(`carte`)[j].style.display = "block";
        }
    }
}





// image-box is the id of the div element that will store our cropping image preview
const imagebox = document.getElementById('image-cropbox')
    // crop-btn is the id of button that will trigger the event of change original file with cropped file.
const crop_btn = document.getElementById('crop-btn')
// id_image is the id of the input tag where we will upload the image
const input = document.getElementById('annonce_images')

// When user uploads the image this event will get triggered
input.addEventListener('change', () => {
    for (let cropIndex = 0; cropIndex < input.files.length; cropIndex++) {
        
        // Getting image file object from the input variable
        // const img_data = input.files[0]
        // createObjectURL() static method creates a DOMString containing a URL representing the object given in the parameter.
        // The new object URL represents the specified File object or Blob object.
        // const url = URL.createObjectURL(img_data)
        
        // Creating a image tag inside imagebox which will hold the cropping view image(uploaded file) to it using the url created before.
        // imagebox.innerHTML = `<img src="${URL.createObjectURL(input.files[cropIndex])}" id="crop-image" style="width:100%;">`
        const img = document.createElement("img");
        img.src = URL.createObjectURL(input.files[cropIndex]);
        img.id = `crop-image`;

        imagebox.appendChild(img);
    
        // Storing that cropping view image in a variable
        // const image = document.getElementById('crop-image')
        // console.log(image);
    
        // Displaying the image box
        document.getElementById('image-cropbox').style.display = 'block'
        // Displaying the Crop buttton
        document.getElementById('crop-btn').style.display = 'block'
        // Hiding the Post button
        document.getElementById('confirm-btn').style.display = 'none'
    
        // Creating a croper object with the cropping view image
        // The new Cropper() method will do all the magic and diplay the cropping view and adding cropping functionality on the website
        // For more settings, check out their official documentation at https://github.com/fengyuanchen/cropperjs
        const cropper = new Cropper(img, {
        autoCropArea: 1,
        viewMode: 1,
        scalable: false,
        zoomable: false,
        movable: false,
        minCropBoxWidth: 220,
        minCropBoxHeight: 500,
        })
        // When crop button is clicked this event will get triggered
        crop_btn.addEventListener('click', ()=>{
        // This method coverts the selected cropped image on the cropper canvas into a blob object
        cropper.getCroppedCanvas().toBlob((blob)=>{
            
            // Gets the original image data
            let fileInputElement = document.getElementById('annonce_images');
            // Make a new cropped image file using that blob object, image_data.name will make the new file name same as original image
            let file = new File([blob], input.files[cropIndex].name,{type:"image/*", lastModified:new Date().getTime()});
            // Create a new container
            let container = new DataTransfer();
            // Add the cropped image file to the container
            container.items.add(file);
            // Replace the original image file with the new cropped image file
            fileInputElement.files = container.files;
    
            // Hide the cropper box
            document.getElementById('image-cropbox').style.display = 'none'
            // Hide the crop button
            document.getElementById('crop-btn').style.display = 'none'
            // Display the Post button
            document.getElementById('confirm-btn').style.display = 'block'
    
            });
        });
    }
})