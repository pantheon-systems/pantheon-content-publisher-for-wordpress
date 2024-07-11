import {ARTICLE_UPDATE_SUBSCRIPTION, PantheonClient, PublishingLevel} from "@pantheon-systems/pcc-sdk-core";

const url = new URL(window.location.href);
const params = new URLSearchParams(url.search);
const documentId = params.get('document_id');

const pantheonClient = new PantheonClient({
	siteId: window.PCCFront.site_id,
	token: window.PCCFront.token
});

const observable = pantheonClient.apolloClient.subscribe({
	query: ARTICLE_UPDATE_SUBSCRIPTION,
	variables: {
		id: window.PCCFront.preview_document_id, //replace with dynamic article ID
		contentType: "TREE_PANTHEON_V2",
		publishingLevel: PublishingLevel.REALTIME,
	},
});

observable.subscribe({
	next: (update) => {
		if (!update.data) return;
		const article = update.data.article;
		// Bail if current article is not equal to one in session
		// @TODO it's already checked and register above and needs to be revisited again before removing the following code
		if (documentId !== article.id) {
			return;
		}

		var entryTitle = document.getElementsByClassName('wp-block-post-title');
		var entryContents = document.getElementsByClassName('entry-content');
		entryTitle[0].innerHTML = article.title

		if (entryContents.length > 0) {
			entryContents[0].innerHTML = ''; // Clear existing content
			entryContents[0].appendChild(generateHTMLFromJSON(JSON.parse(update.data.article.content)));
		}
	},
});

function generateHTMLFromJSON(json, parentElement = null) {
	const createElement = (tag, attrs = {}, styles = {}, content = '') => {
		if (undefined === tag) {
			tag = 'div';
		}
		const element = document.createElement(tag);

		// Set attributes
		for (const [key, value] of Object.entries(attrs)) {
			element.setAttribute(key, value);
		}

		// Set styles
		if (Array.isArray(styles)) {
			styles.forEach(style => {
				const [key, value] = style.split(':').map(s => s.trim());
				element.style[key] = value;
			});
		} else if (typeof styles === 'object') {
			for (const [key, value] of Object.entries(styles)) {
				element.style[key] = value;
			}
		}

		// Set content
		if (content !== null) {
			element.innerHTML = content;
		}

		return element;
	};

	const processNode = (node, parent) => {
		const {tag, data, children, style, attrs} = node;

		const element = createElement(tag, attrs, style || [], data !== null ? data : '');

		if (children && children.length) {
			children.forEach(child => processNode(child, element));
		}

		parent.appendChild(element);
	};

	// Create a container if parentElement is not provided
	const container = parentElement || document.createElement('div');

	processNode(json, container);

	return container;
}

