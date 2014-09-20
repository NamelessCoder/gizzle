<?php
namespace NamelessCoder\Gizzle;

/**
 * Interface PluginListInterface
 */
interface PluginListInterface {

	/**
	 * Get all class names of plugins delivered from implementer package.
	 *
	 * @return string[]
	 */
	public function getPluginClassNames();

}