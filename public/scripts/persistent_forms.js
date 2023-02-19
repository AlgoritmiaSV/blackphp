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

$(function() {
	var stored_module = localStorage.getItem("module");
	var stored_method = localStorage.getItem("method");
	if(stored_module != url.module || stored_method != url.method)
	{
		localStorage.clear();
	}
	localStorage.setItem("module", url.module);
	localStorage.setItem("method", url.method);
	$(".form_content input, .form_content textarea").each(function() {
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
		localStorage.clear();
	});
});
