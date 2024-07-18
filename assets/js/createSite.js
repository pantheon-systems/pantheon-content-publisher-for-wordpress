import {getSelectedPostType, updateSpinnerText} from "./helper";
import axios from "axios";

export default function createSite() {
	return new Promise(
		async (resolve, reject) => {
			try {
				const siteUrl = window.PCCAdmin.site_url;
				const selectedPostType = getSelectedPostType();
				if (!selectedPostType) {
					return reject(new Error('Post type not selected'));
				}

				updateSpinnerText('Creating your site...');
				let siteId = (await createSiteId(siteUrl)).data;
				updateSpinnerText('Register webhook...');
				await registerWebhook(siteUrl);
				updateSpinnerText('Creating your collection...');
				await createCollection(siteId, selectedPostType);
				resolve();
			} catch (error) {
				updateSpinnerText('Error while creating your collection. Please try again.');
				reject(error);
			}
		},);
}

/**
 * Save created site id & post type in database
 *
 * @param siteId
 * @param postType
 * @returns {Promise<axios.AxiosResponse<any>>}
 */
async function createCollection(siteId, postType) {
	const { rest_url, nonce } = window.PCCAdmin;
	return await axios.post(`${rest_url}/collection`, {
		site_id: siteId,
		post_type: postType,
	}, {
		headers: { 'X-WP-Nonce': nonce }
	});
}

async function createSiteId(url) {
	const { rest_url, nonce } = window.PCCAdmin;
	return await axios.post(`${rest_url}/site`, {
		url: url,
	}, {
		headers: { 'X-WP-Nonce': nonce }
	});
}

async function registerWebhook() {
	const { rest_url, nonce } = window.PCCAdmin;
	return await axios.put(`${rest_url}/webhook`, {}, {
		headers: { 'X-WP-Nonce': nonce }
	});
}
