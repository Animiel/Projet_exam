let t_slider = document.getElementsByClassName('t_slider');
let etape = 0;
let nbr_slides = t_slider.length;

function removeActive() {
    for(let i = 0 ; i < nbr_slides ; i++) {
        t_slider[i].classList.remove('active');
    }
}

setInterval(function() {
    // document.getElementById('radio' + counter).checked = true;
    etape++;
    if(etape >= nbr_slides) {
        etape = 0;
    }
    removeActive();
    t_slider[etape].classList.add('active');
}, 5000);





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





for (let numAnnonce = 1; numAnnonce <= cards.length; numAnnonce++) {
    let btnDogInfo = document.getElementById(`info-${numAnnonce}`);
    let btnBackInfo = document.getElementById(`back-${numAnnonce}`);
    btnDogInfo.addEventListener('click', element => {
        document.getElementById(`second-view-${numAnnonce}`).style.display = 'block';
        document.getElementById(`first-view-${numAnnonce}`).style.display = 'none';
    });
    btnBackInfo.addEventListener('click', element => {
        document.getElementById(`second-view-${numAnnonce}`).style.display = 'none';
        document.getElementById(`first-view-${numAnnonce}`).style.display = 'block';
    });
}





let gallery = document.getElementById('gallery-grid');
let imagesGallery = document.getElementsByClassName('gallery-img');
let currentImg = 0;

window.addEventListener('load', element => {
    imagesGallery[0].classList.add('currentGalImg');
    imagesGallery[1].classList.add('currentGalImg');
    imagesGallery[2].classList.add('currentGalImg');
})
setInterval(function() {
    imagesGallery[currentImg].classList.add('currentGalImg');
    imagesGallery[currentImg + 1].classList.add('currentGalImg');
    imagesGallery[currentImg + 2].classList.add('currentGalImg');
    currentImg++;
    if (imagesGallery[currentImg + 3] > imagesGallery[imagesGallery.length]) {
        imagesGallery[currentImg + 2] = imagesGallery[0];
        imagesGallery[currentImg + 2].classList.add('currentGalImg');
        imagesGallery[currentImg + 1] = imagesGallery[imagesGallery.length];
    }
    if (imagesGallery[currentImg + 2] > imagesGallery[imagesGallery.length]) {
        imagesGallery[currentImg + 2] = imagesGallery[1];
        imagesGallery[currentImg + 1] = imagesGallery[0];
    }
    if (currentImg >= imagesGallery.length) {
        currentImg = 0;
        // imagesGallery[currentImg + 1] = imagesGallery[0];
        // imagesGallery[currentImg + 2] = imagesGallery[1];
    }
    imagesGallery[currentImg - 1].classList.remove('currentGalImg');
    imagesGallery[currentImg] = imagesGallery[currentImg + 1];
    imagesGallery[currentImg].classList.add('currentGalImg');
    imagesGallery[currentImg + 1] = imagesGallery[currentImg + 2];
    imagesGallery[currentImg + 1].classList.add('currentGalImg');
    imagesGallery[currentImg + 2] = imagesGallery[currentImg + 3];
    imagesGallery[currentImg + 2].classList.add('currentGalImg');
}, 4000);
