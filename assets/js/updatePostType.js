import {getSelectedPostType, updateSpinnerText} from "./helper";
import axios from "axios";

export default function updatePostType() {
	return new Promise(
		async (resolve, reject) => {
			try {
				const selectedPostType = getSelectedPostType();
				if (!selectedPostType) {
					return reject(new Error('Post type not selected'));
				}

				updateSpinnerText('Updating your collection...');
				await updateConfiguration(selectedPostType);
				resolve();
			} catch (error) {
				updateSpinnerText('Error while updating your collection. Please try again.');
				reject(error);
			}
		},);
}


/**
 * Update integration post type in database
 *
 * @param postType
 * @returns {Promise<axios.AxiosResponse<any>>}
 */
async function updateConfiguration(postType) {
	const { rest_url, nonce } = window.PCCAdmin;
	return await axios.put(`${rest_url}/collection`, {
		post_type: postType,
	}, {
		headers: { 'X-WP-Nonce': nonce }
	});
}
