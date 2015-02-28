<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle;

/**
 * Class Message
 */
class Message {

	/**
	 * Mandatory message body.
	 *
	 * @var string
	 */
	protected $body = NULL;

	/**
	 * Path to file, if Message is for a specific line in
	 * a file in Commit or PullRequest
	 *
	 * @var string
	 */
	protected $path = NULL;

	/**
	 * Line number, if Message is for a specific file (e.g.
	 * if path is set)
	 *
	 * @var integer
	 */
	protected $position = 0;

	/**
	 * Optional: if Message is for a specific commit.
	 *
	 * @var Commit
	 */
	protected $commit = NULL;

	/**
	 * Optional: if Message is for a specific PullRequest
	 *
	 * @var PullRequest
	 */
	protected $pullRequest = NULL;

	/**
	 * @param string $body
	 * @param string $path
	 * @param integer $position
	 */
	public function __construct($body = NULL, $path = NULL, $position = 0) {
		$this->body = $body;
		$this->path = $path;
		$this->position = $position;
	}

	/**
	 * @return PullRequest
	 */
	public function getPullRequest() {
		return $this->pullRequest;
	}

	/**
	 * @param PullRequest $pullRequest
	 * @return void
	 */
	public function setPullRequest(PullRequest $pullRequest) {
		$this->pullRequest = $pullRequest;
	}

	/**
	 * @return integer
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * @param integer $position
	 * @return void
	 */
	public function setPosition($position) {
		$this->position = $position;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return Commit
	 */
	public function getCommit() {
		return $this->commit;
	}

	/**
	 * @param Commit $commit
	 * @return void
	 */
	public function setCommit(Commit $commit) {
		$this->commit = $commit;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param string $body
	 * @return void
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @return array
	 */
	public function toGitHubApiDataArray() {
		$data = array(
			'body' => $this->body
		);
		if (TRUE === $this->commit instanceof Commit) {
			if (NULL === $this->path) {
				$data['sha1'] = $this->commit->getId();
			} else {
				$data['commit_id'] = $this->commit->getId();
				$data['path'] = $this->path;
				$data['position'] = $this->position;
			}
		} elseif (TRUE === $this->pullRequest instanceof PullRequest) {
			$data['sha1'] = $this->pullRequest->getId();
		}
		return $data;
	}

}
