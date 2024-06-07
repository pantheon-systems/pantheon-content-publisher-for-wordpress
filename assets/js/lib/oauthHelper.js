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
		return true;
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
	let credentials = await AddOnApiHelper.getToken(code);
	await persistDetailsToDatabase(credentials);
};

/**
 * Redirect to main page
 */
export const redirectToMainPage = () => {
	window.location.href = window.location.origin + '/wp-admin/admin.php?page=pcc'
}
