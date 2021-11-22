//https://codepen.io/maximsv/pen/MbaNMz
(function() {
  var $grid = jQuery('.grid').imagesLoaded(function() {
    jQuery('.grid__wrapper').masonry({
      itemSelector: '.grid'
    });
  });
})();

jQuery(".card").click(function(e){
  var nameid = jQuery(this).attr("id");
  var url = '/?page_id=' + nameid;
  window.location.href=url;
});
