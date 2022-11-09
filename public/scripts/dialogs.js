/**
 * Configuración de cuadros de diálogo de JQuery UI
 * 
 * Incorporado el 2020-04-12 23:20
 * 
 * @author Edwin Fajardo
 * @copyright 2022 Red Teleinformática
 * @see https://www.edwinfajardo.com
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
	$(".open_dialog_button").on("click", function() {
		$("#" + $(this).data("dialog")).dialog("open");
	});

	$(".dialog").each(function() {
		options = {
			buttons:
			[
				{
					text: $(this).data("accept") || "Accept",
					click: function(evt) {
						$(this).find('button[type="submit"]').trigger("click");
					}
				},
				{
					text: $(this).data("close") || "Close",
					click: function() {
						$(this).dialog("close");
					}
				}
			]
		};
		$(this).dialog(options);
		if($(this).data("reload"))
		{
			$(this).dialog("option", "close", function() {
				location.reload();
			});
		}
	});
});
