<?php

namespace PCC;

class Parser
{
	/**
	 * Generates HTML from a JSON string and wraps it in a parent element if provided.
	 *
	 * @param string $json JSON string representing the HTML structure.
	 * @param string|null $parent_element Optional. The tag of the parent element to wrap around the generated HTML.
	 * @return string The generated HTML output.
	 */
	public function generateHtmlFromJson($json, $parent_element = null)
	{
		$json_array = json_decode($json, true);

		$unique_class = 'scoped-' . substr(md5(mt_rand()), 0, 9);

		$html_output = '';

		$this->processNode($json_array, $html_output, $unique_class);

		if ($parent_element) {
			$html_output = $this->createElement($parent_element, ['class' => $unique_class], [], $html_output);
		} else {
			$html_output = $this->createElement('div', ['class' => $unique_class], [], $html_output);
		}

		return $html_output;
	}

	/**
	 * Processes a JSON node and appends the corresponding HTML to the output.
	 *
	 * @param array $node The JSON node to process.
	 * @param string &$html_output The accumulated HTML output (passed by reference).
	 * @param string $unique_class A unique class name used for scoping styles.
	 * @return void
	 */
	private function processNode($node, &$html_output, $unique_class)
	{
		$tag = isset($node['tag']) ? $node['tag'] : 'div';
		$data = isset($node['data']) ? $node['data'] : '';
		$children = isset($node['children']) ? $node['children'] : [];
		$style = isset($node['style']) ? $node['style'] : [];
		$attrs = isset($node['attrs']) ? $node['attrs'] : [];

		if (empty($children) && empty($data) && empty($attrs)) {
			return;
		}

		if ($tag === 'style' && $data) {
			$scoped_data = ".$unique_class $data";
			$element = $this->createElement($tag, $attrs, $style, $scoped_data);
			$html_output .= $element;
			return;
		}

		$element = $this->createElement($tag, $attrs, $style, $data);

		if (!empty($children)) {
			$child_html = '';
			foreach ($children as $child) {
				$this->processNode($child, $child_html, $unique_class);
			}
			$element = str_replace('</' . $tag . '>', $child_html . '</' . $tag . '>', $element);
		}

		$html_output .= $element;
	}

	/**
	 * Creates an HTML element with the specified tag, attributes, styles, and content.
	 *
	 * @param string $tag The tag of the element to create.
	 * @param array $attrs Optional. An associative array of attributes for the element.
	 * @param array|string $styles Optional. An array or string of CSS styles to apply to the element.
	 * @param string|null $content Optional. The content to place inside the element.
	 * @return string The generated HTML element.
	 */
	private function createElement($tag, $attrs = [], $styles = [], $content = '')
	{
		if ($tag === null) {
			$tag = 'div';
		}

		$element = '<' . $tag;

		foreach ($attrs as $key => $value) {
			$element .= ' ' . $key . '="' . esc_attr($value) . '"';
		}

		$style_string = '';
		if (is_array($styles)) {
			foreach ($styles as $style) {
				list($key, $value) = array_map('trim', explode(':', $style));
				$style_string .= $key . ':' . $value . ';';
			}
		} elseif (is_array($styles)) {
			foreach ($styles as $key => $value) {
				$style_string .= $key . ':' . $value . ';';
			}
		}

		if ($style_string) {
			$element .= ' style="' . esc_attr($style_string) . '"';
		}

		$element .= '>';

		if ($content !== null) {
			$element .= $content;
		}

		$element .= '</' . $tag . '>';

		return $element;
	}
}
