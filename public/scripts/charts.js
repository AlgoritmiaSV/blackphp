$(function() {
	init_chart = function()
	{
		$(".myChart").each(function() {
			create_chart($(this));
		});
	}

	function create_chart(element)
	{
		var ctx = element;
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: element.data("labels").split(","),
				datasets: [{
					label: 'Metros cúbicos',
					data: element.data("data").split(","),
					backgroundColor: [
						'#000',
						'#000',
						'#000',
						'#000',
						'#000',
						'#000'
					]
				}]
			},
			options: {
				scales: {
					y: {
						beginAtZero: true
					}
				},
				plugins: {
					datalabels: { // This code is used to display data values
						anchor: 'end',
						align: 'top',
						formatter: Math.round,
						font: {
							weight: 'bold'
						}
					}
				}
			},
			plugins: [ChartDataLabels],
			legend: {
				display: false,
			},
			tooltips: {
				callbacks: {
					label: tooltipItem => `${tooltipItem.yLabel}: ${tooltipItem.xLabel}`, 
					title: () => null,
				}
			}
		});

		container = element.parent().next(".small_chart").first();
		img = $(document.createElement("img"));
		img.attr("src", myChart.toBase64Image());
		img.css("width", "100%");
		container.html(img);
		element.parent().remove();
	}
});
