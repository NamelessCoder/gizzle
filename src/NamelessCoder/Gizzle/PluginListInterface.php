<?php
namespace NamelessCoder\Gizzle;

/**
 * Interface PluginListInterface
 */
interface PluginListInterface {

	/**
	 * Initialize the plugin lister with an array of settings.
	 *
	 * @param array $settings
	 * @return void
	 */
	public function initialize(array $settings);

	/**
	 * Get all class names of plugins delivered from implementer package.
	 *
	 * @return string[]
	 */
	public function getPluginClassNames();

}