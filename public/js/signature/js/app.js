var wrapper = document.getElementById('signature-pad');
var clearButton = wrapper.querySelector('[data-action=clear]');
var undoButton = wrapper.querySelector('[data-action=undo]');
var openViewButton = wrapper.querySelector('[data-action=open-view]');
var savePNGButton = wrapper.querySelector('[data-action=save-png]');
var saveJPGButton = wrapper.querySelector('[data-action=save-jpg]');
var saveSVGButton = wrapper.querySelector('[data-action=save-svg]');
var canvas = wrapper.querySelector('canvas');
var signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

function resizeCanvas() {
	var ratio = Math.max(window.devicePixelRatio || 1, 1);
	canvas.width = canvas.offsetWidth * ratio;
	canvas.height = canvas.offsetHeight * ratio;
	canvas.getContext('2d').scale(ratio, ratio);
	signaturePad.clear();
}

window.onresize = resizeCanvas;
resizeCanvas();

function download(dataURL, filename) {
	if (navigator.userAgent.indexOf('Safari') > -1 && navigator.userAgent.indexOf('Chrome') === -1) {
		window.open(dataURL);
	} else {
		var blob = dataURLToBlob(dataURL);
		var url = window.URL.createObjectURL(blob);

		var a = document.createElement('a');
		a.style = 'display: none';
		a.href = url;
		document.body.appendChild(a);
		a.download = filename;
		a.click();
		window.URL.revokeObjectURL(url);
	}
}

function dataURLToBlob(dataURL) {
	var parts = dataURL.split(';base64,');
	var contentType = parts[0].split(':')[1];
	var raw = window.atob(parts[1]);
	var rawLength = raw.length;
	var uInt8Array = new Uint8Array(rawLength);

	for (var i = 0; i < rawLength; ++i) {
		uInt8Array[i] = raw.charCodeAt(i);
	}

	return new Blob([ uInt8Array ], { type: contentType });
}

clearButton.addEventListener('click', function(event) {
	signaturePad.clear();
});

undoButton.addEventListener('click', function(event) {
	var data = signaturePad.toData();

	if (data) {
		data.pop();
		signaturePad.fromData(data);
	}
});

openViewButton.addEventListener('click', function(event) {
	if (signaturePad.isEmpty()) {
		alert('Please provide a signature first.');
	} else {
		var dataURL = signaturePad.toDataURL();
		var blob = dataURLToBlob(dataURL);
		var url = window.URL.createObjectURL(blob);
		var a = document.createElement('a');
		a.style = 'display: none';
		a.href = url;
		window.open(url);
		document.body.appendChild(a);
	}
});

savePNGButton.addEventListener('click', function(event) {
	if (signaturePad.isEmpty()) {
		alert('Please provide a signature first.');
	} else {
		var dataURL = signaturePad.toDataURL();
		download(dataURL, 'signature.png');
	}
});

saveJPGButton.addEventListener('click', function(event) {
	if (signaturePad.isEmpty()) {
		alert('Please provide a signature first.');
	} else {
		var dataURL = signaturePad.toDataURL('image/jpeg');
		download(dataURL, 'signature.jpg');
	}
});

saveSVGButton.addEventListener('click', function(event) {
	if (signaturePad.isEmpty()) {
		alert('Please provide a signature first.');
	} else {
		var dataURL = signaturePad.toDataURL('image/svg+xml');
		download(dataURL, 'signature.svg');
	}
});
