$(function() {
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
});
