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





const coeurPlein = document.getElementById('solid-off');
const coeurVide = document.getElementById('regular-off');

window.addEventListener('load', (event) => {
    coeurPlein.style.left = '-20px';
    coeurVide.style.left = '-20px';
  }); 

function swapIconFull(element) {
    document.getElementById("regular-on").animate([
        { transform: 'translateX(-40px)' }],
        { duration: 800});
    coeurPlein.animate([
        { transform: 'translateX(30px)' }],
        { duration: 800});
}

function swapIconVoid(element) {
    document.getElementById("solid-on").animate([
        { transform: 'translateX(-40px)' }],
        { duration: 1000,
        iterations: 1 });
}





let a_slider = document.getElementsByClassName('wanted-pic');
let actImg = 0;
let nbr_img = a_slider.length;

function removeActive() {
    for(let i = 0 ; i < nbr_img ; i++) {
        a_slider[i].classList.remove('active');
    }
}