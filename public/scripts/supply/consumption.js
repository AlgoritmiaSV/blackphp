start_calc_consumption = function() {
	$(".current").on("change", function() {
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
	});
};
