/**
 * order.js
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * Date-Time: 2021-10-11 15:28
 */
$(function(){
	$(".price_input, .quantity_input").on("change", update_total);

	$(".order_item a").on("click", function() {
		var input = $(this).parent().find(".quantity_input");
		var value = input.val() == "" ? 0 : parseInt(input.val());
		input.val(value + 1);
		update_total();
	});

	function update_total()
	{
		var total_price = 0;
		$(".order_item").each(function() {
			var order_price = $(this).find(".price_input").val() * $(this).find(".quantity_input").val();
			total_price += order_price;
		});
		$("#form_total").text(total_price.toFixed(2));
	}
});
