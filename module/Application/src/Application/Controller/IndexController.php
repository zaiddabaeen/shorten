<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class IndexController extends AbstractActionController {

	public $action, $action2, $short;
	protected $shortenTable;

	public function shortenAction() {
		$this->short = $this->getEvent()->getRouteMatch()->getParam('short');

		if ($this->short == "") {
			return $this->redirect()->toRoute('index');
		}

		$shorten = $this->getShortenTable()->findByAttr(['short' => $this->short, 'active' => true]);

		if ($shorten->count() > 0) {
			$this->getShortenTable()->increment($this->short);
			$link = $shorten->current()->link;
			$this->redirect()->toUrl($link);
		} else {
			$view = new ViewModel();
			$this->layout('layout/404.phtml');
			$view->setTemplate('error/404.phtml');
			return $view;
		}
	}

	public function createAction() {

		$this->getPublicThings();

		$request = $this->getRequest();

		if ($request->isPost()) {

			$short = $request->getPost('short');
			$link = $request->getPost('link');
			$description = $request->getPost('description');

			if ($short == 'admin' || $short == '') {
				message('0', 'Prohibited keyword ' . $short);
			}
			
			if(strlen($link) == 0){
				message('0', 'Please provide a link');
			}

			if (strpos($link, "http") === false) {
				$link = "http://" . $link;
			}

			$data['short'] = $short;
			$data['link'] = $link;
			$data['description'] = $description;
			$data['created_at'] = Date('Y-m-d h:i:s');
			$this->getShortenTable()->save($data);

			message('1', 'Successfully created');
		} else {
			return new ViewModel();
		}
	}

	public function deleteAction() {

		$this->getPublicThings();

		$request = $this->getRequest();

		if ($request->isPost()) {

			$id = $request->getPost('id');
			$this->getShortenTable()->del($id);

			message('1', 'Successfully deleted');
		} else {
			message('0', 'Error');
		}
	}

	public function manageAction() {

		$this->getPublicThings();

		$request = $this->getRequest();

		if ($request->isPost()) {

			$id = $request->getPost('id');
			$short = $request->getPost('short');
			$link = $request->getPost('link');
			$active = $request->getPost('active');
			$description = $request->getPost('description');

			if ($link == 'admin') {
				message('0', 'Prohibited keyword admin');
			}

			if (strpos($link, "http") === false) {
				$link = "http://" . $link;
			}

			$data['id'] = $id;
			$data['short'] = $short;
			$data['link'] = $link;
			$data['active'] = $active;
			$data['description'] = $description;
			$this->getShortenTable()->save($data);

			message('1', 'Successfully saved');
		} else {

			$search = $request->getQuery()->search;

			if (isset($search)) {

				$view = new ViewModel([
					'entries' => $this->getShortenTable()->search($search),
					'search' => $search,
					'host' => 'http://' . $_SERVER['SERVER_NAME']
				]);
			} else {

				$view = new ViewModel([
					'entries' => $this->getShortenTable()->fetchAll(),
					'host' => 'http://' . $_SERVER['SERVER_NAME']
				]);
			}

			$view->setTemplate('application/index/index.phtml');
			return $view;
		}
	}
	
	public function getPublicThings() {
		$this->action = $this->getEvent()->getRouteMatch()->getParam('action');
		$this->action2 = $this->getEvent()->getRouteMatch()->getParam('action2');
		$this->isLoggedIn();
	}

	public function loginAction() {

		$this->layout()->action = $this->getEvent()->getRouteMatch()->getParam('action');
		$request = $this->getRequest();

		if ($request->isPost()) {

			$username = $request->getPost('username');
			$password = $request->getPost('password');
			$remember_me = $request->getPost('rememberme');

			if ($username == "root" && $password == "root") {

				$_SESSION['admin'] = true;

				if ($remember_me == 'on') {
					$expire = time() + (60 * 60 * 24 * 30);
					$path = '/';
					$domain = ($_SERVER['HTTP_HOST'] !== 'localhost') ? $_SERVER['HTTP_HOST'] : null;
					$secure = false;
					$httponly = true;

					setcookie('admin', 'true', $expire);
				}

				message('1', 'Get In!');
			} else {

				$_SESSION['admin'] = false;

				message('0', 'Wrong credentials');
			}
		} else {
			return new ViewModel();
		}
	}

	function logoutAction() {
		$this->layout()->loggedIn = false;
		unset($_SESSION['admin']);
		unset($_COOKIE['admin']);
		setcookie('admin', null, -1);
		return $this->redirect()->toRoute('index', ['action' => 'login']);
	}

	function isLoggedIn() {
		if (!isset($_COOKIE['admin']) && !isset($_SESSION['admin'])) {
			return $this->redirect()->toRoute('index', ['action' => 'login']);
			$this->layout()->loggedIn = false;
		} else {
			$this->layout()->loggedIn = true;
		}
	}

	public function getShortenTable() {
		if (!$this->shortenTable) {
			$sm = $this->getServiceLocator();
			$this->shortenTable = $sm->get('ShortenTable');
		}
		return $this->shortenTable;
	}

}
