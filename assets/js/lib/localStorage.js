import AddOnApiHelper from "./addonApiHelper";

//@todo: refactor auth and config data as DB values injected in the JS asset.
export const AUTH_FILE_PATH = "auth.json";
export const CONFIG_FILE_PATH = "config.json";

//@todo: refactor to use DB values
export const getLocalAuthDetails = async (requiredScopes,) => {
  let credentials;
  try {
    credentials = JSON.parse(AUTH_FILE_PATH);
  } catch (_err) {
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
    const newCred = await AddOnApiHelper.refreshToken(credentials.refresh_token,);
    await persistAuthDetails(newCred);
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

export const persistAuthDetails = async (payload,) => {
  await persistDetailsToDatabase(payload, AUTH_FILE_PATH);
};

export const persistConfigDetails = async (payload) => {
  await persistDetailsToDatabase(payload, CONFIG_FILE_PATH);
};

export const deleteConfigDetails = async () => console.log(CONFIG_FILE_PATH); //@todo: refactor to delete from DB

//@todo: refactor to persist in DB
const persistDetailsToDatabase = async (payload, filePath) => {
  //writeFileSync(filePath, JSON.stringify(payload, null, 2)); @todo: send payload to endpoint
};
