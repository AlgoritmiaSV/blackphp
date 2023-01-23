/*
	Trees
	By: Edwin Fajardo
	Date-time: 2021-08-12 13:57
*/
$(function() {
	$(".parent_checkbox").on("click", function() {
		var parent_item = $(this).closest(".parent_item");
		parent_item.find(".child_checkbox").prop("checked", $(this).prop("checked"));
	});

	$(".child_checkbox").on("click", function() {
		var parent_checkbox = $(this).closest(".parent_item").find(".parent_checkbox");
		var container = $(this).closest(".children_container");
		if($(this).prop("checked"))
		{
			parent_checkbox.prop("checked", true);
		}
		else
		{
			if(container.find("input:checked").length == 0)
			{
				parent_checkbox.prop("checked", false);
			}
		}
	});

	if(screen.width >= 800)
	{
		$(".children_container").sortable();
	}
});
