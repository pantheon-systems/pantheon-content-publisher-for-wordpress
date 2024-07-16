import {deleteConfigDetails} from "./lib/localStorage";
import {fetchTokenAndSaveCredentials, getCodeFromURL, redirectToMainPage} from "./lib/oauthHelper";
import createSite from "./createSite";
import {hideErrorMessage, hideSpinner, showErrorMessage, showSpinner, updateSpinnerText} from "./helper";
import updatePostType from "./updatePostType";
import authenticate from "./authenticate";

console.info('window.PCCAdmin.credentials', window.PCCAdmin.credentials);

if (document.getElementById('pcc-app-authenticate') != undefined) {
	document.getElementById('pcc-app-authenticate').addEventListener('click', function () {
		authenticate([]);
	});
}

if (document.getElementById('pcc-create-site') != undefined) {
	document.getElementById('pcc-create-site').addEventListener('click', async function (e) {
		try {
			showSpinner();
			await createSite();
			redirectToMainPage();
		} catch (error) {
			showErrorMessage(`Error while creating site: ${error.message}`)
		} finally {
			hideSpinner();
		}
	});
}

if (document.getElementById('pcc-update-collection') != undefined) {
	document.getElementById('pcc-update-collection').addEventListener('click', async function () {
		try {
			await updatePostType();
			redirectToMainPage();
		} catch (error) {
			showErrorMessage(`Error while creating site: ${error.message}`)
		} finally {
		}
	});
}

if (document.getElementById('pcc-disconnect') != undefined) {
	document.getElementById('pcc-disconnect').addEventListener('click', async function () {
		try {
			showSpinner();
			updateSpinnerText('Disconnecting your collection...')
			await deleteConfigDetails();
			redirectToMainPage();
		} catch (error) {
			showErrorMessage(`Error while disconnecting: ${error.message}`)
		} finally {
		}
	});
}

if (document.getElementById('pcc-error-close-button') != undefined) {
	document.getElementById('pcc-error-close-button').addEventListener('click', function () {
		hideErrorMessage();
	});
}

// fetch token and save credentials if code is provided in URL
let code = getCodeFromURL();
if (code) {
	let saved = await fetchTokenAndSaveCredentials(code)
	if (saved) {
		redirectToMainPage();
	} else {
		showErrorMessage('Error while saving credentials')
	}
}
