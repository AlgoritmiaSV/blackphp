/*
#	Lists viewer
#	By: Edwin Fajardo
#	Date-time: 2020-06-18 13:17
*/
jQuery(function($) { $.extend({
	form: function(url, data, method) {
		if (method == null) method = 'POST';
		if (data == null) data = {};

		var form = $('<form>').attr({
			method: method,
			action: url
		}).css({
			display: 'none'
		});

		var addData = function(name, data) {
			if (Array.isArray(data)) {
				for (var i = 0; i < data.length; i++) {
					var value = data[i];
					addData(name + '[]', value);
				}
			} else if (typeof data === 'object') {
				for (var key in data) {
					if (data.hasOwnProperty(key)) {
						addData(name + '[' + key + ']', data[key]);
					}
				}
			} else if (data != null) {
				form.append($('<input>').attr({
					type: 'hidden',
					name: String(name),
					value: String(data)
				}));
			}
		};

		for (var key in data) {
			if (data.hasOwnProperty(key)) {
				addData(key, data[key]);
			}
		}

		return form.appendTo('body');
	}
}); });
$( function()
{
	if(window.jspdf != null)
	{
		window.jsPDF = window.jspdf.jsPDF;
	}
	/* Data viewer */
	$(".data_viewer").each(function() {
		load_table($(this).attr("id"));
	});

	function load_table(table_id)
	{
		var _table = $("#" + table_id);
		var _template = _table.find(".template").clone().removeClass("template").prop("outerHTML");
		$.ajax({
			method: "POST",
			url: url.module + "/" + table_id + "_loader/",
			data: url,
			dataType: "json"
		})
		.done(function(data) {
			var empty_table = true;
			if(data.content != null)
			{
				if(data.content.length > 0)
				{
					empty_table = false;
				}
				$.each(data.content, function(index, value) {
					var tr = _template;
					$.each(value, function(e_index, e_value) {
						tr = tr.replace(new RegExp("{{" + e_index + "}}", 'g'), e_value);
					});
					$("#" + table_id + " tbody").append(tr);
				});
				$("#" + table_id + " tbody tr").on("click", function() {
					if($(this).data("href"))
					{
						location.href = $(this).data("href") + "/" + $(this).data("id") + "/"
					}
					else if($(this).data("alert"))
					{
						$.jAlert({
							'title': $(this).data("title") || false,
							'theme': $(this).data("theme") || "blue",
							'iframe': $(this).data("alert"),
							'size': {
								"height": content_height + "px",
								"width": "100%"
							},
							'iframeHeight': (content_height - 41) + "px",
							'noPadContent':true
						});
					}
				});
				if(data.foot)
				{
					$.each(data.foot, function(index, value) {
						tr = $("#" + table_id + " tfoot").html();
						tr = tr.replace(new RegExp("{{" + index + "}}", 'g'), value);
						$("#" + table_id + " tfoot").html(tr);
					});
				}
			}
			if(data.found_rows != null)
			{
				var current_page = 0;
				if(url.options.page == null)
				{
					current_page = data.found_rows > 0 ? 1 : 0;
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
					max_page: Math.ceil(data.found_rows / 100),
					current_page: current_page,
					page_string: "{current_page} / {max_page}"
				});
			}
			_table.show();
			if(empty_table)
			{
				$(".empty_table_message").show();
			}
			if(!_table.data("type"))
			{
				_table.floatThead({
					'scrollContainer': true
				});
			}

			/** Relleno con celdas vacías */
			var void_tr = _template.replaceAll(/\{\{[A-Za-z0-9_]+\}\}/g, "");
			void_tr = $(void_tr);
			void_tr.find("td").html("&nbsp;");
			void_tr.addClass("void_tr");
			while(_table.outerHeight() + 45 < content_height)
			{
				$("#" + table_id + " tbody").append(void_tr.clone());
			}

			/* Fill content outside table after load */
			if(data.load_after)
			{
				$.each(data.load_after, function(index, value) {
					$("." + index).text(value);
				});
			}
		})
		.fail(function() {
			$("div.loading_error").show();
		})
		.always(function() {
			$("div.loading_data").hide();
		});
	}

	/* Data tables */
	$(".data_table").each(function() {
		load_data_table($(this).attr("id"));
		$(".content_viewer").css("overflow", "hidden");
	});

	function load_data_table(table_id)
	{
		var _table = $("#" + table_id);
		var _template = _table.find(".template").clone().removeClass("template").prop("outerHTML");
		$.ajax({
			method: "POST",
			url: url.module + "/" + table_id + "_loader/",
			data: url,
			dataType: "json"
		})
		.done(function(data) {
			if(data.content != null)
			{
				$.each(data.content, function(index, value) {
					var tr = _template;
					$.each(value, function(e_index, e_value) {
						tr = tr.replace(new RegExp("{{" + e_index + "}}", 'g'), e_value);
					});
					$("#" + table_id + " tbody").append(tr);
				});
				$("#" + table_id + " tbody tr").on("click", function(e) {
					if($(this).data("href"))
					{
						location.href = $(this).data("href") + "/" + $(this).data("id") + "/"
					}
					if($(this).data("alert"))
					{
						$.jAlert({
							'title': $(this).data("title") || false,
							'theme': $(this).data("theme") || "blue",
							'iframe': $(this).data("alert"),
							'size': {
								"height": content_height + "px",
								"width": "100%"
							},
							'iframeHeight': (content_height - 41) + "px",
							'noPadContent':true
						});
					}
				});
				if(data.foot)
				{
					$.each(data.foot, function(index, value) {
						tr = $("#" + table_id + " tfoot").html();
						tr = tr.replace(new RegExp("{{" + index + "}}", 'g'), value);
						$("#" + table_id + " tfoot").html(tr);
					});
				}
			}
			_table.find("tr.template").remove();
			var dtable = _table.DataTable({
				responsive: true,
				paging: false,
				fixedHeader: {
					header: true,
					footer: true
				},
				scrollY: $(".content_viewer").height() - 65,
				language: {
					url: '/Resources/datatables_language/' + $("html").attr("lang")
				},
				dom: 'lrtip'
			});
			/* Fill content outside table after load */
			if(data.load_after)
			{
				$.each(data.load_after, function(index, value) {
					$("." + index).text(value);
				});
			}
			/* Data serach */
			$(".data_search").on("keyup", function()
			{
				dtable.search($(this).val()).draw();
			});
		})
		.fail(function() {
			$("div.loading_error").show();
		})
		.always(function() {
			$("div.loading_data").hide();
		});
	}

	/* Content loader */
	$(".content_loader").each(function() {
		load_content($(this).attr("id"));
	});

	function load_content(div_id)
	{
		var _div = $("#" + div_id);
		module = url.module || "index";
		$.ajax({
			method: "POST",
			url: module + "/" + div_id + "_loader/",
			data: url,
			dataType: "html"
		})
		.done(function(data) {
			_div.html(data);
			$(".menu_item").on("click", function() {
				$(this).css({
					"opacity":"0.1",
					"transform":"scale(2)"
				});
			});
			$("#main_content").has(".details_content").addClass("details_main_content");
			$(".close_details").on("click", function(e) {
				e.preventDefault();
				$(".details_content").css({
					"opacity":"0.1",
					"transform":"scale(0.1)"
				});
				history.back();
			});
			_div.find(".link_button").on("click", function() {
				window.open($(this).data("href"), "_top");
			});
			_div.find(".alert_button").on("click", function() {
				var alert_href = $(this).data("href");
				$.jAlert({
					'title': $(this).data("title") || false,
					'theme': $(this).data("theme") || "blue",
					'iframe': alert_href,
					'size': {
						"height": content_height + "px",
						"width": "100%"
					},
					'iframeHeight': (content_height - 41) + "px",
					'noPadContent':true
				});
				return false;
			});
			_div.find(".print_button").on("click", function() {
				print_header = null;
				if($(".print_header").length)
				{
					print_header = $(".print_header").html();
				}
				print_footer = null;
				if($(".print_footer").length)
				{
					print_footer = $(".print_footer").html();
				}
				$($(this).data("print")).printThis({
					'loadCSS': $(this).data("css") || "",
					'header': print_header,
					'footer': print_footer
				});
			});
			_div.find(".pdf_button").on("click", function() {
				/* Destroy floatThead to avoid conficts with printThis */
				var html = "";
				var print_header = "";
				if($(".print_header").length)
				{
					print_header = $(".print_header").html();
				}
				var print_footer = "";
				if($(".print_footer").length)
				{
					print_footer = $(".print_footer").html();
				}
				html = print_header + $($(this).data("content")).html() + print_footer;
				getPDF(html);
			});
			_div.find(".filter_button").on("click", function() {
				$(".action_filter").toggle("slow");
			});
			$(".open_dialog_button").on("click", function() {
				$("#" + $(this).data("dialog")).dialog("open");
			});
			if($(".delete_button").length)
			{
				$(".delete_button").on("click", delete_button_click);
			}
			//Show data viewer
			_div.find(".data_viewer").show();

			/* Link row */
			$(".link_row").on("click", function() {
				if($(this).data("href") != "#")
				{
					location.href = $(this).data("href");
				}
			});

			/* Charts */
			var ctx = $(".myChart");
			if(ctx.length > 0)
			{
				init_chart();
			}
		})
		.fail(function() {
			$("div.loading_error").show();
		})
		.always(function() {
			$("div.loading_data").hide();
		});
	}

	if($(".data_viewer").length)
	{
		$(".data_search").on("keyup", function() {
			var string_value = $(this).val();
			$(".data_viewer tbody tr").not(".template").each(function() {
				found = false;
				$(this).find("td").each(function() {
					if($(this).text().toUpperCase().indexOf(string_value.toUpperCase()) >= 0)
					{
						found = true;
					}
				});
				if(found)
				{
					$(this).show();
				}
				else
				{
					$(this).hide();
				}
			});
		});
	}

	/* Filter loader */
	//filter_content = [];
	$(".data_filter").each(function() {
		if($(this).is("select"))
		{
			load_filter($(this).attr("id"));
		}
		else
		{
			$(this).on("change", function(){
				if($(this).is(".date_input"))
				{
					$(this).data("value", $(this).val());
					$(this).val($.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate")));
				}
				url.options[$(this).data("identifier")] = $(this).val();
				goto_url();
			});
		}
	});

	function load_filter(selector_id)
	{
		var action_module = url.module || "index";
		var _selector = $("#" + selector_id);
		if(_selector.data("module") != null)
		{
			action_module = _selector.data("module");
		}
		$.ajax({
			method: "POST",
			url: action_module + "/" + selector_id + "_loader/",
			data: url,
			dataType: "json"
		})
		.done(function(json_data) {
			if(json_data.results)
			{
				var select_default_width = screen.width < 750 ? '100%' : 'fit-content';
				var select_params = {
					data: json_data.results,
					dropdownAutoWidth: true,
					placeholder: _selector.data("placeholder") || "",
					width: _selector.data("width") || select_default_width
				}
				if(_selector.data("search") != "none" || _selector.data("default") == "none")
				{
					item = $(document.createElement("option"));
					item.appendTo(_selector);
				}
				if(_selector.data("search") == "none")
				{
					select_params.minimumResultsForSearch = Infinity;
				}
				_selector.select2(select_params);
				if(_selector.data("value") != null)
				{
					_selector.val(_selector.data("value"));
					_selector.trigger('change.select2');
				}
				if(_selector.data("identifier"))
				{
					if(url.options[_selector.data("identifier")])
					{
						_selector.val(url.options[_selector.data("identifier")]);
						_selector.trigger('change.select2');
					}
					_selector.on("change", function(){
						url.options[_selector.data("identifier")] = _selector.val();
						if(_selector.data("reset"))
						{
							fields = _selector.data("reset").split(",");
							$.each(fields, function() {
								if(url.options[this])
								{
									url.options[this] = null;
								}
							});
						}
						goto_url();
					});
				}
			}
		})
		.fail(function() {
			_selector.hide();
		})
		.always(function() {
		});
	}

	$(".filter_button").on("click", function() {
		$(".action_filter").toggle("slow");
	});

	/* Go To URL */
	function goto_url()
	{
		if(url.method == null)
		{
			url.method = "listar";
		}
		var href = url.module + "/" + url.method + "/";
		$.each(url.options, function(key, value) {
			if(value != null && value != 0)
			{
				href += key + "/" + value + "/";
			}
		});
		
		location.href = href;
	}

	/** Download link */
	$(".download_link").on("click", function() {
		$.form($(this).data("url"), url).submit();
	});

	/* Print button */
	$(".print_button").on("click", function() {
		/* Destroy floatThead to avoid conficts with printThis */
		if($(".data_viewer").length > 0)
		{
			$($(this).data("print")).floatThead('destroy');
		}
		var rand = Math.floor(Math.random() * 100000);
		var print_header = null;
		if($(".print_header").length)
		{
			print_header = $(".print_header").html();
		}
		var print_footer = null;
		if($(".print_footer").length)
		{
			print_footer = $(".print_footer").html();
		}
		var css = "";
		if($(this).data("print") == ".data_viewer" || $(this).data("print") == ".data_table")
		{
			css = 'public/styles/print_list.css?r=' + rand;
		}
		if($($(this).data("print")).data("css") !== undefined)
		{
			css = $($(this).data("print")).data("css");
		}
		$($(this).data("print")).printThis({
			'loadCSS': css,
			'header': print_header,
			'footer': print_footer
		});
	});

	/** PDF button */
	$(".pdf_button").on("click", function() {
		/* Destroy floatThead to avoid conficts with printThis */
		if($(".data_viewer").length > 0)
		{
			$($(this).data("print")).floatThead('destroy');
		}
		var html = "";
		var print_header = "";
		if($(".print_header").length)
		{
			print_header = $(".print_header").html();
		}
		var print_footer = "";
		if($(".print_footer").length)
		{
			print_footer = $(".print_footer").html();
		}
		html = print_header;
		$($(this).data("content")).each(function() {
			html += $(this).html();
		});
		html += print_footer;
		getPDF(html);
	});

	function getPDF(html)
	{
		div = $(document.createElement('div'));
		div.css("width", "720px");
		div.html(html);
		$("body").append(div);
		var HTML_Width = div.width();
		var HTML_Height = div.height();
		var top_left_margin = 48;
		var PDF_Width = HTML_Width+(top_left_margin*2);
		var PDF_Height = 958+(top_left_margin*2);
		var canvas_image_width = HTML_Width;
		var canvas_image_height = HTML_Height;
		
		var totalPDFPages = Math.ceil(HTML_Height/PDF_Height)-1;

		html2canvas(div[0],{
			allowTaint: true,
			scale: 2
		}).then(function(canvas) {
			canvas.getContext('2d');
			
			console.log(canvas.height+"  "+canvas.width);
			
			
			var imgData = canvas.toDataURL("image/jpeg", 1.0);
			var pdf = new jsPDF('p', 'pt',  [PDF_Width, PDF_Height]);
			pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin,canvas_image_width,canvas_image_height);
			
			
			for (var i = 1; i <= totalPDFPages; i++) { 
				pdf.addPage(PDF_Width, PDF_Height);
				pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
			}
			
			pdf.save("HTML-Document.pdf");
		});
		div.remove();
	};
});
