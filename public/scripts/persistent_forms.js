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

$(function() {
	var stored_module = localStorage.getItem("module");
	var stored_method = localStorage.getItem("method");
	var device_code = localStorage.getItem("blackphp_device_code");
	if(device_code == null)
	{
		device_code = generateUUIDv4();
		localStorage.setItem("blackphp_device_code", device_code);
	}
	if(stored_module != url.module || stored_method != url.method)
	{
		localStorage.clear();
		localStorage.setItem("blackphp_device_code", device_code);
	}
	localStorage.setItem("module", url.module);
	localStorage.setItem("method", url.method);

	// Se agregan como persistentes, todos los inputs y textareas que estén dentro
	// de un contenedor de la clase .form_content, y también de forma individual
	// a todos los inputs de la clase .persistent_input
	$(".form_content input, .form_content textarea, .persistent_input").each(function() {
		var name = $(this).attr("name");
		var value = localStorage.getItem(name);
		if(value)
		{
			$(this).val(value);
		}
		$(this).on("change", function() {
			var name = $(this).attr("name");
			if(name && name.indexOf("[") < 0)
			{
				localStorage.setItem(name, $(this).val());
			}
		});
	});
	$(".form_content form").on("submit", function()
	{
		var device_code = localStorage.getItem("blackphp_device_code");
		localStorage.clear();
		localStorage.setItem("blackphp_device_code", device_code);
	});
});
