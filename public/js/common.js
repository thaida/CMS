// JavaScript Document

$(document).ready(function(){
	
	if(document.getElementById('slider_banner'))
	{
		$('#slider_banner .slider-banner').owlCarousel({
			loop:true,
			margin:10,
			nav:true,
			items:1
		})
	}
	if(document.getElementById('cj-slider01'))
	{
		$('#cj-slider01 .show-cj-slider').owlCarousel({
			loop:true,
			nav:true,
			margin:26,
			items:4
		})
	}
	if(document.getElementById('cj-slider02'))
	{
		$('#cj-slider02 .show-cj-slider').owlCarousel({
			loop:true,
			nav:true,
			margin:26,
			items:4
		})
	}
	if(document.getElementById('cj-slider03'))
	{
		$('#cj-slider03 .show-cj-slider').owlCarousel({
			loop:true,
			nav:true,
			margin:26,
			items:4
		})
	}
	if(document.getElementById('cj-slider04'))
	{
		$('#cj-slider04 .show-cj-slider').owlCarousel({
			loop:true,
			nav:true,
			margin:26,
			items:4
		})
	}
})
