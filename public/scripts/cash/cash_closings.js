$(function() {
	$("#count").on("change", function() {
		var balance = parseFloat($("#balance").val());
		var count = parseFloat($(this).val());
		var diff = 0;
		if(count < balance)
		{
			diff = balance - count;
			$("#shortage").val(diff.toFixed(2));
		}
		else
		{
			$("#shortage").val("0.00");
		}
		if(count > balance)
		{
			diff = count - balance;
			$("#overage").val(diff.toFixed(2));
		}
		else
		{
			$("#overage").val("0.00");
		}
	});
});
