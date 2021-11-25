/*
#	Functions for dialogs
#	By: Edwin Fajardo
#	Date-time: 2020-04-12 23:20
*/

$(function ()
{
	dialog_width = 500;
	if($(window).width() < 600)
	{
		if(navigator.userAgent.indexOf('AppleWebKit') != -1)
		{
			dialog_width = "-webkit-fill-available";
		}
		else
		{
			dialog_width = "-moz-available";
		}
	}
	/* extend, sobreescribe la configuración por defecto de jquery ui dialogs */
	$.extend($.ui.dialog.prototype.options, {
		modal: true,
		autoOpen: false,
		show: {
			effect: "explode",
			duration: 500
		},
		hide: {
			effect: "explode",
			duration: 500
		},
		width: dialog_width,
		maxWidth: 500,
		open: function()
		{
			$("#container").css("opacity", "0.2");
		},
		close: function()
		{
			var dialog_form = $(this).find("form");
			dialog_form.trigger("reset");
			$("#container").css("opacity", "1");
			dialog_form.find("select").trigger("change.select2");
		}
	});

	// _allowInteraction permite la interacción de Select2 dentro de Dialog
	$.ui.dialog.prototype._allowInteraction = function(e)
	{
		return true;
	};

	//buttons
	$(".open_dialog_button").click(function() {
		$("#" + $(this).data("dialog")).dialog("open");
	});

	$("#add_category_dialog").dialog({
		title: "Agregar Categoría",
		buttons:
		{
			"Agregar": function(evt) {
				$("#category_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});

	$("#add_measure_dialog").dialog({
		title: "Agregar Unidad de medida",
		buttons:
		{
			"Agregar": function(evt) {
				$("#measure_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});

	$("#add_provider_dialog").dialog({
		title: "Agregar Proveedor",
		buttons:
		{
			"Agregar": function(evt) {
				$("#provider_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});

	$("#add_customer_dialog").dialog({
		title: "Agregar Cliente",
		buttons:
		{
			"Agregar": function(evt) {
				$("#customer_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});
	
	$("#add_requester_dialog").dialog({
		title: "Agregar solicitante",
		buttons:
		{
			"Agregar": function(evt) {
				$("#requester_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});

	$("#add_user_dialog").dialog({
		title: "Agregar Usuario",
		buttons:
		{
			"Agregar": function(evt) {
				$("#user_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});

	$("#set_payment_dialog").dialog({
		title: "Registrar pago",
		buttons:
		{
			"Guardar": function(evt) {
				$("#payment_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		}
	});

	$("#add_physician_dialog").dialog({
		title: "Registrar médico",
		buttons:
		{
			"Guardar": function(evt) {
				$("#physician_submit").click();
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		},
		close: function(){
			location.reload();
		}
	});
});
