import AddOnApiHelper from "./addonApiHelper";
import axios from "axios";

//@todo: refactor auth and config data as DB values injected in the JS asset.
export const AUTH_FILE_PATH = "auth.json";
export const CONFIG_FILE_PATH = "config.json";

//@todo: refactor to use DB values
export const getLocalAuthDetails = async (requiredScopes,) => {
  let credentials = window.PCCAdmin.credentials;
	if (undefined === credentials || !credentials.access_token) {
		return null;
	}

  // Return null if required scope is not present
  const grantedScopes = new Set(credentials.scope?.split(" ") || []);
  if (requiredScopes && requiredScopes.length > 0 && !requiredScopes.every((i) => grantedScopes.has(i))) {
    return null;
  }

  // Check if token is expired
  if (credentials.expiry_date) {
    const currentTime = await AddOnApiHelper.getCurrentTime();

    if (currentTime < credentials.expiry_date) {
      return credentials;
    }
  }

  try {
    const newCred = await AddOnApiHelper.refreshToken(credentials.access_token,);
    await persistDetailsToDatabase(newCred);
    return newCred;
  } catch (_err) {
    return null;
  }
};

export const getLocalConfigDetails = async () => {
  try {
    return JSON.parse(CONFIG_FILE_PATH);
  } catch (_err) {
    return null;
  }
};

/**
 * Delete the API configuration details
 *
 * @param payload
 * @returns {Promise<*>}
 */
export const deleteConfigDetails = async (payload) => {
	const resp = await axios.delete(`${window.PCCAdmin.rest_url}/disconnect`,
        {headers: {'X-WP-Nonce': window.PCCAdmin.nonce}}
	);

	return resp
};


/**
 * Persist details to the database
 *
 * @param payload
 * @returns {Promise<any>}
 */
export const persistDetailsToDatabase = async (payload) => {
	const resp = await axios.post(`${window.PCCAdmin.rest_url}/oauth/credentials`,
		payload,
		{
			headers: {
				'X-WP-Nonce': window.PCCAdmin.nonce
			}
		}
	);

	return resp;
};
