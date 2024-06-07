import {deleteConfigDetails} from "./lib/localStorage";

require('./login');

import login from './login';
import {fetchTokenAndSaveCredentials, getCodeFromURL, redirectToMainPage} from "./lib/oauthHelper";

if (document.getElementById('pcc-app-authenticate') != undefined) {
	document.getElementById('pcc-app-authenticate').addEventListener('click', function () {
		login([]);
	});
}
if (document.getElementById('pcc-app-disconnect') != undefined) {
	document.getElementById('pcc-app-disconnect').addEventListener('click', function () {
		deleteConfigDetails()
	});
}

// fetch token and save credentials if code is provided in URL
let code = getCodeFromURL();
if (code) {
	fetchTokenAndSaveCredentials()
	redirectToMainPage();
}
