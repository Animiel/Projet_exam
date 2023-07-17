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





const coeurPlein = document.getElementById('solid-off');
const coeurVide = document.getElementById('regular-on');
let cardInfos = document.getElementsByClassName('dogInfos');

window.addEventListener('load', (event) => {
    coeurPlein.style.left = '-20px';
    coeurVide.style.left = '10px';
    for (let dogIndex = 0; dogIndex < cardInfos.length; dogIndex++) {
        cardInfos[dogIndex].style.display = 'none';
    }
}); 

coeurVide.addEventListener('mouseover', function() {
    coeurVide.animate([
    { transform: 'translateX(-40px)' }],
    { duration: 600});
    coeurPlein.animate([
    { transform: 'translateX(30px)' }],
    { duration: 600});
})





let cards = document.getElementsByClassName('wanted');
let a_slides = [];
let a_slider = [];
let actImg = 0;
let nbr_img = a_slides.length;
let leftArrow = document.getElementsByClassName('fa-caret-left');
let rightArrow = document.getElementsByClassName('fa-caret-right');

function removeActiveImg() {
    for(let i = 0 ; i < nbr_img ; i++) {
        a_slides[i].classList.remove('activeImg');
    }
}

// function previous() {
//     if(actImg <= 0) {
//         actImg = nbr_img;
//     }
//     actImg--;
//     removeActiveImg();
//     slider_children[actImg].classList.add('activeImg');
// }

// function next() {
//     actImg++;
//     if(actImg >= nbr_img) {
//         actImg = 0;
//     }
//     removeActiveImg();
//     slider_children[actImg].classList.add('activeImg');
// }

// for (let j = 1; j <= cards.length; j++) {
//     a_slider.push(document.getElementsByClassName(`first-view-${j}`));  //a_slider == [[]] => a_slider[j] = []
// }
// for (let k = 0; k < a_slider.length; k++) {
//     let slider_children = a_slider[k][0].children;
//     // console.log(slider_children);
//     for (let l = 0; l < slider_children.length; l++) {
//         if (slider_children[l].localName == "img") {
//             a_slides.push(slider_children[l])
//         }
//         if (slider_children[l].className == 'fa-caret-left') {
//             slider_children[l].addEventListener('click', element => {
//                 if(actImg <= 0) {
//                     actImg = nbr_img;
//                 }
//                 actImg--;
//                 removeActiveImg();
//                 slider_children[actImg].classList.add('activeImg');
//             });
//         }
//         if (slider_children[l].className == 'fa-caret-right') {
//             slider_children[l].addEventListener('click', element => {
//                 actImg++;
//                 if(actImg >= nbr_img) {
//                     actImg = 0;
//                 }
//                 removeActiveImg();
//                 slider_children[actImg].classList.add('activeImg');
//             });
//         }
//     }
// }





for (let numAnnonce = 1; numAnnonce < cards.length; numAnnonce++) {
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