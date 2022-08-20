/*
#	Functions for add register
#	By: Edwin Fajardo
#	Date-time: 2017-09-24 16:31
*/
$( function()
{
	new_item_row = $(".item_row").first().clone(true);
	json = [];

	action_module = "index";
	if(url.module != null && url.module != "")
	{
		action_module = url.module;
	}

	$("#main_content").has(".form_content").addClass("form_main_content");

	/* Build select2 */
	building_entry_selector = true;
	function build_selectors()
	{
		$(".data_selector:not(.select2-hidden-accessible)").each(function() {
			var selector = $(this);
			if($(window).width() < 900)
			{
				selector.data("width", "100%");
			}
			if(json[selector.data("source")] != null)
			{
				if(selector.data("search") != "none" || selector.data("default") == "none")
				{
					item = $(document.createElement("option"));
					item.appendTo(selector);
				}
				var select_params = {
					data: json[selector.data("source")],
					dropdownAutoWidth: true,
					placeholder: selector.data("placeholder") || "",
					width: selector.data("width") || "fit-content"
				}
				if(selector.data("search") == "none")
				{
					select_params.minimumResultsForSearch = Infinity;
				}
				selector.select2(select_params);
				if(selector.data("value") != null)
				{
					selector.val(selector.data("value"));
					selector.trigger('change.select2');
				}
			}
		});
		$("#bill_type").trigger("change");
		$(".entry_selector").trigger("change");
		building_entry_selector = false;
	}

	/* Get form data */
	$.ajax({
		method: "POST",
		url: action_module + "/load_form_data/",
		data: url,
		dataType: "json"
	})
	.done(function(json_data) {
		json = json_data;
		/* inputs */
		if(json.update)
		{
			$(".update_input").each(function() {
				$(this).val(json.update[$(this).attr("name")]);
				$(this).data("value", json.update[$(this).attr("name")]);
				if($(this).data("cat_source") != null)
				{
					$(this).data("default_value", json.update[$(this).attr("name")]);
					$(this).data("default_cat", json.update[$($(this).data("cat_source")).attr("name")]);
				}
			});
			$(".update_radio").each(function() {
				if(json.update[$(this).attr("name")] == $(this).attr("value"))
				{
					$(this).attr("checked", "checked");
				}
				else
				{
					$(this).removeAttr("checked");
				}
			});
			$(".update_ucheck").each(function()
			{
				if(parseInt(json.update[$(this).attr("name")]) == 1)
				{
					$(this).attr("checked", "checked");
				}
				else
				{
					$(this).removeAttr("checked");
				}
			});
			$(".update_text").each(function()
			{
				$(this).text(json.update[$(this).data("id")]);
			});
		}
		$(".age_input").trigger("change");

		/* Multiple checks */
		if(json.check)
		{
			$(".update_check").each(function()
			{
				var check = $(this);
				if(json.check[check.data("source")])
				{
					var checked = false;
					$.each(json.check[check.data("source")], function(index, value){
						if(value.id == check.attr("value"))
						{
							check.attr("checked", "checked");
							checked = true;
						}
					});
					if(!checked)
					{
						check.removeAttr("checked");
					}
				}
			});
		}

		/* Items */
		if(json.items)
		{
			$(".items_container").each(function()
			{
				var container = $(this);
				if(json.items[container.data("source")] && json.items[container.data("source")].length > 0)
				{
					var _template = container.find("tr").first().clone(true);
					container.find("tr:first").first().remove();
					row_count = 0;
					$.each(json.items[container.data("source")], function(index, value) {
						/* Prepare */
						var _tr = _template.clone(false);
						_tr.find(".date_input").each(set_date_picker);
						_tr.find("input").on("keypress", input_keypress);
						_tr.find(".row_quantity, .row_price").on("change", function() {
							calc_row_total($(this));
							calc_bill_total();
						});
						_tr.find(".delete_row_icon").on("click", delete_row_click);
						build_autocomplete(_tr);
						/* Fill */
						_tr.find(".row_number").val(row_count);
						_tr.find(".row_count").text(++row_count);
						_tr.find(".row_available").text(value.available);
						_tr.find(".row_quantity").val(value.quantity);
						_tr.find(".row_price").val(value.price);
						$.each(value, function(v_index, v_value) {
							_tr.find("input." + v_index).val(v_value);
							_tr.find("textarea." + v_index).val(v_value);
							_tr.find("select." + v_index).data("value", v_value);
							_tr.find("span." + v_index).text(v_value);
						});
					
						if(value.pres_id)
						{
							_tr.find(".pres_id").val(value.pres_id);
						}
						calc_row_total(_tr);
						container.append(_tr);
						_tr.find("input").first().trigger("focus");
						/* Partial values */
						_tr.find(".complete_value").text(value.product_name);
						_tr.find(".complete_value").on("click", complete_click);
						_tr.find(".complete_value").css("display", "inline-block");
						_tr.find(".partial_value").hide();
						_tr.find(".partial_value").on("blur", partial_blur);
						_tr.find(".partial_value").on("change", partial_change);
						/* Generics */
						_tr.find(".row_generics").text("");
					});
					if(row_count > 1)
					{
						$(".delete_row_icon").css({
							"visibility":"visible"
						});
					}
					calc_bill_total();
				}
			});
			$(".current").on("change", calculate_consumption);
			$(".current").trigger("change");
		}
		if(json.presentations)
		{
			trs = $("#pres_container tr");
			for(i = 0; i < trs.length && i < json.presentations.length; i++)
			{
				var pres = json.presentations[i];
				_tr = $(trs.get(i));
				_tr.find(".pres_id").val(pres.pres_id);
				_tr.find(".pres_name").val(pres.pres_name);
				_tr.find(".pres_price").val(pres.price);
				_tr.find(".pres_units").val(pres.units);
			}
		}
		/* Form Pagination */
		if(json.found_rows != null)
		{
			var current_page = 0;
			if(url.options.page == null)
			{
				current_page = json.found_rows > 0 ? 1 : 0;
			}
			else
			{
				current_page = url.options.page;
			}
			$('.pagination').jqPagination({
				paged: function(page) {
					url.options["page"] = page;
					goto_url();
				},
				max_page: Math.ceil(json.found_rows / 100),
				current_page: current_page,
				page_string: "{current_page} / {max_page}"
			});
		}
		/* selectors */
		build_selectors();
		build_autocomplete();
		/* Unique selection */
		$(".unique_selection").on("change", function() {
			setTimeout(unique_selection, 500);
		});
		unique_selection();
		$("textarea").trigger("input");
	})
	.fail(function() {
	})
	.always(function() {
	});

	/* sending a form */
	close_dialog = null;
	add_selector = null;
	last_form = null;
	//Flag to prevent duplicate submission 
	duplicate = false;

	$("form").on("submit", function(e){
		e.preventDefault();
		if(duplicate)
		{
			return false;
		}
		else
		{
			duplicate = true;
			setTimeout(function() {
				duplicate = false;
			}, 5000);
		}
		if(!validate())
		{
			return false;
		}
		var action = $(this).attr("action") || "save";
		$(this).find(".date_input").each(function() {
			$(this).data("value", $(this).val());
			$(this).val($.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate")));
		});
		//form_data = $.parseJSON(JSON.stringify($(this).serializeArray()));
		var form_data = new FormData(this);
		first_input = $(this).find("input").first();
		div_sending = $(this).siblings(".sending");
		div_success = $(this).siblings(".success");
		div_error = $(this).siblings(".error");
		div_sending.show();
		close_dialog = $(this).data("close_dialog");
		add_selector = $(this).data("selector");
		last_form = $(this);
		if($(this).data("module") != null)
		{
			action_module = $(this).data("module");
		}
		var ajax_options = {
			'xhr': function() {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt) {
					if (evt.lengthComputable) {
						var percentComplete = ((evt.loaded / evt.total) * 100);
						$(".progress-bar").width(percentComplete + '%');
						$(".progress-bar").html(percentComplete+'%');
					}
				}, false);
				return xhr;
			},
			'beforeSend': function(){
				$(".progress-bar").width('0%');
				$(".progress-bar").html('0%');
			},
			'method': "POST",
			'url': action_module + "/" + action + "/",
			'data': form_data,
			'dataType': "json",
			'processData': false,
			'contentType': false
		};
		$.ajax(ajax_options)
		.done(function(json) {
			if(json.message)
			{
				$.jAlert({
					'title': json.title,
					'content': json.message,
					'theme': json.theme,
					'autofocus': '.jalert_accept',
					'onClose': function() {
						first_input.focus();
						if(json.reload_after)
						{
							location.reload();
						}
						if(json.print_after)
						{
							$(".form_body").printThis();
							$(".reload_button").show()
							$(".reload_button").siblings().hide()
						}
						if(json.redirect_after)
						{
							reset_last_form(json);
							location.href = json.redirect_after;
						}
					},
					'btns': [
						{'text':json.accept||'Accept', 'closeAlert':true, 'theme': json.theme, 'class': 'jalert_accept'}]
				});
			}
			if(json.success || json.saved)
			{
				success(json, div_success);
			}
			else if(json.next != null)
			{
				reset_last_form(json);
				location.href = json.next;
			}
			else if(json.reload)
			{
				location.reload();
			}
			else
			{
				fail(div_error);
			}
		})
		.fail(function() {
			fail(div_error);
		})
		.always(function() {
			div_sending.hide();
			$(".date_input").each(function() {
				if($(this).data("value") != null)
				{
					$(this).val($(this).data("value"));
					$(this).removeAttr("data-value");
				}
			});
		});
	});

	$("form .cancel_button").on("click", function() {
		var reset_location = $(this).closest("form").data("reset");
		if(reset_location)
		{
			$(".form_content").css({
				"opacity":"0.1",
				"transform":"scale(0.1)"
			});
			history.back();
		}
	});

	$(".close_form").on("click", function(e) {
		e.preventDefault();
		$(".form_content").css({
			"opacity":"0.1",
			"transform":"scale(0.1)"
		});
		history.back();
	});

	/* Validations */
	function validate()
	{
		var equal_value = "";
		var validated = true;
		$(".equal_value").each(function() {
			if(equal_value == "")
			{
				equal_value = $(this).val();
			}
			else if($(this).val() != equal_value)
			{
				$.jAlert({
					'title': "Error",
					'content': "Las contraseñas no coinciden",
					'theme': "red",
					'autofocus': '.jalert_accept',
					'btns': [
						{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept'}]
				});
				validated = false;
			}
		});
		return validated;
	}

	/* On success */
	function success(json_result, div_success)
	{
		div_success.fadeIn();
		if(add_selector != null)
		{
			source = $("#" + add_selector).data("source");
			json[source][json[source].length] = json_result;
			$("#" + add_selector).select2({"data":json[source]});
			$("#" + add_selector).val(json_result.id).trigger("change");
		}
		if(close_dialog != null && !json_result.reload_after)
		{
			$("#" + close_dialog).dialog("close");
			close_dialog = null;
		}
		else
		{
			setTimeout(function() {
				div_success.fadeOut("slow");
			}, 5000);
		}
		reset_last_form(json_result);
	}

	/* On fail */
	function fail(div_error)
	{
		div_error.fadeIn();
		setTimeout(function() {
			div_error.fadeOut("slow");
		}, 5000);
	}

	/**
	 * Copiar datos a span antes de imprimir
	 * 
	 * Esta función será removida en el futuro, ya que se está imprimiendo sólo reportes
	 * con printThis
	 */
	$(window).on("beforeprint", function() {
		$('input, textarea').each(function() {
			$(this).parent().find('.no_screen').text($(this).val());
		});
		$('select').each(function() {
			$(this).parent().find('.no_screen').text($(this).find("option:selected").text());
		});
	});

	/* Clalc details */
	bill_type = 1;
	function calc_row_total(_input)
	{
		var row_quantity = _input.closest("tr").find(".row_quantity").val();
		var row_price = _input.closest("tr").find(".row_price").val();
		var total_cell = _input.closest("tr").find(".row_total").find("span");
		if(row_quantity == '' && !isNaN(row_price))
		{
			total_cell.text(parseFloat(row_price).toFixed(2));
		}
		else if(isNaN(row_quantity) || isNaN(row_price))
		{
			total_cell.text("0.00");
		}
		else
		{
			var total = row_quantity * row_price;
			total_cell.text(total.toFixed(2));
		}
	}

	$(".row_quantity, .row_price").on("change", function() {
		calc_row_total($(this));
		calc_bill_total();
	});

	$(".details_table input").on("keypress", input_keypress);

	/**
	 * 
	 * @param {object} e Evento de Javascript
	 * @returns void
	 */
	function input_keypress(e)
	{
		if(e.which == 13)
		{
			var tbody = $(this).closest("tbody");
			if(tbody.is(".locked_tbody"))
			{
				return false;
			}
			if($(this).closest("tr").is(':last-child'))
			{
				var last_price = $(this).closest("tr").find(".row_price");
				var last_product = $(this).closest("tr").find(".row_product_name");
				if(last_product.val() == "")
				{
					last_product.focus();
					return false;
				}
				if(last_price.val() == "")
				{
					last_price.focus();
					return false;
				}
				$(this).closest("tr").find(".data_selector").each(function() {
					$(this).select2("destroy");
				});
				var _tr = $(this).closest("tr").clone();
				tbody.append(_tr);
				/* Prepare row */
				_tr.find(".row_count").text(tbody.find("tr").length);
				_tr.find("input").first().trigger("focus");
				_tr.find("input").val('');
				_tr.find("textarea").val('');
				_tr.find(".row_number").val(tbody.find("tr").length - 1);
				_tr.find(".clearable").text("");
				_tr.find(".date_input").each(set_date_picker);
				_tr.find("input").on("keypress", input_keypress);
				_tr.find(".row_quantity, .row_price").on("change", function() {
					calc_row_total($(this));
					calc_bill_total();
				});
				_tr.find(".row_total").find("span").text("0.00");
				_tr.find(".delete_row_icon").on("click", delete_row_click);
				$(".delete_row_icon").css({
					"visibility":"visible"
				});
				_tr.find(".complete_value").text("");
				_tr.find(".complete_value").on("click", complete_click);
				_tr.find(".partial_value").show();
				_tr.find(".partial_value").on("blur", partial_blur);
				_tr.find(".partial_value").on("change", partial_change);
				_tr.find(".row_generics").text("");
				_tr.find(".local_code").on("change", search_by_code);
				build_autocomplete(_tr);
				build_selectors();
			}
			return false;
		}
	}

	function calc_bill_total()
	{
		var bill_total = 0;
		$(".row_total").each(function() {
			bill_total += parseFloat($(this).find("span").text());
		});
		$("#subtotal").val(format_number(bill_total));
		$("#iva").val(format_number(bill_total * 0.13));
		if(bill_type == 2)
		{
			$("#total").val(format_number(bill_total * 1.13));
		}
		else
		{
			$("#total").val(format_number(bill_total));
		}
		$("#paid_amount_input").trigger("change");
	}

	format_number = function(str)
	{
		var n = new Number(str);
		var myObj = {
			style: "currency",
			currency: "USD"
		}
		return n.toLocaleString("en-US", myObj).slice(1);
	}

	/* Autocomplete */
	function build_autocomplete(element = $("body"))
	{
		element.find(".list_input").each(function() {
			var labeled_source = json[$(this).data("source")].map(function (item) {
				if(item.label == null)
					item.label = item.text; 
				return item;
			});
			$(this).autocomplete({
				source: labeled_source,
				select: function( event, ui ) {
					if(ui.item.id || ui.item.local_code)
					{
						var _tr = $(this).closest("tr");
						$.each(ui.item, function(v_index, v_value) {
							_tr.find("input." + v_index).val(v_value);
							_tr.find("textarea." + v_index).val(v_value);
							_tr.find("select." + v_index).data("value", v_value);
							_tr.find("span." + v_index).text(v_value);
						});
						if(bill_type == 2)
						{
							_tr.find(".sale_price").val(ui.item.nvat_price);
						}
						_tr.find(".row_available").text(ui.item.quantity);
						var row_quantity = _tr.find(".row_quantity").val();
						if(row_quantity == "")
						{
							_tr.find(".row_quantity").val(1);
						}
						calc_row_total(_tr);
						calc_bill_total();
						if(ui.item.combo_id)
						{
							setTimeout(check_generic, 50, _tr, ui.item.combo_id);
						}
						else
						{
							_tr.find(".row_generics").html("");
						}
					}
				},
				change: function(event, ui) {
					if (!ui.item) {
						$(this).val('');
						$(this).trigger("change");
					}
				},
				autoFocus: true
			});
			$(this).on("click", function() {
				$(this).trigger("select");
			});
		});
	}

	function check_generic(_tr, combo_id)
	{
		var combo_tr = _tr;
		$.ajax({
			method: "GET",
			url: url.module + "/generic_by_combo/" + combo_id,
			dataType: "json"
		})
		.done(function(generic_data) {
			combo_tr.find(".row_generics").html("");
			$.each(generic_data, function(index, value) {
				var div = $(document.createElement("div"));
				var generic_span = $(document.createElement("span"));
				generic_span.text(value.product_name + ":");
				div.append(generic_span);
				var select = $(document.createElement("select"));
				select.attr("name", "generics[]");
				div.append(select);
				combo_tr.find(".row_generics").append(div);
				$.each(value.options, function(oindex, ovalue) {
					var new_option = $(document.createElement("option"));
					new_option.attr("value", ovalue.product_id);
					new_option.text(ovalue.product_name);
					select.append(new_option);
				});
			});
		})
		.fail(function() {
		})
		.always(function() {
		});	
	}

	/* Bill type */
	$("#bill_type").on("change", function()
	{
		var type_id = $(this).val();
		if(type_id == 2)
		{
			$("#vat_total, #subtotal_div").show();
		}
		else
		{
			$("#vat_total, #subtotal_div").hide();
		}
		if(type_id == 3)
		{
			$("#bill_number").val(json.ticket);
			$("#bill_number").attr("type", "number");
		}
		else
		{
			$("#bill_number").val('');
			$("#bill_number").attr("type", "text");
		}
		if(type_id == "")
		{
			JSON.ticket++;
		}
		bill_type = type_id;
	});

	/* Classifier */
	$(".classifier_input").on("change", function() {
		$(".content_viewer").css("visibility", "hidden");
		$(".classifier_button").show();
		$(".print_button").hide();
	});

	/* Calculate change */
	$("#paid_amount_input").on("change", function() {
		var payment = parseFloat($(this).val());
		var total = $("#total").val();
		total = parseFloat(total.replace(",",""));
		var change = payment - total;
		if(!isNaN(change))
		{
			$("#change_input").val(change.toFixed(2));
		}
		else
		{
			$("#change_input").val('');
		}
	});

	/* Delete */
	delete_button_click = function()
	{
		deletion_url = $(this).data("url");
		deletion_next = $(this).data("next");
		$.jAlert({
			'title': "Confirmar",
			'content': "¿Confirma que desea borrar este registro?",
			'theme': "red",
			'autofocus': '.jalert_cancel',
			'btns': [
				{'text':'Confirmar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept', 'onClick': delete_entry},
				{'text':'Cancelar', 'closeAlert':true, 'theme': 'gray', 'class': 'jalert_cancel'}]
		});
	}

	$(".delete_button").on("click", delete_button_click);

	function delete_entry()
	{
		$.ajax({
			method: "POST",
			url: deletion_url,
			data: url,
			dataType: "json"
		})
		.done(function(deletion_data) {
			if(deletion_data.deleted)
			{
				$.jAlert({
					'title': deletion_data.title || "Success",
					'content': deletion_data.message || "Deleted succesfully!",
					'theme': "blue",
					'autofocus': '.jalert_accept',
					'btns': [
						{'text': deletion_data.accept || 'OK', 'closeAlert':true, 'theme': 'blue', 'class': 'jalert_accept', 'onClick': function() {
							location.href = deletion_next;
						}}]
				});
			}
			else if(deletion_data.message)
			{
				$.jAlert({
					'title': deletion_data.title || "Message",
					'content': deletion_data.message,
					'theme': deletion_data.theme || "red",
					'autofocus': '.jalert_accept',
					'btns': [
						{'text':deletion_data.accept||'OK', 'closeAlert':true, 'theme': deletion_data.theme || "red", 'class': 'jalert_accept'}]
				});
			}
			else
			{
				$.jAlert({
					'title': "Error",
					'content': "Failed to delete.",
					'theme': "red",
					'autofocus': '.jalert_accept',
					'btns': [
						{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept'}]
				});
			}
		})
		.fail(function() {
		})
		.always(function() {
		});	
	}

	$(".reload_button").on("click", function() {
		location.reload();
	});

	delete_row_click = function(e) {
		e.preventDefault();
		delete_button = $(this);
		tbody = $(this).closest("tbody");
		row_count = tbody.find("tr").length;
		if(row_count < 2)
		{
			return false;
		}
		$.jAlert({
			'title': "Confirmar",
			'content': "¿Está seguro de que desea eliminar la fila?",
			'theme': "red",
			'autofocus': '.jalert_cancel',
			'btns': [
				{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept', 'onClick':function(){
					// Temporary Solution:
					// Products
					product_id = delete_button.closest("tr").find(".product_id").val();
					if(product_id != null && product_id != "")
					{
						removed = delete_button.closest("tr").find(".item_id").val();
						input = $(document.createElement("input"));
						input.val(removed);
						input.attr("name", "removed[]");
						input.attr("type", "hidden");
						delete_button.closest("form").append(input);
					}
					// Services
					service_id = delete_button.closest("tr").find(".service_id").val();
					if(service_id != null && service_id != "")
					{
						removed = delete_button.closest("tr").find(".item_id").val();
						input = $(document.createElement("input"));
						input.val(removed);
						input.attr("name", "removed_services[]");
						input.attr("type", "hidden");
						delete_button.closest("form").append(input);
					}
					// Others
					delete_button.closest("tr").remove();
					calc_bill_total();
					row_count = 0;
					tbody.find(".row_count").each(function() {
						$(this).text(++row_count);
					});
					row_number = 0;
					tbody.find(".row_number").each(function() {
						$(this).val(row_number++);
					});
					if(row_count < 2)
					{
						$(".delete_row_icon").css({
							"visibility":"hidden"
						});
					}
				}},
				{'text':'Cancelar', 'closeAlert':true, 'theme': 'darkgray', 'class': 'jalert_cancel'}
			]
		});
	};

	$(".delete_row_icon").on("click", delete_row_click);

	/* Go To URL */
	function goto_url()
	{
		if(url.method == null)
		{
			url.method = "listar";
		}
		href = url.module + "/" + url.method + "/";
		$.each(url.options, function(key, value) {
			if(value != null)
			{
				href += key + "/" + value + "/";
			}
		});
		
		location.href = href;
	}

	function reset_last_form(json)
	{
		if(last_form != null && !json.no_reset)
		{
			last_form.trigger("reset");
			last_form.find(".data_selector").val('').trigger('change');
			last_form.find(".clearable").text("");
			last_form.find(".deletable").not(":first").remove();
		}
	}

	/* Code generator */
	$(".code_generator").on("change", function() {
		var object_data = $(this).select2("data")[0];
		var code_input = $($(this).data("code_input"));
		if(code_input.data("default_cat") != null && object_data.id == code_input.data("default_cat"))
		{
			code_input.val(code_input.data("default_value"));
		}
		else if(object_data.next != null)
		{
			code_input.val("" + object_data.category_prefix + object_data.next + object_data.category_suffix);
		}
	});

	/* Add item from button */
	$(".add_row_button").on("click", function() {
		var tbody = $($(this).data("tbody"));
		if(tbody == null)
		{
			tbody = $(".items_container").first();
		}
		var last_tr = tbody.find("tr").last();
		last_tr.find(".data_selector").each(function() {
			$(this).select2("destroy");
		});
		_tr = last_tr.clone();
		tbody.append(_tr);
		/* Prepare row */
		_tr.find(".row_count").text(tbody.find("tr").length);
		_tr.find("input").first().focus();
		_tr.find("input").val('');
		_tr.find("textarea").val('');
		_tr.find(".row_number").val(tbody.find("tr").length - 1);
		_tr.find(".date_input").each(set_date_picker);
		_tr.find("input").keypress(input_keypress);
		_tr.find(".row_quantity, .row_price").change(function() {
			calc_row_total($(this));
			calc_bill_total();
		});
		_tr.find(".row_total").find("span").text("0.00");
		_tr.find(".delete_row_icon").click(delete_row_click);
		_tr.find(".clearable").text("");
		$(".delete_row_icon").css({
			"visibility":"visible"
		});
		_tr.find(".complete_value").text("");
		_tr.find(".complete_value").click(complete_click);
		_tr.find(".row_generics").text("");
		_tr.find(".partial_value").show();
		_tr.find(".partial_value").blur(partial_blur);
		_tr.find(".partial_value").change(partial_change);
		_tr.find(".local_code").change(search_by_code);
		build_autocomplete(_tr);
		build_selectors();
	});

	$(".add_entry_button").on("click", function() {
		var container = $($(this).data("container"));
		var last_entry = container.find(".form_entry").last();
		last_entry.find(".data_selector").each(function() {
			$(this).data("value", $(this).val());
			$(this).select2("destroy");
		});
		var entry = last_entry.clone();
		container.append(entry);

		/* Prepare entry */
		entry.find(".entry_count").text(container.find(".form_entry").length);
		entry.find("input").first().trigger("focus");
		entry.find("input, select, textarea").val('');
		entry.find(".date_input").each(set_date_picker);
		entry.find(".delete_entry_icon").on("click", delete_entry_click);
		entry.find(".extra_data").text("");
		$(".delete_entry_icon").css({
			"visibility":"visible"
		});
		build_autocomplete(entry);
		build_selectors();
		entry.find(".image-upload").each(function()
		{
			$(this).imageReader();
		});
		entry.find(".image-preview").html('');
	});

	function delete_entry_click(e)
	{
		e.preventDefault();
		delete_button = $(this);
		$.jAlert({
			'title': "Confirmar",
			'content': "¿Está seguro de que desea eliminar la entrada?",
			'theme': "red",
			'autofocus': '.jalert_cancel',
			'btns': [
				{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept', 'onClick':function(){
					delete_button.closest(".form_entry").remove();
				}},
				{'text':'Cancelar', 'closeAlert':true, 'theme': 'darkgray', 'class': 'jalert_cancel'}
			]
		});
	}
	$(".delete_entry_icon").on("click", delete_entry_click);

	/* Image uploader */
	if($('.input-images').length)
	{
		$('.input-images').imageUploader({
			'label': 'Arrastre aquí una imagen, o haga click para buscarla.',
			'maxFiles': 1
		});
	}

	/**
	 * Unique selection
	 * Validate unique selection for each value in several selectors
	 */
	function unique_selection()
	{
		selected = [];
		$(".unique_selection").each(function() {
			selected[selected.length] = $(this).val();
		});
		$(".unique_selection option").each(function() {
			$(this).removeAttr("disabled");
		});
		$(".unique_selection").each(function() {
			selected_val = $(this).val();
			selector = $(this);
			selector.find("option").each(function() {
				if($.inArray($(this).val(), selected) >= 0 && selected_val != $(this).val())
				{
					$(this).attr("disabled", true);
				}
			});
			selector.select2();
		});
	}
	/**
	 * Selection of entries
	 */
	 $(".entry_selector").on("change", function(e) {
		value = $(e.target).val();
		$(".hidden_entry").hide();
		$(".hidden_entry").find("input").each(function() {
			if(!building_entry_selector)
			{
				$(this).val("");
			}
			$(this).removeAttr("required");
		});
		$(".hidden_entry").find("select").each(function() {
			if(!building_entry_selector)
			{
				$(this).val("");
			}
			$(this).removeAttr("required");
		});
		$(".entry_type_" + value).show();
		$(".entry_type_" + value).find("input").each(function() {
			if($(this).data("required") != null)
			{
				$(this).attr("required", true);
			}
		});
		$(".entry_type_" + value).find("select").each(function() {
			if($(this).data("required") != null)
			{
				$(this).attr("required", true);
			}
		});
	});
	$(".entry_type_" + $(".entry_selector").data("default")).show();

	//Textarea
	$('textarea').each(function () {
		min_height = 30;
		if(this.scrollHeight > this.style.height)
		{
			min_height = this.scrollHeight;
		}
		else
		{
			min_height = this.style.height;
		}
		if(min_height < 30)
		{
			min_height = 30;
		}
		this.setAttribute('style', 'height:' + min_height + 'px;');
	}).on('input', function () {
		this.style.height = 'auto';
		this.style.height = (this.scrollHeight) + 'px';
	});

	//Age inputs
	$(".age_input").on("change", function() {
		if($(this).val() == "")
		{
			return true;
		}
		date = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate"));
		age_span = $(this).next("span");
		$.ajax({
			method: "GET",
			url: "index/age_calculation/" + date,
			dataType: "json"
		})
		.done(function(age_data) {
			if(age_data.age)
			{
				age_span.text(age_data.age);
			}
		})
		.fail(function() {
		})
		.always(function() {
		});	
	});

	/* Partial and complete values */
	$(".partial_value").on("blur", partial_blur);

	function partial_blur()
	{
		if($(this).val().length > 0)
		{
			$(this).siblings(".complete_value").text($(this).val());
			$(this).siblings(".complete_value").css("display", "inline-block");
			$(this).hide();
		}
	}

	$(".complete_value").on("click", complete_click);
	function complete_click()
	{
		var partial_value = $(this).siblings(".partial_value").first();
		partial_value.show();
		partial_value.trigger("focus");
		partial_value.trigger("select");
		$(this).css("display", "none");
	}

	$(".partial_value").on("change", partial_change);
	function partial_change()
	{
		$(this).siblings(".complete_value").text($(this).val());
		if($(this).val().length == 0)
		{
			$(this).siblings(".complete_value").hide();
			$(this).show();
		}
	}
	$(".image-upload").each(function()
	{
		$(this).imageReader();
	});

	/* Search by local_code and barcode */
	function search_by_code()
	{
		local_code = $(this).val();
		list_input = $(this).closest("tr").find(".list_input");
		items = json[list_input.data("source")];
		$.each(items, function(index, value) {
			if((value.local_code == local_code || value.barcode == local_code) && !value.pres_id)
			{
				list_input.val(value.product_name);
				list_input.data('ui-autocomplete')._trigger('select', 'autocompleteselect', {item:value});
			}
		});
	}
	$(".local_code").on("change", search_by_code);

	/* Check DUI */
	$(".check_identifier").on("change", function() {
		$(".identifier_clean").val("");
		check_url = url.module + "/check_identifier/" + $(this).val();
		if($(".list_selector").val() != null && $(".list_selector").val() != "")
		{
			check_url += "/" + $(".list_selector").val() + "/";
		}
		$.ajax({
			method: "GET",
			url: check_url,
			dataType: "json"
		})
		.done(function(identified) {
			if(identified.found)
			{
				$.jAlert({
					'title': "Entrada duplicada",
					'content': "El(la) sr(a) " + identified.complete_name + " ya se encuentra en la lista seleccionada.",
					'theme': "red",
					'autofocus': '.jalert_accept',
					'btns': [
						{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept'}]
				});
			}
			$.each(identified, function(index, value) {
				$("." + index).val(value);
			});
		})
		.fail(function() {
		})
		.always(function() {
		});	
	});

	/** Calculate consumption */
	calculate_consumption = function() {
		tr = $(this).closest("tr");
		var previous_value = parseInt(tr.find(".previous").text());
		var current_value = parseInt($(this).val());
		var consumption_span = tr.find(".consumption");
		if(isNaN(previous_value) || isNaN(current_value))
		{
			return false;
		}
		consumption = current_value - previous_value;
		if(consumption < 0)
		{
			$.jAlert({
				'title': "Error",
				'content': "La lectura no puede ser inferior a " + previous_value + ".",
				'theme': "red",
				'autofocus': '.jalert_accept',
				'btns': [
					{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept'}]
			});
			consumption_span.text("");
		}
		else
		{
			consumption_span.text(consumption);
		}
	};
});
