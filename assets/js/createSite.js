import AddOnApiHelper from "./lib/addonApiHelper";
import {getSelectedPostType, updateSpinnerText} from "./helper";
import axios from "axios";

export default function createSite() {
	return new Promise(
		async (resolve, reject) => {
			try {
				const siteUrl = window.PCCAdmin.site_url;
				const selectedPostType = getSelectedPostType();
				if (!selectedPostType) {
					alert('Please select a post type');
					return reject(new Error('Post type not selected'));
				}

				updateSpinnerText('Creating your collection...');
				const siteId = await AddOnApiHelper.createSite(siteUrl);
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
