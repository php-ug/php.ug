(function ($) {
	var 
		opera = window.opera && window.opera.toString() == "[object Opera]",
		fontsizeMeasurerId = "fontsize-measurer__" + (+new Date()),
		blockStyle = "position:relative;display:inline;width:auto;",
		blockSpanStyle = "color:transparent;display:inline;position:relative;z-index:2;cursor:inherit;white-space:nowrap;vertical-align:baseline;",
		textBlocks = [],
		fontsizeMeasurer,
		fontsizeMeasurerValue = 0,
		svgDefsCache = {},
		blocksCountTotal = 0,
		blocksCountUpdated = 0,
		elements,
		svgPatternPrefix = "pattern__",
		NS = {
			xhtml: "http://www.w3.org/1999/xhtml",
			svg: "http://www.w3.org/2000/svg",
			xlink: "http://www.w3.org/1999/xlink"
		};

	function isSVGNativeSupported() {
		return document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1");
	}
	
	function checkImageLoaded(img, block) {
		(function recheck() {
			if (img && img.complete && img.width > 0 && img.height > 0) {
				block.img = img;

				blocksCountUpdated++;
				
				updateBlock(block);

				//blocks final redrawing (bug in WebKit)
				if (blocksCountUpdated == blocksCountTotal) {
					updateBlocks();
				}
			} else {
				setTimeout(recheck, 10);
			}
		})();
	}
	
	function updateBlock(block) {
		var 
			span = block.span,
			svg = block.svg,
			svgText = block.svgText,
			svgPattern = block.svgPattern || null,
			svgImage = block.svgImage || null,
			img = block.img;

		if (svgPattern && svgImage) {
			svgPattern.attr("width", img.width).attr("height", img.height);
			
			svgImage.attr("width", img.width).attr("height", img.height);
		}

		svg.attr("style", "position:absolute;bottom:0;left:0;z-index:-1");
		
		span.attr("style", blockSpanStyle);

		var 
			textWidth = span.width(),
			textHeight = span.height(),
			textFontsize = span.css("font-size");
		
		svg.attr("width", textWidth).attr("height", textHeight);
		
		svgText.attr("font-size", textFontsize);
		
		/*! opera postfix hack */
		if (opera) {
			svg.attr("style", "position:absolute;bottom:0.065em;left:0;z-index:-1");
		}
	}
	
	function updateBlocks() {
		for (var i = 0, l = textBlocks.length; i < l; i++) {
			updateBlock(textBlocks[i]);
		}
	}
	
	function getFontSizeMeasurerValue() {
		return parseFloat(fontsizeMeasurer.css("font-size")) + fontsizeMeasurer.height() + fontsizeMeasurer.width();
	}
	
	function buildMeasurer() {
		fontsizeMeasurer = $(document.createElementNS(NS.xhtml, "span"));
		
		fontsizeMeasurer.attr("id", fontsizeMeasurerId).attr("style", "display:inline;position:absolute;left:-10000px");

		fontsizeMeasurer.html((new Array(100)).join("&#160;"));
		
		$("body").append(fontsizeMeasurer);
	}
	
	function initBlocksUpdater() {
		buildMeasurer();
		
		fontsizeMeasurerValue = getFontSizeMeasurerValue();
		
		(function updater() {
			var fontsizeMeasurerValueCurrent = getFontSizeMeasurerValue();
			
			if (fontsizeMeasurerValue != fontsizeMeasurerValueCurrent) { 
				updateBlocks();

				fontsizeMeasurerValue = fontsizeMeasurerValueCurrent;
			}
			
			setTimeout(updater, 100);
		})();
	}
	
	function createBlocks() {
		blocksCountTotal = elements.length;

		if (!blocksCountTotal) { //typeof document.documentElement.style.WebkitBackgroundClip == "undefined"
			return;
		}

		var 
			block,
			blockText,
			span,
			svg,
			svgDefs,
			svgText,
			svgPattern,
			svgPatternValue,
			svgImage,
			helperImage,
			textWidth,
			textHeight,
			textFontsize;
		
		elements.each(function (i) {
			block = $(this);
			
			block.attr("style", blockStyle);
			
			blockText = block.text();
			
			block.text("");

			span = $(document.createElementNS(NS.xhtml, "span"));

			span.html("<span>" + blockText + "</span>");
			
			span.attr("style", blockSpanStyle + "height:" + block.height() + "px; width:" + block.width() + "px;");
			
			block.append(span);
			
			textWidth = span.css("width");
			textHeight = span.css("height");
			
			textFontsize = span.css("font-size");

			svg = $(document.createElementNS(NS.svg, "svg"));

			svg.attr("version", "1.1").attr("baseProfile", "full").attr("x", "0").attr("y", "0").attr("width", "0").attr("height", "0");

			svg[0].setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns",  NS.svg); 
			svg[0].setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:xlink", NS.xlink); 

			span.append(svg);
			
			span.attr("style", blockSpanStyle);

			svgPatternValue = block.data("pattern");

			svgDefs = $(document.createElementNS(NS.svg, "defs"));
			
			svg.append(svgDefs);
			
			svgPattern = $(document.createElementNS(NS.svg, "pattern"));
			svgPattern.attr("id", svgPatternPrefix + svgPatternValue).attr("x", "0").attr("y", "0");
			
			svgPattern[0].setAttribute("patternUnits", "userSpaceOnUse");
			
			svgDefs.append(svgPattern);
			
			svgImage = $(document.createElementNS(NS.svg, "image"));
			svgImage[0].setAttributeNS(NS.xlink, "xlink:href", svgPatternValue); 
			
			svgImage.attr("x", "0").attr("y", "0");

			svgPattern.append(svgImage);
			
			svgDefsCache[svgPatternValue] = svgPattern;

			svgText = $(document.createElementNS(NS.svg, "text"));

			svgText.text(blockText);
			
			svgText.attr("x", "0").attr("y", "0").attr("font-size", textFontsize);
			
			/*! http://www.opera.com/docs/specs/opera9/svg/ */
			svgText.attr("dominant-baseline", "text-before-edge");
			if (opera) { /*hack for simulating dominant-baseline: text-before-edge*/
				svgText.attr("y",  textFontsize);
				svgText[0].setAttribute("textLength", textWidth)
			}
			
			svgText.attr("fill", "url(#" + svgPatternPrefix + svgPatternValue + ")");
			
			svg.append(svgText);
			
			textBlocks.push({
				span: span, 
				svg: svg, 
				svgText: svgText, 
				svgPattern: svgPattern, 
				svgImage: svgImage, 
				img: null
			});

			helperImage = new Image();

			helperImage.onload = (function (img, block) {
				checkImageLoaded(img, block); //for possible bug in WebKit
			})(helperImage, textBlocks[i]);
			
			helperImage.src = svgPatternValue;

			block.attr("style", blockSpanStyle);
		});

		initBlocksUpdater();
	}

	var methods = {
		init: function (options) {
			elements = $(this);

			if (isSVGNativeSupported()) {
				createBlocks();
			}
		}
	};

	$.fn.patternizer = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method == "object" || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error("Method " +  method + " does not exist on jQuery.patternizer");
		}
	};
})(jQuery);