!function($){"use strict";var App=function(){var elements={wnd:$(window),header:$(".main-header"),portfolioList:$("#portfolioList"),protfolioFilter:$("#portfolioFilter"),nav:$("#nav"),contAlert:$("#contactUsAlert")};var stickHeader=function(){var changeClasses=function(){if($(document).scrollTop()>(elements.wnd.height()/2)){elements.header.addClass("sticky");elements.header.removeClass("fixed");}else{elements.header.addClass("fixed");elements.header.removeClass("sticky");}}
elements.wnd.on("scroll",changeClasses)
changeClasses()}
var googleMap=function(){function rgb2hex(rgb){var rgb=rgb||"rgb(2, 2, 2)";if(/^#[0-9A-F]{6}$/i.test(rgb))return rgb;rgb=rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);function hex(x){return("0"+parseInt(x).toString(16)).slice(-2);}return"#"+hex(rgb[1])+hex(rgb[2])+hex(rgb[3]);}var ourAddress=$("#map").data("location")||"earth",mapColor=rgb2hex($("#map").css("backgroundColor"))||"#e2e4e3";function getLatLong(address){var geo=new google.maps.Geocoder;geo.geocode({'address':address},function(results,status){if(status==google.maps.GeocoderStatus.OK){var latitude=results[0].geometry.location.k,longitude=results[0].geometry.location.B,styleMap=[{"stylers":[{"hue":mapColor},{"saturation":0}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]}];$("#map").gmap3({marker:{address:ourAddress},map:{options:{center:[latitude,longitude],zoom:15,mapTypeId:google.maps.MapTypeId.ROADMAP,styles:styleMap}}})}else{alert("Geocode was not successful for the following reason: "+status);}})}if($.fn.gmap3&&$("#map").length){getLatLong(ourAddress)}}
var isotopePortfolio=function(){$(window).load(function(){if($.fn.isotope){var portfolio=elements.portfolioList.isotope({itemSelector:'.p-item',layoutMode:'fitRows'})
elements.protfolioFilter.on("click","a",function(e){e.preventDefault()
var filterValue=$(this).attr('data-filter');portfolio.isotope({filter:filterValue});return false;})}})}
var navigation=function(){if($.fn.onePageNav&&elements.nav.length){elements.nav.onePageNav({currentClass:"active",scrollOffset:60,filter:":not(.external)"})}}
function countdown(){var count=$('#countdown');if($.fn.countdown&&count.length){count.countdown(count.data("date"),function(event){var $this=$(this);switch(event.type){case"seconds":case"minutes":case"hours":case"days":case"weeks":case"daysLeft":$this.find('div#'+event.type).html(event.value);break;case"finished":$this.hide();break;}});}}function wow(){$(window).load(function(){new WOW().init({mobile:false})});}function placeholderIE(){if($.fn.placeholder){$("input, textarea").placeholder()}}function validateAndSend(){if($.fn.validateForm){$.validate({form:'#contactForm',validateOnBlur:false,addSuggestions:false,scrollToTopOnError:false,onSuccess:function(){var name=$("#userName").val(),email=$("#userEmail").val(),phone=$("#userSubj").val(),plan=$("#userMessage").val(),allData=$("#contactForm").serialize();$.ajax({type:"POST",url:"php/contact.php",data:allData,success:function(){elements.contAlert.show()
$("#userName, #userEmail, #userSubj, #userMessage").val("")}});return false;}})
var messageForError=$("#helpBlock");$.validate({form:"#subscribeForm",errorMessagePosition:messageForError,scrollToTopOnError:false,onSuccess:function(){var sEmail=$("#sEmail").val(),allData=$("#subscribeForm").serialize();$.ajax({type:"POST",url:"php/subscribe.php",data:allData,success:function(){elements.contAlert.show()
$("#sEmail").val("")}});return false;}})}}function preloader(){$(window).load(function(){$(".preloader").fadeOut()
$("body").removeClass("remove-scroll")});}return{init:function(){preloader()
stickHeader()
googleMap()
isotopePortfolio()
navigation()
wow()
countdown()
placeholderIE()
validateAndSend()}}}();$(function(){App.init()})}(window.jQuery);