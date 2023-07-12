let t_slider = document.getElementsByClassName('t_slider');
let etape = 0;
let nbr_slides = t_slider.length;
let buttons = "";

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





let cards = document.getElementsByClassName('wanted');
const coeurPlein = document.getElementById('solid-off');
const coeurVide = document.getElementById('regular-on');

window.addEventListener('load', (event) => {
    coeurPlein.style.left = '-20px';
    coeurVide.style.left = '10px';
  }); 

coeurVide.addEventListener('mouseover', function() {
    coeurVide.animate([
        { transform: 'translateX(-40px)' }],
        { duration: 600});
    coeurPlein.animate([
        { transform: 'translateX(30px)' }],
        { duration: 600});
    coeurPlein.addEventListener('mouseover', function() {
        setTimeout(5000);
    })
})





let a_slides = document.getElementsByClassName('wanted-pic');
let actImg = 0;
let nbr_img = a_slides.length;
let leftArrow = document.getElementsByClassName('fa-caret-left');
let rightArrow = document.getElementsByClassName('fa-caret-right');

function removeActiveImg() {
    for(let i = 0 ; i < nbr_img ; i++) {
        a_slides[i].classList.remove('activeImg');
    }
}

function previous() {
    actImg--;
    if(actImg < 0) {
        actImg = nbr_img;
    }
    removeActiveImg();
    a_slides[actImg].classList.add('activeImg');
}

function next() {
    actImg++;
    if(actImg >= nbr_img) {
        actImg = 0;
    }
    removeActiveImg();
    a_slides[actImg].classList.add('activeImg');
}
console.log(cards);
console.log(leftArrow);
console.log(a_slides);
console.log(cards.a_slides);