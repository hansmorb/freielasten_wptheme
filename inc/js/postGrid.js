//https://codepen.io/maximsv/pen/MbaNMz
(function() {
  var $grid = jQuery('.grid').imagesLoaded(function() {
    jQuery('.grid__wrapper').masonry({
      itemSelector: '.grid'
    });
  });
})();

jQuery(".card").click(function(e){
    var nameid = jQuery(e.target)[0].id;
    window.location.href='/?page_id=' + nameid;
});
