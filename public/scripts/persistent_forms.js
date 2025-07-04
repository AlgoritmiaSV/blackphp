/**
 * Formularios persistentes
 * 
 * Conjunto de funciones que ayudan a mantener los datos de un formulario en 
 * localStorage, para evitar la pérdida de datos si el formulario se recarga
 * 
 * Incorporado el 2023-01-06 13:03
 * 
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
 */

function generateUUIDv4()
{
	return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
		(c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
	);
}

document.addEventListener("DOMContentLoaded", function ()
{
	const stored_module = localStorage.getItem("module");
	const stored_method = localStorage.getItem("method");
	let device_code = localStorage.getItem("blackphp_device_code");

	if (!device_code) {
		device_code = generateUUIDv4();
		localStorage.setItem("blackphp_device_code", device_code);
	}

	if (stored_module !== url.module || stored_method !== url.method) {
		localStorage.clear();
		localStorage.setItem("blackphp_device_code", device_code);
	}

	localStorage.setItem("module", url.module);
	localStorage.setItem("method", url.method);

	// Se agregan como persistentes, todos los inputs y textareas que estén dentro
	// de un contenedor de la clase .form_content, y también de forma individual
	// a todos los inputs de la clase .persistent_input
	const inputs = document.querySelectorAll(".form_content input, .form_content textarea, .persistent_input");

	inputs.forEach(function (el){
		const name = el.getAttribute("name");
		if (!name) return;

		const value = localStorage.getItem(name);
		if (value !== null){
			el.value = value;
		}

		el.addEventListener("change", function (){
			const key = el.getAttribute("name");
			if (key && !key.includes("[")) {
				localStorage.setItem(key, el.value);
			}
		});
	});

	const forms = document.querySelectorAll(".form_content form");
	forms.forEach(form => {
		form.addEventListener("submit", function () {
			const code = localStorage.getItem("blackphp_device_code");
			localStorage.clear();
			localStorage.setItem("blackphp_device_code", code);
		});
	});
});
