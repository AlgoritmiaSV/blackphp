# Registro de cambios

Registro de todos los cambios notables en el proyecto BlackPHP.

## Unreleased

### Added

- Registro de cambios
- Soporte para acceso a través de API
  - Instalación de librería JWT
  - Creación de una clase estándar para respuestas de API ApiResponse
  - V1 Clase para acceso API

### Changed

- Indentación de 4 espacios de acuerdo al estándar PSR-12
- Todas las llamadas a la clase date_utilities, se redirigieron a Dates
- Todas las llamadas a la clase text_utilities, se redirigieron a Texts
- Se modificaron todas las respuestas de http::json por ApiResponse
- Se modificó el envío de formaularios AJAX para adaptar la nueva estructura de API
- En el llenado de tablas de AJAX, ahora se reciben los datos dentro de la estructura data
- En la carga de filtros, resp.results ahora es resp.data
- Los parámetros reload_after, print_after, redirect_after en las respuestas JSON, parason a ser reload, print y redirect respectivamente. Se entenderá que, si hay un mensaje en el campo message, éste se mostrará antes de la ejecución de las acciones mencionadas.

### Removed

- Se eliminaron los parámetros: saved, deleted, changed y theme de las respuestas.
