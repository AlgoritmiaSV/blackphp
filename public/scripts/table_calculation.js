/**
 * Cálculo de tablas
 * 
 * Asignación de eventos y ejecución de funciones de forma dinámica para el cálculo de tablas.
 * Para la utilización de esta funcionalidad, cada tbody debe tener al menos uno de los
 * siguientes atributos:
 * - data-row-calculation: Nombre de la función que calcula una fila
 * - data-table-calculation: Nombre de la función que realiza cálculo en toda la tabla
 * 
 * Asimismo, para disparar el evento, se necesita que cada input contenga la clase calculate_row
 * 
 * Este script contiene lo siguiente:
 * - Delegación de evento: una llamada a la función correspondiente configurada al detectar
 *   cambios en los input con clase calculate_row
 * - Procedimiento para agregar nuevas filas a las tablas
 * - Llamada de forma manual a las funciones de cálculo de tablas
 * 
 * Incorporado el 2026-04-04
 */
document.addEventListener("input", function(e) {
	if (e.target.classList.contains("calculate_row")) {
		const tbody = e.target.closest("tbody");
		const rowCalcFn = tbody.dataset.rowCalculation;
		const tableCalcFn = tbody.dataset.tableCalculation;

		// Call row calculation
		if (rowCalcFn && typeof window[rowCalcFn] === "function") {
			window[rowCalcFn](e.target.closest("tr"));
		}

		// Call table calculation (if defined)
		if (tableCalcFn && typeof window[tableCalcFn] === "function") {
			window[tableCalcFn](tbody);
		}
	}
});

document.addEventListener('DOMContentLoaded', function (){
	document.querySelectorAll(".add_row_button").forEach(button => {
		button.addEventListener("click", function() {
			const tbodyId = this.dataset.tbody;
			const tbody = document.getElementById(tbodyId);

			// Check max items limit
			const maxItemsAttr = tbody.getAttribute("data-max-items");
			const maxItems = maxItemsAttr ? parseInt(maxItemsAttr, 10) : 0;
			const currentRows = tbody.querySelectorAll("tr").length;

			if (maxItems > 0 && currentRows >= maxItems) {
				// Prevent adding more rows
				Swal.fire({
					title: "Limit reached",
					text: `You can only add up to ${maxItems} rows in this table.`,
					icon: "info",
					confirmButtonText: "OK"
				});
				return;
			}
		
			tbody.querySelectorAll("td.td_active").forEach(cell => {
				cell.classList.remove("td_active");
			});

			// Clone the last row
			const lastRow = tbody.querySelector("tr:last-child");
			if (!lastRow) return;

			const newRow = lastRow.cloneNode(true);

			// Clear inputs and spans
			newRow.querySelectorAll("input").forEach(input => input.value = "");
			newRow.querySelectorAll("select").forEach(select => select.value = "");
			newRow.querySelectorAll("span").forEach(span => span.textContent = "");

			// Valores completos y parciales
			newRow.querySelector(".complete_value").textContent = "";
			newRow.querySelector(".complete_value").style.display = "none";
			newRow.querySelector(".partial_value").style.display = "initial";

			// Append new row
			tbody.appendChild(newRow);

			// Update row numbers
			tbody.querySelectorAll(".row_number").forEach((span, i) => {
				span.textContent = i + 1;
			});

			tbody.querySelectorAll(".delete_row_icon").forEach(icon => icon.style.visibility = "visible");

			if(typeof(build_autocomplete) == "function")
			{
				build_autocomplete($(newRow));
			}
			if(typeof(build_selectors) == "function")
			{
				build_selectors();
			}
		});
	});
});

function PerformTableCalculation()
{
	document.querySelectorAll(".items_container").forEach(tbody => {
		const rowCalcFn = tbody.dataset.rowCalculation;
		const tableCalcFn = tbody.dataset.tableCalculation;

		// Call row calculation
		if (rowCalcFn && typeof window[rowCalcFn] === "function") {
			window[rowCalcFn](e.target.closest("tr"));
		}

		// Call table calculation (if defined)
		if (tableCalcFn && typeof window[tableCalcFn] === "function") {
			window[tableCalcFn](tbody);
		}
	});
}

// Handle delete button clicks for any table with SweetAlert confirmation
document.addEventListener("click", function(e) {
    if (e.target.closest(".delete_row_icon")) {
		e.preventDefault();
        const button = e.target.closest(".delete_row_icon");
        const row = button.closest("tr");
        const tbody = row.closest("tbody");

        // Show SweetAlert confirmation
        Swal.fire({
            title: "Are you sure?",
            text: "This row will be permanently deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove the row
                row.remove();

                // Update row numbers
                tbody.querySelectorAll(".row_number").forEach((span, i) => {
                    span.textContent = i + 1;
                });

                // Optionally re-run table calculation
                const tableCalcFn = tbody.dataset.tableCalculation;
                if (tableCalcFn && typeof window[tableCalcFn] === "function") {
                    window[tableCalcFn](tbody);
                }

                // Success feedback
                // Swal.fire("Deleted!", "The row has been removed.", "success");

				if(tbody.querySelectorAll("tr").length < 2)
				{
					tbody.querySelector(".delete_row_icon").style.visibility = "hidden";
				}
            }
        });
    }
});

// Handle cell clicks for any table body
document.addEventListener("click", function(e) {
    const td = e.target.closest("td");
    if (!td) return;

    const tbody = td.closest(".items_container");
    if (!tbody) return;

    // Remove td_active from all cells in this tbody
    tbody.querySelectorAll("td.td_active").forEach(cell => {
        cell.classList.remove("td_active");
    });

    // Add td_active to the clicked cell
    td.classList.add("td_active");
});
