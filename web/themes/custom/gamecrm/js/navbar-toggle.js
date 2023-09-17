var burgerTrigger = document.querySelector(".gamecrm-header-info-menu")
var burgerMenu = document.getElementById("block-adminmenu-2")
var cross = document.querySelector(".cross")
var figure = document.querySelector('.a')

burgerTrigger.addEventListener('click' , function () {
  burgerMenu.classList.toggle('active');
  cross.classList.toggle('hidden');
  figure.classList.toggle ('active');
})

