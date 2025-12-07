document.addEventListener('DOMContentLoaded', () => {
const passwordFields = document.querySelectorAll('input[type="password"]');

passwordFields.forEach(input => {
	const wrapper = input.closest('.form_entry, .entry');

	// Create eye icon
	const eye = document.createElement('span');
	eye.classList.add('eye_icon');
	eye.innerHTML = '&#128065;'; // 👁️ Unicode eye icon

	// Insert icon into wrapper
	wrapper.appendChild(eye);

	// Event listeners for press-and-hold reveal
	eye.addEventListener('mousedown', () => {
	input.type = 'text';
	});

	eye.addEventListener('mouseup', () => {
	input.type = 'password';
	});

	eye.addEventListener('mouseleave', () => {
	input.type = 'password';
	});

	// Optional: touch support for mobile
	eye.addEventListener('touchstart', () => {
	input.type = 'text';
	});

	eye.addEventListener('touchend', () => {
	input.type = 'password';
	});
});
});
