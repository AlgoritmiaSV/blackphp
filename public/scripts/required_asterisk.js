function markRequiredFields() {
	document.querySelectorAll('.form_entry').forEach(entry => {
		const label = entry.querySelector('label');
		const requiredField = entry.querySelector('input[required], textarea[required], select[required]');

		if (label && requiredField && !label.querySelector('.required-asterisk')) {
			label.innerHTML = label.innerHTML.replace(/:$/, '');

			const span = document.createElement('span');
			span.className = 'required-asterisk';
			span.textContent = ' *';

			label.appendChild(span);
			label.append(':');
		}
	});
}

document.addEventListener('DOMContentLoaded', markRequiredFields);
