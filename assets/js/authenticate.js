import {getAccessToken, showErrorMessage} from "./helper";
import axios from "axios";

export default function authenticate() {
  return new Promise(// eslint-disable-next-line no-async-promise-executor -- Handling promise rejection in the executor
    async (resolve, reject) => {
      try {
        const { rest_url, nonce } = window.PCCAdmin;
		  let accessToken = getAccessToken();
		  if (!accessToken) {
			  return reject(new Error('Access token cannot be empty'));
		  }

        await axios.post(`${rest_url}/oauth/access-token`, {
          access_token: getAccessToken(),
        }, {
          headers: { 'X-WP-Nonce': nonce }
        });
		resolve();
      } catch (e) {
        showErrorMessage(`Error: ${e.message}`)
        reject(e);
      }
    },);
}
