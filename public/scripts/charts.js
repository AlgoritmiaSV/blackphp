$(function() {
	init_chart = function()
	{
		$(".myChart").each(function() {
			create_chart($(this));
		});
	}

	/**
	 * Crear un gráfico
	 * 
	 * Por el momento este método está específico para el uso en una empresa de servicio de 
	 * agua potable. Falta generalizarlo.
	 * 
	 * @todo Generalizar el método
	 * 
	 * @param {object} element El canvas sobre el que se va a crear el gráfico
	 */
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
						'#666',
						'#666',
						'#666',
						'#666',
						'#666',
						'#666'
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

		var container = element.parent().next(".small_chart").first();
		var img = $(document.createElement("img"));
		img.attr("src", myChart.toBase64Image());
		img.css("width", "100%");
		container.html(img);
		element.parent().remove();
	}
});
