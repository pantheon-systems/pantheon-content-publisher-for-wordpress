import axios from "axios";
export function showSpinner() {
	const spinnerBox = document.getElementById('spinner-box');
	const pccContent = document.getElementById('pcc-content');
	if (spinnerBox && pccContent) {
		hideErrorMessage();
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

/**
 * Get Access Token
 *
 * @returns {string}
 */
export function getAccessToken() {
	return document.getElementById('access-token')?.value;
}

/**
 * Show error message
 * @param message
 */
export function showErrorMessage(messages, showResetLink = false) {
	const errorMessageContainer = document.getElementById('pcc-error-message');
	const errorText = document.getElementById('pcc-error-text');
	if(typeof messages === 'string') {
		messages = [messages];
	}
	if (errorMessageContainer && errorText) {
		errorText.innerHTML = '';
		for (let index in messages) {
			if (index !== "0") {
				errorText.appendChild(document.createElement("br"));
			}
			let pTag = document.createElement("p");
			pTag.className = "text-sm text-black";
			pTag.textContent = messages[index];
			errorText.appendChild(pTag);
		}
		if (showResetLink) {
			let resetLink = document.createElement("a");
			resetLink.className = "text-red-600 font-semibold";
			resetLink.textContent = 'Click here to reset Google Workspace authentication.';
			resetLink.id = 'pcc-disconnect';
			resetLink.href = '#';
			resetLink.onclick = PccDisconnect;
			errorText.appendChild(resetLink);
		}
		errorMessageContainer.classList.remove('hidden');
	}
}

/**
 * Hide error message
 */
export function hideErrorMessage() {
	const errorMessageContainer = document.getElementById('pcc-error-message');
	if (errorMessageContainer){
		errorMessageContainer.classList.add('hidden');
	}
}

/**
 * Redirect to main page
 */
export const redirectToMainPage = () => {
	window.location.href = window.PCCAdmin.plugin_main_page
}

/**
 * Delete the API configuration details
 *
 * @returns {Promise<*>}
 */
export const deleteConfigDetails = async () => {
	const { rest_url, nonce } = window.PCCAdmin;
	const resp = await axios.delete(`${rest_url}/disconnect`,
		{headers: {'X-WP-Nonce': nonce}}
	);

	return resp
};

export const PccDisconnect = async () => {
	try {
		showSpinner();
		updateSpinnerText('Disconnecting your collection...')
		await deleteConfigDetails();
		redirectToMainPage();
	} catch (error) {
		showErrorMessage(`Error while disconnecting: ${error.response.data}`)
		hideSpinner();
	}
}
