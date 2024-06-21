export function showSpinner() {
	const spinnerBox = document.getElementById('spinner-box');
	const pccContent = document.getElementById('pcc-content');
	if (spinnerBox && pccContent) {
		spinnerBox.classList.remove('hidden');
		pccContent.classList.add('hidden');
	}
}

export function hideSpinner() {
	const spinnerBox = document.getElementById('spinner-box');
	const pccContent = document.getElementById('pcc-content');

	if (spinnerBox && pccContent) {
		spinnerBox.classList.add('hidden');
		pccContent.classList.remove('hidden');
	}
}

export function updateSpinnerText(text) {
	const spinnerText = document.getElementById('spinner-text');
	if (spinnerText) {
		spinnerText.textContent = text;
	}
}

/**
 * Get selected post type
 *
 * @returns {string}
 */
export function getSelectedPostType() {
	return document.querySelector('input[name="post_type"]:checked')?.value;
}

