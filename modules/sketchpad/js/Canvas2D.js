/*

	// Canvas2D : v0.3 : 2009.01.16 
	--------------------------------
	1) Retrofits older browsers that don't support HTML 5.0
		* IECanvas, an ActiveX module which emulates <canvas>, retrofits IE: code.google.com/p/iecanvas/
		* Retrofits prototyping up to Safari 3.  IE is fixed externally in /lib/HTMLElement.htc
		* Retrofits arcTo up to (which browsers don't work???)
		* Retrofits fillText / strokeText up to Safari 4, Firefox 3.1, and Opera
		* Retrofits transform / setTransform up to Safari 4, Firefox 3.1 (+?)

	2) Parses and Implements SVG:
		* Parses <font> attributes and paths then converts to FOB for fast access.  In the process of emulating more attributes in <canvas>.
		* Parses <path> [ M, m, L, l, C, c, Q, q, Z, H, h, V, v, A, a ] and converts to VOB [ M, L, C, Q, Z ] for fast access
		* Implements Shapes: circle, ellipse, line, path, polygon, polyline, rect

	3) Adds non-standardized features:
		* Pixel: colorMatrix, colorTransform (browser must support getImageData / putImageData)
		* Path: arc, roundRect, star, polygon, burst, arrow, wedge, gear, spiral, superformula, drawVector, drawVOB
		* Typeface: drawText


	* Breaking down the libraries:
	-------------------------------
	Canvas2D.js // add, remove, fix (browser fixes), ect
	Canvas2D.Pixel.js // ColorMatrix + ConvolutionMatrix + CustomFilter mixing and applying (maybe a few other default filters, such as DisplacementMap)
	Canvas2D.Raster.js // reflect, straighten, flip, rotate, resize 
	Canvas2D.Record.js // sets up functions for recording native functions
	Canvas2D.StyleSheet.js // importing, exporting, converting, and storing:  patterns, gradients, colors, and native styles (i.e. miterLimit, font, ect)
	Canvas2D.SVG.js // parse SVG into VOB (fonts and paths)
	Canvas2D.Transform.js // geometric TransformationMatrix + Decompose
	Canvas2D.Typeface.js // text building and styles
	Canvas2D.Vector.js // shapes and broken native path functions (optionally caches in VOB)
	Canvas2D.VOB.js // v2.0 is pretty complex, ties in layers + groups w/ vector objects, so gets it's own .js

    
    * Listing functions supported by the library:
    ----------------------------------------------
	Canvas2D.add // create new <canvas> element
	Canvas2D.remove // remove specific <canvas> element 


	HTMLCanvasElement (HTML 5.0)
	-----------------------------
	attribute unsigned long width;
	attribute unsigned long height;
	
	DOMString toDataURL([Optional] in DOMString type); // converts <canvas> in a base64 string of image type
	DOMObject getContext(in DOMString contextId); // returns CanvasRenderingContext2D
	

	State Stack (HTML 5.0)
	--------------------------
	void save(); // push state on state stack
	void restore(); // pop state stack and restore state
	
	void getPath(); // returns current path *


	Style API
	-----------------------
	Composition (HTML 5.0)
	-----------------------
	attribute float globalAlpha; // (default 1.0)
	attribute DOMString globalCompositeOperation; // (default source-over)
	
	Colors & Styles (HTML 5.0)
	--------------------------
	attribute any strokeStyle; // (default black)
	attribute any fillStyle; // (default black)
	
	Line Caps & Joins (HTML 5.0)
	----------------------------
	attribute float lineWidth; // (default 1)
	attribute DOMString lineCap; // "butt", "round", "square" (default "butt")
	attribute DOMString lineJoin; // "round", "bevel", "miter" (default "miter")
	attribute float miterLimit; // (default 10)
	
	Shadows: supported in Firefox 3.1, Safari 3, Opera 10
	-----------------------------------------
	attribute float shadowOffsetX; // (default 0)
	attribute float shadowOffsetY; // (default 0)
	attribute float shadowBlur; // (default 0)
	attribute DOMString shadowColor; // (default transparent black)

	CanvasGradient (HTML 5.0)
	-------------------------
	createLinearGradient(float x0, float y0, float x1, float y1);
	createRadialGradient(float x0, float y0, float r0, float x1, float y1, float r1);
	
	interface { void addColorStop(float offset, DOMString color); };
	

	Patterns (HTML 5.0)
	-------------------
	CanvasPattern createPattern(HTMLImageElement image, DOMString repetition);
	CanvasPattern createPattern(HTMLCanvasElement image, DOMString repetition);
	
	
	Transformation API (HTML 5.0)
	------------------------------
	void scale(float x, float y);
	void rotate(float angle);
	void translate(float x, float y);
	void transform(float m11, float m12, float m21, float m22, float dx, float dy);
	void setTransform(float m11, float m12, float m21, float m22, float dx, float dy);

	
	Pixel API (HTML 5.0)
	------------------------
	ImageData createImageData(float sw, float sh);
	ImageData getImageData(float sx, float sy, float sw, float sh); // Opera 9.5+, Firefox 2.0+, Safari 4.0+

	void putImageData(ImageData imagedata, float dx, float dy);
	void putImageData(ImageData imagedata, float dx, float dy, float dirtyX, float dirtyY, float dirtyWidth, float dirtyHeight);

	Addon: Filters (seperate file contains filters, cause this is a lot of information)

	void getPixel();
	void setPixel();
	
	
	Raster API (HTML 5.0)
	------------------------
	void drawImage(HTMLImageElement image, float dx, float dy, [Optional] float dw, float dh);
	void drawImage(HTMLImageElement image, float sx, float sy, float sw, float sh, float dx, float dy, float dw, float dh);
	void drawImage(HTMLCanvasElement image, float dx, float dy, [Optional] float dw, float dh);
	void drawImage(HTMLCanvasElement image, float sx, float sy, float sw, float sh, float dx, float dy, float dw, float dh);
	

	Vector API (HTML 5.0)
	----------------------
	boolean isPointInPath(float x, float y);

	void beginPath();
	void closePath();
	void moveTo(float x, float y);
	void lineTo(float x, float y);
	void arcTo(float x1, float y1, float x2, float y2, float radius); // don't punish for not having arcTo native
	void bezierCurveTo(float cp1x, float cp1y, float cp2x, float cp2y, float x, float y);
	void quadraticCurveTo(float cpx, float cpy, float x, float y);
	void arc(float x, float y, float radius, float startAngle, float endAngle, boolean anticlockwise); // openlaszlo.org/jira/browse/LPP-3491
	void rect(float x, float y, float w, float h);
	void clearRect(float x, float y, float w, float h);
	void fillRect(float x, float y, float w, float h);
	void strokeRect(float x, float y, float w, float h);
	void fill();
	void stroke();
	void clip();
	
	// Addon: SVG Shapes @ w3.org/TR/SVG11/shapes.html

	void circle(float cx, float cy, float r) // defines a circle based on a center point and a radius
	void ellipse(float cx, float cy, float rx, float ry) // defines an ellipse based on a center point and two radii
	void line(float x1, float y1, float x2, float y2) // defines a line segment that starts at one point and ends at another
	void path(string d) // SVG instructions, contains the moveto, line, cubic, quadratic, arc, and closepath: "M5,5 C5,45 45,45 45,5"

	// Addon: Path Shapes

	void roundRect(float x, float y, float width, float height, float rad) // circular rounded-rectangle
	void roundRect(float x, float y, float width, float height, float rx, float ry) // elliptical rounded-rectangle (two radii)
	void wedge(float x, float y, float radius, float startAngle, float endAngle, boolean anticlockwise); // en.wikipedia.org/wiki/Wedge_%28geometry%29
	void polygon(x, y, radius, sides) // en.wikipedia.org/wiki/Regular_polygon
	void gear(float x, float y, float radius, int sides, float slope); // en.wikipedia.org/wiki/Gear
	void star(float x, float y, float radius, int sides, float slope);
	void burst(float x, float y, float radius, int sides, float slope);
	void spiral(x, y, radius, sides, coils) // en.wikipedia.org/wiki/Archimedean_spiral
	void superformula(x, y, radius, points, m, n1, n2, n3) // en.wikipedia.org/wiki/Superformula


	Typeface API (HTML 5.0)  -- Firefox 3.1+ and Safari 4.0+
	------------------------
	attribute DOMString font; // (default 10px sans-serif)
	attribute DOMString textAlign; // "start", "end", "left", "right", "center" (default: "start")
	attribute DOMString textBaseline; // "top", "hanging", "middle", "alphabetic", "ideographic", "bottom" (default: "alphabetic")

	void fillText(DOMString text, float x, float y, [Optional] float maxWidth);
	void strokeText(DOMString text, float x, float y, [Optional] float maxWidth);
	void measureText(DOMString text); // returns { width: 100 }
	
	// Addon: Mozilla
	
	void drawText(DOMString text, float x, float y, [Optional] float maxWidth); // rhino-canvas.sourceforge.net/www/drawstring.html

*/
if (typeof Canvas2D == 'undefined') {
	var Canvas2D = {};
}
(function () {
var surface = {}; // canvas array
Canvas2D = mixin(Canvas2D, { // Global functions
	add: function (attr, height, id, style) { // add <canvas> element
		if (typeof attr == "number") { // accepts Canvas2D.add(width, height, id, style)
			attr = {
				width: attr,
				height: height,
				id: id,
				style: style
			};
		}
		if (attr) { // check whether surface exists
			if (typeof attr == "string") {
				attr = { // convert to object
					id: attr
				};
			}
			if (attr && attr.id && surface[attr.id]) {
				return surface[attr.id];
			}
		}
		var d = document.createElement('canvas'),
		ctx = d.getContext('2d');
		if (typeof attr == "object") {
			for (var key in attr) {
				switch (key) { // type of attribute
					case "style":
						if (typeof attr[key] == "object") {
							for (var type in attr[key]) // style object
							d.style[type] = attr[key][type];
							break;
						}
						else { // string
							d.setAttribute(key, attr[key]);
							break;
						}
					default:
						//-
						d[key] = attr[key];
						break;
				}
			}
		}
		document.body.appendChild(d);
		if (attr.id) { // cache surface
			surface[attr.id] = ctx;
		}
		return ctx;
	},
	remove: function (id) { // remove <canvas> element
		if (!surface[id]) {
			return;
		}
		delete surface[id]; // delete reference
		document.body.removeChild($("#" + id)); // remove DOM
	}
});
})();