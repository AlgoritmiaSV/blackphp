bill_type = 1;

function calc_row_total(_input)
{
	var row_quantity = _input.closest("tr").find(".row_quantity").val();
	var row_price = _input.closest("tr").find(".row_price").val();
	var total_cell = _input.closest("tr").find(".row_total").find("span");
	if(row_quantity == '' && row_price != '' && !isNaN(row_price))
	{
		total_cell.text(parseFloat(row_price).toFixed(2));
	}
	else if(isNaN(row_quantity) || isNaN(row_price) || row_price == '')
	{
		total_cell.text("0.00");
	}
	else
	{
		var total = row_quantity * row_price;
		total_cell.text(total.toFixed(2));
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
	if(parseInt($("#bill_type").val()) == 2)
	{
		$("#total").val(format_number(bill_total * 1.13));
	}
	else
	{
		$("#total").val(format_number(bill_total));
	}
	$("#paid_amount_input").trigger("change");
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

$(function() {
	$(".row_quantity, .row_price").on("change", function() {
		calc_row_total($(this));
		calc_bill_total();
	});

	$("#bill_type").on("change", function()
	{
		var type_id = $(this).val();
		if(type_id == 2)
		{
			$("#vat_total, #subtotal_div").show();
			var inputs = $(".items_container .row_price").toArray();
			$.each(inputs, function() {
				var _input = $(this);
				if(_input.data("nvat_price"))
				{
					_input.val(_input.data("nvat_price"));
					_input.trigger("change");
				}
			});
		}
		else
		{
			$("#vat_total, #subtotal_div").hide();
			var inputs = $(".items_container .row_price").toArray();
			$.each(inputs, function() {
				var _input = $(this);
				if(_input.data("sale_price"))
				{
					_input.val(_input.data("sale_price"));
					_input.trigger("change");
				}
			});
		}
		var object_data = $(this).select2("data")[0];
		if(object_data && object_data.next)
		{
			$("#bill_number").val(object_data.next);
		}
		if(type_id == "")
		{
			JSON.ticket++;
		}
		bill_type = type_id;
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
});
