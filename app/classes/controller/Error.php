<?php
class Controller_Error extends Controller
	{
		public function notFound() {
			return $this->render('error/index');
		}
	}
