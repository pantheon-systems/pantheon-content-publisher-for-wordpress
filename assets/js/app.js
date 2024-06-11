import {deleteConfigDetails} from "./lib/localStorage";

require('./login');

import login from './login';
import {fetchTokenAndSaveCredentials, getCodeFromURL, redirectToMainPage} from "./lib/oauthHelper";

console.info('window.PCCAdmin.credentials', window.PCCAdmin.credentials);


if (document.getElementById('pcc-app-authenticate') != undefined) {
	document.getElementById('pcc-app-authenticate').addEventListener('click', function () {
		login([]);
	});
}
if (document.getElementById('pcc-disconnect') != undefined) {
	document.getElementById('pcc-disconnect').addEventListener('click', async function () {
		try {
			await deleteConfigDetails();
			window.location.href = window.PCCAdmin.plugin_main_page;
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
