function pictureMagnifier (id, imgSrc) {
	var placeToInsertPhoto = $$(".product-shop")[0];
	var foto = $(id);
	
	var newFotoContainer = document.createElement("div");
	newFotoContainer.setAttribute('id', foto.getAttribute('id') + '_container');
	
	var newFoto = document.createElement("img");
	newFoto.setAttribute('src', imgSrc);
	
	newFotoContainer.appendChild(newFoto);
	
	placeToInsertPhoto.insert({
		'after' : newFotoContainer
	});
	
	newFotoContainer.setStyle('background: #ddd; padding: 5px;');
	
	newFotoContainer.clonePosition(placeToInsertPhoto);
	newFotoContainer.setStyle('position: fixed; top: 20px;');
	newFotoContainer.setStyle('width: ' + newFoto.width + 'px; height: ' + newFoto.height + 'px;');
}

function removePictureMagnifier(id) {
	var foto = $(id);
	var newFotoContainer = $(foto.getAttribute('id') + '_container');
	newFotoContainer.remove();
}
