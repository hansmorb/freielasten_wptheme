//https://codepen.io/maximsv/pen/MbaNMz
(function() {
  var $grid = jQuery('.grid').imagesLoaded(function() {
    jQuery('.grid__wrapper').masonry({
      itemSelector: '.grid'
    });
  });
})();
