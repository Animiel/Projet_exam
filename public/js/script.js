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

// window.addEventListener("load", (event) => {
//     for(let j = 0 ; j < nbr_slides ; j++) {
//         document.body.insertBefore(document.createElement('input'), document.getElementById('bloc'));
//     }
//   });

function onClick(element) {
    document.getElementById('modal-img').src = "/img/posts/" + element.title;
    document.getElementById('img-modal').style.display = "block";
}