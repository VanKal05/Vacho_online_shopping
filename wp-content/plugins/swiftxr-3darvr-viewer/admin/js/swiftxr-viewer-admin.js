(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {

		// Get the URL search params
		const params = new URLSearchParams(window.location.search);

		// Get the value of the 'status' parameter
		const redirect = params.get('redirect');

		// Check if the 'redirect' parameter is set
		if (redirect) {

			if(redirect === 'dashboard'){
				// Do something with the status value
				window.location.href = my_script_vars.admin_url;
			}
			
		}


		showHelperText();

	});

})( jQuery );

window.addEventListener('load',()=>{
	showHelperText();
	// RunCheckBoxSelect();
})

let currentSelectedProductIndex = {
	index: null,
	parent: null
};

//swiftxr-mode-toggle

function RunCheckBoxSelect() {
	const selectedCheckBoxEl = document.querySelector('#swiftxr-mode-toggle');

	if(!selectedCheckBoxEl){
		return;
	}

	const selectedOption = selectedCheckBoxEl.checked;

	if(selectedOption){
		document.querySelector('#website-mode').style.display = 'none';
		document.querySelector('#ecommerce-mode').style.display = 'flex';
	}
	else{
		document.querySelector('#website-mode').style.display = 'flex';
		document.querySelector('#ecommerce-mode').style.display = 'none';
	}
}

function SelectProduct(evt) {

	currentSelectedProductIndex.index = evt.value;
	currentSelectedProductIndex.parent = evt?.parentElement;

	document.querySelector('#product-picker-select').disabled = false;

}

function AddSelectedProduct(evt) {

	const productIdEl = document.querySelector('input[name=swiftxr-woocommerce-product-id]');

	if(!productIdEl){
		alert ("Unknown error adding product, kindly contact support");

		return;
	}

	productIdEl.value = currentSelectedProductIndex?.index;

	CloseProductPicker();

	const selectorEl = document.querySelector('#swiftxr-wc-selected-product');

	if(!selectorEl){
		return;
	}

	if(currentSelectedProductIndex?.parent){
		selectorEl.innerHTML = '';

		const el = currentSelectedProductIndex.parent.cloneNode(true);

		const image = el.querySelector('img');
		const text = el.querySelector('p');

		selectorEl.append(image,text);

		el.remove();
	}
	 
}

function CloseProductPicker() {

	document.querySelector('#productModal').classList.add('swiftxr-hide');
}

function OpenProductPicker(event) {

	event.preventDefault();

	document.querySelector('#productModal').classList.remove('swiftxr-hide');
}

function SearchProducts() {
	const mainEl = document.querySelector('#swiftxr-product-modal');
	const searchInput = document.querySelector('#swiftxr-search-products');

	if(!mainEl || !searchInput){
		return;
	}

	const productEls = mainEl.querySelectorAll('.swiftxr-product-item');

	productEls.forEach((el)=>{

		if(el.children[2]){

			if(!el.children[2].innerText.toLowerCase().includes(searchInput.value.toLowerCase())){
				el.classList.add('swiftxr-hide');
			}
			else{
				el.classList.remove('swiftxr-hide');
			}
		}
	})

}

function showHelperText(evt) {
	// Get the selected option value

	const selectedOptionEl = document.querySelector('select[name=swiftxr-product-append]');

	if(!selectedOptionEl){
		return;
	}

	const selectedOption = selectedOptionEl.value;

	const helperText = document.getElementById('swiftxr-helper-text');

	// Show the appropriate helper text based on the selected option
	if (selectedOption === 'woocommerce_before_single_product_summary') {
		helperText.innerHTML = "This will place the product viewer at the top of the product page.";
	} else if (selectedOption === 'woocommerce_product_thumbnails') {
		helperText.innerHTML = "This will place the product viewer in the product gallery below the product images.";
	} else if (selectedOption === 'woocommerce_before_add_to_cart_button') {
		helperText.innerHTML = "This will place the product viewer below the product description and above the Add to Cart button.";
	} else if (selectedOption === 'woocommerce_after_single_product_summary') {
		helperText.innerHTML = "This will place the product viewer at the bottom of the product page after the product summary.";
	}

	// Show the helper text
	helperText.style.display = 'block';
}
