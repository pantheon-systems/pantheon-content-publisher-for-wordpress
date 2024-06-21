import {deleteConfigDetails} from "./lib/localStorage";

require('./login');

import login from './login';
import {fetchTokenAndSaveCredentials, getCodeFromURL, redirectToMainPage} from "./lib/oauthHelper";
import createSite from "./createSite";
import {hideSpinner, showSpinner} from "./helper";
import updatePostType from "./updatePostType";

console.info('window.PCCAdmin.credentials', window.PCCAdmin.credentials);


if (document.getElementById('pcc-app-authenticate') != undefined) {
	document.getElementById('pcc-app-authenticate').addEventListener('click', function () {
		login([]);
	});
}

if (document.getElementById('pcc-create-site') != undefined) {
	document.getElementById('pcc-create-site').addEventListener('click', async function () {
		try {
			showSpinner();
			await createSite();
		} catch (error) {
			console.error('Error while creating site:', error);
		} finally {
			hideSpinner();
			redirectToMainPage();
		}
	});
}

if (document.getElementById('pcc-update-collection') != undefined) {
	document.getElementById('pcc-update-collection').addEventListener('click', async function () {
		try {
			await updatePostType();
		} catch (error) {
			console.error('Error while creating site:', error);
		} finally {
			redirectToMainPage();
		}
	});
}

if (document.getElementById('pcc-disconnect') != undefined) {
	document.getElementById('pcc-disconnect').addEventListener('click', async function () {
		try {
			await deleteConfigDetails();
			redirectToMainPage();
		} catch (e) {
			console.log('Error while disconnecting', e);
		}
	});
}

// fetch token and save credentials if code is provided in URL
let code = getCodeFromURL();
if (code) {
	let saved = await fetchTokenAndSaveCredentials(code)
	if (saved) {
		console.log('Credentials saved successfully');
		redirectToMainPage();
	} else {
		console.log('Error while saving credentials');
	}
}
