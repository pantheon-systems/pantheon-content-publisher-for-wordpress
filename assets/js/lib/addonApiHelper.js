import axios, {AxiosError, HttpStatusCode} from "axios";
import login from "../login"; //@todo: refactor out these commands
import {getApiConfig} from "./apiConfig";
import {getLocalAuthDetails} from "./localStorage";
import {toKebabCase} from "./utils";

class AddOnApiHelper {
  static async getToken(code) {
    const resp = await axios.post(`${(await getApiConfig()).OAUTH_ENDPOINT}/token`, {
      code: code,
    },);
    return resp.data;
  }

  static async refreshToken(refreshToken) {
    const resp = await axios.post(`${(await getApiConfig()).OAUTH_ENDPOINT}/refresh`, {
      refreshToken,
    },);
    return resp.data;
  }

  static async getCurrentTime() {
    try {
      const resp = await axios.get(`${(await getApiConfig()).addOnApiEndpoint}/ping`,);
      return Number(resp.data.timestamp);
    } catch {
      // If ping fails, return current time
      return Date.now();
    }
  }

  static async getIdToken(requiredScopes = null, withAuthToken = false) {
    let authDetails = await getLocalAuthDetails(requiredScopes);

    // If auth details not found, try user logging in
    if (!authDetails) {
      await login(requiredScopes || []);
      authDetails = await getLocalAuthDetails(requiredScopes);
      if (!authDetails) throw new Error("Failed to get auth details");
    }

    return withAuthToken ? {
      idToken: authDetails.id_token,
      oauthToken: authDetails.access_token
    } : authDetails.id_token;
  }

  static async getDocument(documentId, insertIfMissing = false, title,) {
    const idToken = await this.getIdToken();

    const resp = await axios.get(`${(await getApiConfig()).DOCUMENT_ENDPOINT}/${documentId}`, {
      params: {
        insertIfMissing, ...(title && {
          withMetadata: {
            title,
            slug: toKebabCase(title)
          },
        }),
      },
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
    return resp.data;
  }

  static async addSiteMetadataField(siteId, contentType, fieldTitle, fieldType,) {
    const idToken = await this.getIdToken();

    await axios.post(`${(await getApiConfig()).SITE_ENDPOINT}/${siteId}/metadata`, {
      contentType,
      field: {
        title: fieldTitle,
        type: fieldType,
      },
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
        "Content-Type": "application/json",
      },
    },);
  }

  static async updateDocument(documentId, siteId, title, tags, metadataFields, verbose = false,) {
    const idToken = await this.getIdToken();

    if (verbose) {
      console.log("update document", {
        documentId,
        siteId,
        title,
        tags,
        metadataFields,
      });
    }

    const resp = await axios.patch(`${(await getApiConfig()).DOCUMENT_ENDPOINT}/${documentId}`, {
      siteId,
      tags,
      title, ...(metadataFields && {
        metadataFields,
      }),
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
        "Content-Type": "application/json",
      },
    },);

    return resp.data;
  }

  static async publishDocument(documentId) {
    const {
      idToken,
      oauthToken
    } = await this.getIdToken(["https://www.googleapis.com/auth/drive"], true,);

    if (!idToken || !oauthToken) {
      throw new Error('Failed to get idToken or oauthToken');
    }

    const resp = await axios.post(`${(await getApiConfig()).DOCUMENT_ENDPOINT}/${documentId}/publish`, null, {
      headers: {
        Authorization: `Bearer ${idToken}`,
        "Content-Type": "application/json",
        "oauth-token": oauthToken,
      },
    },);

    const publishUrl = resp.data.url;

    try {
      const resp = await axios.get(publishUrl);

      // Get the published URL
      console.log("Published to ", resp.request.res.responseUrl);
    } catch (e) {
      if (e instanceof AxiosError) console.error(e, e.code, e.message);
      throw e;
    }
  }

  static async previewFile(docId, {
    baseUrl,
  },) {
    const {
      idToken,
      oauthToken
    } = await this.getIdToken(["https://www.googleapis.com/auth/drive"], true,);

    if (!idToken || !oauthToken) {
      throw new Error('Failed to get idToken or oauthToken');
    }

    const resp = await axios.post(`${(await getApiConfig()).DOCUMENT_ENDPOINT}/${docId}/preview`, {
      baseUrl,
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
        "Content-Type": "application/json",
        "oauth-token": oauthToken,
      },
    },);

    return resp.data.url;
  }

  static async createApiKey({siteId} = {}) {
    const idToken = await this.getIdToken();

    const resp = await axios.post((await getApiConfig()).API_KEY_ENDPOINT, {
      siteId,
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
    return resp.data.apiKey;
  }

  static async listApiKeys() {
    const idToken = await this.getIdToken();

    const resp = await axios.get((await getApiConfig()).API_KEY_ENDPOINT, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    });

    return resp.data;
  }

  static async revokeApiKey(id) {
    const idToken = await this.getIdToken();

    try {
      await axios.delete(`${(await getApiConfig()).API_KEY_ENDPOINT}/${id}`, {
        headers: {
          Authorization: `Bearer ${idToken}`,
        },
      });
    } catch (err) {
      if ((err).response.status === HttpStatusCode.NotFound) throw new Error("API key not found");
    }
  }

  static async createSite(url) {
    const idToken = await this.getIdToken();

    const resp = await axios.post((await getApiConfig()).SITE_ENDPOINT, {
      name: "",
      url,
      emailList: ""
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
    return resp.data.id;
  }

  static async listSites({withConnectionStatus}) {
    const idToken = await this.getIdToken();

    const resp = await axios.get((await getApiConfig()).SITE_ENDPOINT, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
      params: {
        withConnectionStatus,
      },
    });

    return resp.data;
  }

  static async getSite(siteId) {
    const idToken = await this.getIdToken();

    const resp = await axios.get(`${(await getApiConfig()).SITE_ENDPOINT}/${siteId}`, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);

    return resp.data;
  }

  static async updateSite(id, url) {
    const idToken = await this.getIdToken();

    await axios.patch(`${(await getApiConfig()).SITE_ENDPOINT}/${id}`, {url}, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
  }

  static async getServersideComponentSchema(id) {
    const idToken = await this.getIdToken();

    await axios.get(`${(await getApiConfig()).SITE_ENDPOINT}/${id}/components`, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
  }

  static async pushComponentSchema(id, componentSchema,) {
    const idToken = await this.getIdToken();

    await axios.post(`${(await getApiConfig()).SITE_ENDPOINT}/${id}/components`, {
      componentSchema,
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
  }

  static async removeComponentSchema(id) {
    const idToken = await this.getIdToken();

    await axios.delete(`${(await getApiConfig()).SITE_ENDPOINT}/${id}/components`, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
  }

  static async listAdmins(id) {
    const idToken = await this.getIdToken();

    return (await axios.get(`${(await getApiConfig()).SITE_ENDPOINT}/${id}/admins`, {
        headers: {
          Authorization: `Bearer ${idToken}`,
        },
      })).data;
  }

  static async addAdmin(id, email) {
    const idToken = await this.getIdToken();

    await axios.patch(`${(await getApiConfig()).SITE_ENDPOINT}/${id}/admins`, {
      email,
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
  }

  static async removeAdmin(id, email) {
    const idToken = await this.getIdToken();

    await axios.delete(`${(await getApiConfig()).SITE_ENDPOINT}/${id}/admins`, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
      data: {
        email,
      },
    });
  }

  static async updateSiteConfig(id, {
    url,
    webhookUrl,
    webhookSecret,
  }) {
    const idToken = await this.getIdToken();

    const configuredWebhook = webhookUrl || webhookSecret;

    await axios.patch(`${(await getApiConfig()).SITE_ENDPOINT}/${id}`, {
      ...(url && {url: url}), ...(configuredWebhook && {
        webhookConfig: {
          ...(webhookUrl && {webhookUrl: webhookUrl}), ...(webhookSecret && {webhookSecret: webhookSecret}),
        },
      }),
    }, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
    },);
  }

  static async fetchWebhookLogs(siteId, {
    limit,
    offset,
  }) {
    const idToken = await this.getIdToken();

    const resp = await axios.get(`${(await getApiConfig()).SITE_ENDPOINT}/${siteId}/webhookLogs`, {
      headers: {
        Authorization: `Bearer ${idToken}`,
      },
      params: {
        limit,
        offset,
      },
    },);

    return resp.data;
  }
}

export default AddOnApiHelper;
