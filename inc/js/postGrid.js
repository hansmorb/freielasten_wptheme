//https://codepen.io/maximsv/pen/MbaNMz
(function() {
  var $grid = $('.grid').imagesLoaded(function() {
    $('.grid__wrapper').masonry({
      itemSelector: '.grid'
    });
  });
})();
