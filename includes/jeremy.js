//	Functions we'll use soon
function getPath(theImage) {
	"use strict";
	var endPos = theImage.src.lastIndexOf('/') + 1,	//	find the position of the last "/" mark
		path = theImage.src.substring(0, endPos);	//	the extension of the image
	return path;
}

function getBasicName(theImage) {
	"use strict";
	var endPos = theImage.src.lastIndexOf('.'),	//	find ending position of the basic name of the image (e.g. before ".png")
		startPos = theImage.src.lastIndexOf('/') + 1,	//	find start position of the basic name of the image (e.g. after "images/")
		basicName = theImage.src.substring(startPos, endPos);	//	the basic name of the image
	return basicName;
}

function getExt(theImage) {
	"use strict";
	var startPos = theImage.src.lastIndexOf('.') + 1,	//	find the starting position of the extension (after last ".")
		extension = theImage.src.substring(startPos);	//	the extension of the image
	return extension;
}

//	Initialize JQuery on window load
$(function () {
	"use strict";	//	Should be in all JavaScript that will follow strict standards

	//	Apply JQuery Rollover on all IMG tags within a container element
	$(".mouseoverAll img").each(function () {
		$(this).addClass("mouseover");
	});

	//	JQuery Rollover!
	$("img.mouseover").each(function () {	//	activate this on images with the class "mouseover"
		var thisImage = $(this),
			oldSrc = thisImage.attr('src'),	//	non-mouseover src
			overSrc = getPath(this) + getBasicName(this) + "_over." + getExt(this),	// (e.g. "images/myimage_over.png")
			preload = new Image(1, 1);	//	preload the overImage

		preload.src = overSrc;

		thisImage.hover(function () {
			thisImage.attr("src", overSrc);	//	change to overSrc when mousing over
		}, function () {
			thisImage.attr("src", oldSrc);	// change back to oldSrc when mouse leaves
		});
	});

	// JQuery Rollover Slide Enlarge!
	$("img.enlarge").each(function () {	//	activate this on images with the class "enlarge"
		var thisImage = $(this),	//	regular size image
			bigId = getBasicName(this) + "_large",	//	id of new image is basename of image + "_large"
			bigSrc = getPath(this) + bigId + "." + getExt(this),	//	src of big image is similare to regular
			bigImage = $("#" + bigId);	//	we are creating a jQuery reference to the div element even though we don't know its id

		thisImage.parent().append("<div class='overlarge' id='" + bigId + "'><img src='" + bigSrc + "' alt=''></div>");	//	create a new div with the image in it and append it as a sibling

		thisImage.hover(function () {
			var pos = thisImage.css('float') === 'none' ? thisImage.offset() : thisImage.position();	//	get the pos (location on screen) of the current image
			var bigId = getBasicName(this) + "_large";	//	id of new image is basename of image + "_large"
			var bigImage = $("#" + bigId);	//	we are creating a jQuery reference to the div element even though we don't know its id

			bigImage.css("top", pos.top + thisImage.height() + 10).css("left", pos.left).slideDown(600);	//	set position and slide big image down
			thisImage.fadeTo(600, 0.3);	//	fade small image down to 30% opacity
		}, function () {	//	on mouseout
			var bigId = getBasicName(this) + "_large";	//	id of new image is basename of image + "_large"
			var bigImage = $("#" + bigId);	//	we are creating a jQuery reference to the div element even though we don't know its id

			bigImage.slideUp(1000, function () {
				bigImage.css("display", "none");	//	make sure it's gone
				bigImage.stop(true);	//	stop animations (clear the queue = true)
			});

			thisImage.fadeTo(1000, 1.0, function () {	//	fade small image back up to 100% opacity
				thisImage.stop(true);	//	stop animations (clear the queue = true)
			});
		});
	});

	//	JQuery Fade-In effect on icons!
	/*
	$("#specialties img").each(function () {
		var techLogo = $(this);
		techLogo.css("opacity", "0.1");
		techLogo.fadeTo(2000, 1.0, function () {	//	take 3 seconds to fade to 100% opacity on page load
			techLogo.fadeTo(4000, 0.01);	// then fade back to 1% opacity
		});

		techLogo.hover(function () {
			techLogo.stop(true);	//	stop any other animation to do this instead.
			techLogo.fadeTo(500, 1.0);	//	fade up to 100% opacity
		}, function () {
			techLogo.fadeTo(3000, 0.01, function () {	//	fade down to 1% opacity
				techLogo.stop(true);	//	stop and (clearQueue = true)
			});
		});
	});
	$("#specialties .label").each(function () {	//	also works with the words!
		var techLogo = $(this).prev().children("img");
		$(this).hover(function () {	//	when user hovers over words, it affects image fade
			techLogo.stop(true);	//	stop any other animation to do this instead.
			techLogo.fadeTo(500, 1.0);	//	fade up to 100% opacity
		}, function () {
			techLogo.fadeTo(3000, 0.01, function () {	//	fade down to 1% opacity
				techLogo.stop(true);	//	stop and (clearQueue = true)
			});
		});
	});*/

	//	JQuery Logo expand on from left!
	$("#index #logo").css({width: 0, height: "133px"}).animate({width: "700px"}, 1500, null, function animationComplete() {
		$(this).css({width: "auto", height: "auto"});
	});
});
