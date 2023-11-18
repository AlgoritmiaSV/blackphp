/*
	Main Script
	By: Edwin Fajardo
	Date-Time: 2019-06-21 22:36
*/
// Variables globales para todos los scripts
content_height = 200;

$(function() {
	content_height = $(window).height() - $("#main_header").outerHeight() - $("#main_footer").outerHeight();
	if($(".list_options").length)
	{
		content_height -= $(".list_options").outerHeight();
		if($(".path_container").length)
		{
			content_height -= 1;
		}
	}
	if($("#main_nav").length && $("#main_nav").css("width") == $("#main_nav").parent().css("width") && screen.width >= 800)
	{
		content_height -= $("#main_nav").outerHeight();
	}
	if(content_height > 0)
	{
		$("#content_section").css("min-height", content_height + "px");
		$(".content_viewer").css("height", content_height + "px");
	}

	$(".nav_link").each(function() {
		if($(this).prop("href") == location.href)
		{
			$(this).addClass("nav_link_active");
		}
	});

	$(".nav_link").on("click", function(e) {
		$(".nav_link_active").removeClass("nav_link_active");
		$(this).addClass("nav_link_active");
	});

	/* User link */
	$("#user_link").on("click", function() {
		$("#main_aside").slideToggle("fast");
	});

	$(".logout_button").on("click", function() {
		$.ajax({
			method: "GET",
			url: "User/logout/",
			dataType: "json"
		})
		.done(function(json) {
			if(json.session)
			{
				$.jAlert({
					'title': "Error",
					'content': "No se ha podido cerrar sesión",
					'theme': "red",
					'autofocus': '.jalert_accept',
					'btns': [
						{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept'}]
				});
			}
			else
			{
				location.href = "/";
			}
		})
		.fail(function() {
			$.jAlert({
				'title': "Error",
				'content': "No se ha podido cerrar sesión",
				'theme': "red",
				'autofocus': '.jalert_accept',
				'btns': [
					{'text':'Aceptar', 'closeAlert':true, 'theme': 'red', 'class': 'jalert_accept'}]
			});
		});
	});

	/* Manage click on all links */
	$("a").not(".link_exclude").on("click", function(e) {
		e.preventDefault();
		var href = $(this).attr("href");
		if(href == "#")
		{
			return false;
		}
		if(href == "#alert")
		{
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
		}
		if(href.indexOf("#") == 0)
		{
			return false;
		}
		if($(this).prop("href") == location.href)
		{
			return false;
		}
		if(href.indexOf("http") == 0)
		{
			window.open(href, "_blank");
		}
		else
		{
			window.location.href = href;
		}
	});

	/* Manage URL format */
	function url_split()
	{
		var pathname = $(location).attr("pathname");
		if(pathname.indexOf("/") == 0)
		{
			pathname = pathname.substring(1);
		}
		if(pathname.slice(-1) == "/")
		{
			pathname = pathname.substring(0, pathname.length - 1);
		}
		var path = pathname.split("/");
		var url_object = {
			//section: path[0],
			module: path[0],
			method: path[1],
			id: path[2],
			options: {} };

		if(path.length > 3)
		{
			for(i = 2; i < path.length; i += 2)
			{
				url_object.options[path[i]] = path[i + 1];
			}
		}

		return url_object;
	}
	url = url_split();

	connection_fails = 0;
	function keep_alive()
	{
		$.ajax({
			method: "POST",
			url: "Resources/keep_alive/",
			data: url,
			dataType: "json"
		})
		.done(function(json) {
			if(json.alive)
			{
				connection_fails = 0;
			}
			else
			{
				location.reload();
			}
		})
		.fail(function() {
			connection_fails++;
			if(connection_fails > 1)
			{
				location.reload();
			}
		});
	}

	setInterval(keep_alive, 30000);

	$(".link_button").on("click", function() {
		window.open($(this).data("href"), "_top");
	});

	/* Date Picker */
	set_date_picker = function()
	{
		$(this).removeClass("hasDatepicker");
		$(this).removeAttr("id");
		$(this).datepicker({
			dateFormat: $(this).data("format") || "dd/mm/yy",
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ],
			dayNamesMin: [ "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab" ],
		});
	}
	$(".date_input").each(set_date_picker);

	/* Before print */
	window.onbeforeprint = function() {
		$(".content_viewer").css({
			"overflow-y": "auto",
			"height": "auto"
		});
	}

	$(".menu_item").on("click", function() {
		$(this).css({
			"opacity":"0.1",
			"transform":"scale(2)"
		});
	});

	$("#nav_content a").each(function() {
		a_module = $(this).attr("href").replace("/","");
		if(a_module == url.module)
		{
			$(this).addClass("nav_link_active");
		}
	});
	/* Keymap */
	$("body").on('keydown', event => {
		if ((event.key =='I' || event.key =='i') && event.altKey)
		{
			location.href = "/";
		}
	});
	$(document).tooltip();

	$("#menu_button").on("click", function() {
		if($(this).attr("href") == "#")
		{
			$("#main_nav").slideToggle("slow");
		}
	});

	/* Tabs */
	$( "#tabs" ).accordion({
		collapsible: true,
		heightStyle: "content",
		activate: function() {
			$("textarea").trigger("input");
		}
	});

	$(".back_button").on("click", function() {
		history.back();
	});
});
