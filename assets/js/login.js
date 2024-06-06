import {parseJwt} from "@pantheon-systems/pcc-sdk-core";
import {OAuth2Client} from "google-auth-library";
import nunjucks from "nunjucks";
import {getApiConfig} from "./lib/apiConfig";
import {getLocalAuthDetails,} from "./lib/localStorage";

nunjucks.configure({autoescape: true});

const OAUTH_SCOPES = ["https://www.googleapis.com/auth/userinfo.email"];

export default function login(extraScopes) {
  return new Promise(// eslint-disable-next-line no-async-promise-executor -- Handling promise rejection in the executor
    async (resolve, reject) => {
      try {
        const authData = await getLocalAuthDetails(extraScopes);
        if (authData) {
          const scopes = authData.scope?.split(" ");

          if (!extraScopes?.length || extraScopes.find((x) => scopes?.includes(x))) {
            const jwtPayload = parseJwt(authData.id_token);
            return resolve();
          }
        }

        const apiConfig = await getApiConfig();
        const oAuth2Client = new OAuth2Client({
          clientId: apiConfig.googleClientId,
          redirectUri: apiConfig.googleRedirectUri,
        });

        // Generate the url that will be used for the consent dialog.
        const authorizeUrl = oAuth2Client.generateAuthUrl({
          access_type: "offline",
          scope: [
            ...OAUTH_SCOPES,
            ...extraScopes
          ],
        });

        //@todo: refactor to use a WP endpoint to handle the redirect
        //const server = http.createServer(async (req, res) => {
        //  try {
        //    if (!req.url) {
        //      throw new Error("No URL path provided");
        //    }
        //
        //    if (req.url.indexOf("/oauth-redirect") > -1) {
        //      const qs = new url.URL(req.url, "http://localhost:3030").searchParams;
        //      const code = qs.get("code");
        //      const currDir = dirname(fileURLToPath(import.meta.url));
        //      const content = readFileSync(join(currDir, "../templates/loginSuccess.html"),);
        //      const credentials = await AddOnApiHelper.getToken(code);
        //      const jwtPayload = parseJwt(credentials.id_token);
        //      await persistAuthDetails(credentials);
        //      res.end(nunjucks.renderString(content.toString(), {
        //        email: jwtPayload.email,
        //      }),);
        //      server.destroy();
        //      resolve();
        //    }
        //  } catch (e) {
        //    reject(e);
        //  }
        //});
        //
        //destroyer(server);
        //
        //server.listen(3030, () => {
        //  open(authorizeUrl, {wait: true}).then((cp) => cp.kill());
        //});
      } catch (e) {
        reject(e);
      }
    },);
}
