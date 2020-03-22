<?php

namespace controller;

use \Config;
use \Database;
use \Helper;
use \Security;
use model\User;

class Signup extends AbstractController {

	// username, app name, activation link, author
	const MAIL_TEXT =
"dear %s\n
thank you for signing up for %s!\n
go to following link to activate your account: %s\n
sincerely %s";

	public function _get() {
		switch ($this->id) {
			case 'success':
				$this->success();
				break;
			case 'activate':
				$this->activate();
				break;
			case 'resend':
				$this->resend();
				break;
			default:
				// do nothing
				break;
		}
	}

	public function _post() {
		$user = null;
		if (!empty($this->data)) {
			$this->result = (new Users($this))->post($this->data);
			if (!empty($this->result)) {
				$user = new User($this->result);
				if ($this->sendActivationMail($user)) {
					// successfully signed up
					Helper::redirect('signup/success?token='.rawurlencode($user->getToken()));
				} else {
					// sending activation mail failed
					$this->setView(new \view\Failure(
						'activation mail could not be sent to <b>'.$user->getEmail().'</b>!',
						'check the email address and try to '.Helper::makeLink('signup').' again.'));
				}
			} else {
				// render view with user validation errors
			}
		}
		// set username and email to view
		unset($this->data['password'], $this->data['password-repeat']);
		$this->view->setData(array('formdata' => $this->data));
		return $user;
	}

	private function success() {
		if (!empty($this->data) && !empty($this->data['token'])) {
			if (empty($user = (new Users())->getBy('token', rawurldecode($this->data['token'])))) {
				// invalid token
				$this->setView(new \view\Failure(
					'account could not be found!',
					'please try to '.Helper::makeLink('signup').' again.'));
			} else {
				$user = current($user);
				// already activated
				if ($user->getActivated()) {
					Helper::redirect('login');
				} else {
					// successfully signed up
					$this->setView(new \view\Success(
						'you have successuflly signed up for '.Config::get('app.name').'!',
						'check your email inbox for the activation mail (also check the spam folder).'));
				}
			}
		} else {
			die('token was not found');
			Helper::redirect('signup');
		}
	}

	private function activate() {
		if (!empty($this->data) && !empty($this->data['series'])) {
			if (!empty($user = (new Users())->getBy('series', rawurldecode($this->data['series'])))) {
				$user = current($user);
				if ($user->getActivated()) {
					// account already activated
					$this->setView(new \view\Failure(
						'this account is already activated!',
						'signup '.Helper::makeLink('here', 'signup').' or go to the '.Helper::makeLink('login').' to enjoy the game.'));
				} else {
					if ($this->activateUser($user)) {
						// account successfully activated
						$this->setView(new \view\Success(
							'you have successuflly activated your account!',
							'go to the '.Helper::makeLink('login').' to enjoy the game.'));
					} else {
						// could not update db to activate user
						$this->setView(new \view\Failure(
							'activation failed!',
							'click '.Helper::makeLink('here', 'signup/resend?series='.rawurlencode($this->data['series'])).' to receive a new activation mail.'));
					}
				}
			} else {
				// invalid series
				$this->setView(new \view\Failure(
					'invalid activation token!',
					'click '.Helper::makeLink('here', 'signup').' to sign up for the game.'));
			}
		} else {
			Helper::redirect('signup');
		}
	}

	private function resend() {
		// check if user exists
		// TODO 2016-01-04 david: check functionality (fix views like in todo -> set messages);
		if (empty($user = current((new Users())->getBy('series', $this->data['series'])))) {
			$this->setView(new \view\Failure(
				'invalid token!',
				'click '.Helper::makeLink('here', 'signup').' to sign up for the game.'));
		} else {
			if ($this->sendActivationMail(Security::refreshUser($user, true))) {
				// successfully resent activation mail
				$this->setView(new \view\Success(
					'activation mail sent!',
					'check your email inbox for the activation mail (also check the spam folder).'));
			} else {
				// sending activation mail failed
				$this->setView(new \view\Failure(
					'activation mail could not be sent to <b>'.$user->getEmail().'</b>!',
					'check the email address and try to '.Helper::makeLink('signup').' again.'));
			}
		}
	}

	private function activateUser(\model\User $user) {
		return $this->getDb((new Users($this))->getTableName())->where('id', $user->getId())->update(array('activated' => true));
	}

	private function sendActivationMail(\model\User $user) {
		return Helper::sendPlainTextMail($user->getEmail(),
			'activation mail',
			sprintf(self::MAIL_TEXT,
				// username
				$user->getUsername()
				// app name
				, Config::get('app.name')
				// activation link
				, Helper::getProtocol()
					.Config::get('app.url.host')
					.Config::get('app.url.base')
					.'/signup/activate?series='
					.rawurlencode($user->getSeries())
				// author
				, Config::get('app.info.author')));
	}
}
