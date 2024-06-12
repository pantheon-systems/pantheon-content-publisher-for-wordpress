import AddOnApiHelper from "./addonApiHelper";
import {persistDetailsToDatabase} from "./localStorage";

/**
 * Check code is provided in URL
 *
 * @returns {boolean}
 */
export const checkCheckCodeInURL = () => {
	// check URL params contains code
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.get('code')) {
		return true;
	}
	return false;
};

/**
 * Fetch token and save credentials to database
 *
 * @returns {boolean}
 */
export const getCodeFromURL = () => {
	const urlParams = new URLSearchParams(window.location.search);
	let code = urlParams.get('code');
	if (code) {
		return code;
	}
	return false;
};

/**
 * Fetch token and save credentials to database
 *
 * @param code
 * @returns {Promise<void>}
 */
export const fetchTokenAndSaveCredentials = async (code) => {
	try {
		let credentials = await AddOnApiHelper.getToken(code);
		await persistDetailsToDatabase(credentials);
		return true;
	} catch (e) {
		console.log('Error while fetching token and saving credentials to database', e);
	}
	return false;
};

/**
 * Redirect to main page
 */
export const redirectToMainPage = () => {
	window.location.href = window.PCCAdmin.plugin_main_page
}
