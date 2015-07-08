<?php
	
	/*
	*
	* http://digitorum.ru
	*
	* Free for use.
	* thnx to http://www.zytrax.com/
	* more info http://digitorum.ru/blog/2012/12/01/User-Agent-Opredelyaem-mobilnoe-ustrojstvo.phtml
	*
	*/
	
	Class UserAgent_Mobile_Checker {
		
		/*
		* юзерагент для проверки
		*/
		private $userAgent = '';
		
		/*
		* Полученные заголовки
		*/ 
		private $httpAccept = '';
		
		/*
		* "Найденное" мобильное устройство
		*/
		private $mobileDetected = null;
		
		/*
		* Юзерагенты для известных платформ
		*/
		private $mobileUserAgentsPatterns = array();
		
		/*
		* конструктор, на вход принимаем юзерагент и заголовки ( $_SERVER['HTTP_ACCEPT'] )
		*/
		public function __construct($userAgent = '', $httpAccept = '') {
			$this->userAgent = $userAgent;
			$this->httpAccept = $httpAccept;
			
			/*
			* Заполняем паттерны
			*/
			$this->mobileUserAgentsPatterns = array(
				'Windows Phone 7' => '#(zunewp7|xblwp7|windows phone os 7)#i',
				'Windows Mobile' => '#(iris|3g_t|windows ce|iemobile|windows phone 6|windows mobile/6)#i',
				'Playstation' => '#(playstation|psp )#i',
				'Android' => '#android#i',
				'iPhone' => '#iPhone#i',
				'iPad' => '#ipad#i',
				'iPod' => '#ipod#i',
				'Kindle' => '#kindle#i',
				'Blackberry' => '#blackberry#i',
				'Palm' => '#(palm os|palmos|palm|hiptop|avantgo|plucker|xiino|blazer|elainepre/)#i',
				'Opera Mini/Opera Mobile' => '#opera mini|opera mobi#i',
				'Unknown Mobile' => '#(' . implode('|', 
										array(
											'sam\-',
											'samsung(\-|;)',
											'mobileexplorer',
											'nokia',
											'hp ipaq',
											'htc(_|\-)',
											'lg(\-|/)',
											'lge(\-| )',
											'mot(\-| )',
											'160x',
											'x160',
											'480x640',
											'240x400',
											'240x320',
											'600x800',
											'sonyericsson',
											'phone',
											'sanyo',
											'rim8',
											'mob-x',
											'bolt/',
											'docomo',
											'up\.browser',
											'up\.link',
											'vodafone',
											'j\-phone',
											'nook browser',
											'armv5tejl;',
											'armv6l;',
											'symbian',
											'smartphone;',
											'wap;',
											'wap browser',
											'opera mobi',
											'tablet pc',
											'tablet os',
											'wireless',
											'touchpad',
											'ddipocket;',
											'pdxgw/',
											'astel/',
											'dolphin',
											'minimo/',
											'plucker/',
											'pda; ',
											'netfront/',
											'wm5 pie',
											't\-mobile',
											'o2',
											'cricket',
											'ec-sgh'
										)
									) . ')#i'
			);
		}
		
		/*
		* Прасим юзерагент
		*/
		public function Check() {
			// проверяем юзерагент по паттернам
			if($this->userAgent) {
				foreach($this->mobileUserAgentsPatterns as $mobileKey => $pattern) {
					if(preg_match($pattern, $this->userAgent)) {
						$this->mobileDetected = $mobileKey;
						return;
					}
				}
			}
			
			// если дошли до сюда - проверяем заголовки (если они есть)
			if($this->httpAccept) {
				if(strpos($this->httpAccept, '/vnd.wap') !== false) {
					$this->mobileDetected = 'Unknown Mobile';
					return;
				}
			}
			
			// если до сюда дошли, проверяем массив _SERVER
			if(isset($_SERVER) && ( isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) ) ) {
				$this->mobileDetected = 'Unknown Mobile';
				return;
			}
			
			// если ничего найти не получилось - значит не мобильное устройство
			$this->mobileDetected = '';
			
		}
		
		/*
		* Магический метод __call (заворачиваем на проверку платформы)
		*/
		public function __call($name, $arguments) {
			if($this->mobileDetected === null) {
				$this->Check();
			}
			$name = strtolower($name);
			switch($name) {
				case 'mobileplatform' : 
					return $this->mobileDetected;
					break;
				case 'ismobile' :
					if($this->mobileDetected) {
						return true;
					}
					return false;
					break;
				case 'ispc' :
				case 'isdesctop' :
					if(!$this->mobileDetected) {
						return true;
					}
					return false;
					break;
				default : 
					if($this->mobileDetected) {
						$currentPlatformName = 'is' . preg_replace('/\s/', '', strtolower($this->mobileDetected));
						if($name == $currentPlatformName) {
							return true;
						}
					}
					return false;
					break;
			}
		}
		
	}
	
?>
