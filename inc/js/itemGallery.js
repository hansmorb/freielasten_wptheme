var galleryIterator = galleryProps.galleryIterator;
var slideIndex = new Array(galleryIterator);
var slideId = new Array(galleryIterator);
slideIndex.foreach(function(item,index,array){
  slideIndex[index] = 1;
  slideId[index] = "gallery" + index;
});
slideIndex.foreach(function(item,index,array){
  showSlides(1, index);
});

// Next/previous controls
function plusSlides(n, no) {
  showSlides(slideIndex[no] += n, no);
}

// Thumbnail image controls
function currentSlide(n, no) {
	showSlides(slideIndex = n, no);
}

function showSlides(n, no) {
  var i;
  var slides = document.getElementsByClassName(slideId[no]);
	var dots = document.getElementsByClassName("dot" + no);
  if (n > slides.length) {slideIndex[no] = 1}
  if (n < 1) {slideIndex[no] = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
	for (i = 0; i < dots.length; i++){
		dots[i].className = dots[i].className.replace(" active", "");
	}
  slides[slideIndex[no]-1].style.display = "block";
	dots[slideIndex-1].className += " active";
}
