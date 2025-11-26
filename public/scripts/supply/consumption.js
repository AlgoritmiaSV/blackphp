start_calc_consumption = function()
{
	$(".current").on("change", function() {
		tr = $(this).closest("tr");
		var previous_value = parseInt(tr.find(".previous").val());
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

	// Cambios de medidor
	const checkboxes = document.querySelectorAll('input.meter_replacement');

	checkboxes.forEach(function (checkbox) {
		checkbox.addEventListener("change", function () {
			// Find the row containing this checkbox
			const row = checkbox.closest("tr");

			// Find the "previous" input in the same row
			const previousInput = row.querySelector('input[name="initial_reading[]"]');
			const readingInput = row.querySelector('input[name="reading[]"]');
			
			if (checkbox.checked) {
				// Save the current value before overwriting
				previousInput.dataset.oldValue = previousInput.value;

				// Enable and set to zero
				previousInput.readOnly = false;
				previousInput.value = 0;
			} else {
				// Restore the old value if it exists
				if (previousInput.dataset.oldValue !== undefined) {
				previousInput.value = previousInput.dataset.oldValue;
				}

				// Disable again
				previousInput.readOnly = true;
			}

			// Trigger change event on the reading input
			if (readingInput) {
				readingInput.dispatchEvent(new Event("change", { bubbles: true }));
			}
		});
	});

	// Cambios de medidor
	const previousInputs = document.querySelectorAll('input[name="previous[]"]');

	previousInputs.forEach(function (checkbox) {
		checkbox.addEventListener("change", function () {
			// Find the row containing this checkbox
			const row = checkbox.closest("tr");

			// Find the "reading" input in the same row
			const readingInput = row.querySelector('input[name="reading[]"]');

			// Trigger change event on the reading input
			if (readingInput) {
				readingInput.dispatchEvent(new Event("change", { bubbles: true }));
			}
		});
	});
};

$(function() {
	$(".current_reading").on("change", function()
	{
		const previous_value = parseInt($(".previous_reading").val());
		const current_value = parseInt($(this).val());
		let consumption_input = $(".consumption");
		if(isNaN(previous_value) || isNaN(current_value))
		{
			return false;
		}
		let consumption = current_value - previous_value;
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
			consumption_input.val("");
		}
		else
		{
			consumption_input.val(consumption);
		}
	});

	$(".previous_reading").on("change", function()
	{
		$(".current_reading").attr("min", $(this).val());
	});
});
