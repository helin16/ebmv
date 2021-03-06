<?php
/**
 * The FrontEnd Page Abstract
 * 
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
abstract class FrontEndPageAbstract extends TPage 
{
	/**
	 * @var TCallback
	 */
	protected $_getUserBtn = null;
	/**
	 * @var TCallback
	 */
	protected $_loginUserBtn = null;
	/**
	 * constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
		parent::onInit($param);
		
		$this->_getUserBtn = new TCallback();
		$this->_getUserBtn->ID = 'getUserBtn';
		$this->_getUserBtn->OnCallback = 'Page.getCurrentUser';
		$this->getControls()->add($this->_getUserBtn);
		
		$this->_loginUserBtn = new TCallback();
		$this->_loginUserBtn->ID = 'loginUserBtn';
		$this->_loginUserBtn->OnCallback = 'Page.login';
		$this->getControls()->add($this->_loginUserBtn);
		
		$this->getPage()->setTheme($this->_getThemeByName(Core::getLibrary()->getInfo('lib_theme')));
	}
	/**
	 * getting the theme by name
	 *
	 * @param string $themeName The name of the theme
	 *
	 * @throws Exception
	 * @return TTheme
	 */
	protected function _getThemeByName($themeName)
	{
		try
		{
			$currentTheme = $this->getPage()->getTheme();
			$currentThemeName = $currentTheme->getName();
			$currentThemePath = $currentTheme->getBasePath();
			$currentThemeUrl = $currentTheme->getBaseUrl();
				
			$newThemePath = str_replace($currentThemeName,$themeName,$currentTheme->getBasePath());
			$newThemeUrl = str_replace($currentThemeName,$themeName,$currentTheme->getBaseUrl());
			return new TTheme($newThemePath,$newThemeUrl);
		}
		catch(Exception $e)
		{
			throw new Exception("Can't create new theme '".$themeName."' : ".$e->getMessage());
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    if(!$this->IsPostBack || !$this->IsCallback)
	    {
	        $this->getClientScript()->registerEndScript('pageJs', $this->_getEndJs());
	    }
	}
	/**
	 * Getting The end javascript
	 * 
	 * @return string
	 */
	protected function _getEndJs() 
	{
	    return 'if(typeof(PageJs) !== "undefined"){var pageJs = new PageJs(); pageJs._currentLib = ' . trim(Core::getLibrary()->getId()) . ';pageJs.setCallbackId("getUser", "' . $this->_getUserBtn->getUniqueID() . '"); pageJs.setCallbackId("loginUser", "' . $this->_loginUserBtn->getUniqueID() . '"); }';
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onPreInit($param)
	{
	    parent::onPreInit($param);
	    $clientScript = $this->getPage()->getCLientScript();
	    
	    $clientScript->registerPradoScript('ajax');
	    $this->_loadPageJsClass();
        $cScripts = self::getLastestJS(get_class($this));
	    if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
	        $clientScript->registerScriptFile('pageJs', $this->publishAsset($lastestJs));
	    if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
	        $clientScript->registerStyleSheetFile('pageCss', $this->publishAsset($lastestCss));
	}
	/**
	 * loading the page js class files
	 */
	protected function _loadPageJsClass()
	{
		$cScripts = self::getLastestJS(__CLASS__);
		if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
			$this->getPage()->getClientScript()->registerScriptFile('frontEndPageJs', Prado::getApplication()->getAssetManager()->publishFilePath(dirname(__FILE__) . '/'  . $lastestJs, true));
	    return $this;
	}
	/**
	 * Getting the lastest version of Js and Css under the Class'file path
	 * 
	 * @param string $className The class name
	 * 
	 * @return multitype:string
	 */
	public static function getLastestJS($className)
	{
	    $array = array('js' => '', 'css' => '');
	    try
	    {
	        //loading controller.js
	        $class = new ReflectionClass($className);
	        $fileDir = dirname($class->getFileName()) . DIRECTORY_SEPARATOR;
	        if (is_dir($fileDir))
	        {
	            //loop through the directory to find the lastes js version or css version
	            $lastestJs = $lastestJsVersionNo = $lastestCss = $lastestCssVersionNo = '';
	            if ($handle = opendir($fileDir))
	            {
	                while (false !== ($fileName = readdir($handle)))
	                {
	                    preg_match("/^" . $className . "\.([0-9]+\.)?(js|css)$/i", $fileName, $versionNo);
	                    if (isset($versionNo[0]) && isset($versionNo[1]) && isset($versionNo[2]))
	                    {
	                        $type = trim(strtolower($versionNo[2]));
	                        $version = str_replace('.', '', trim($versionNo[1]));
	                        if ($type === 'js') //if loading a javascript
	                        {
	                            if ($lastestJs === '' || $version > $lastestJsVersionNo)
	                            $array['js'] = trim($versionNo[0]);
	                        }
	                        else if ($type === 'css')
	                        {
	                            if ($lastestCss === '' || $version > $lastestCssVersionNo)
	                            $array['css'] = trim($versionNo[0]);
	                        }
	                    }
	                }
	            }
	        }
	        unset($className, $class, $fileDir, $lastestJs, $lastestJsVersionNo, $lastestCss, $lastestCssVersionNo);
	    }
	    catch(Exception $e)
	    {
	        //we are not doing anything if we failed here!
	    }
	    return $array;
	}
	/**
	 * Trying to get the current user
	 * 
	 * @param TCallback           $sender
	 * @param TCallbackParameters $param
	 * 
	 * @throws Exception
	 */
	public function getCurrentUser($sender, $params)
	{
		$errors = $results = array();
		try
		{
			if(!Core::getUser() instanceof UserAccount)
				throw new Exception('Invalid user!');
			$results['user'] = array('id' => Core::getUser()->getId(), 'name' => trim(Core::getUser()->getPerson()));
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	/**
	 * Trying to get the current user
	 * 
	 * @param TCallback           $sender
	 * @param TCallbackParameters $param
	 * 
	 * @throws Exception
	 */
	public function login($sender, $params)
	{
		$errors = $results = array();
        try 
        {
            if(!isset($params->CallbackParameter->username) || ($username = trim($params->CallbackParameter->username)) === '')
                throw new Exception('username not provided!');
            if(!isset($params->CallbackParameter->password) || ($password = trim($params->CallbackParameter->password)) === '')
                throw new Exception('password not provided!');
            
            $authManager=$this->getApplication()->getModule('auth');
            if(!$authManager->login($username, $password))
            	throw new Exception('Invalid user!');
            $results['user'] = array('id' => Core::getUser()->getId(), 'name' => trim(Core::getUser()->getPerson()));
        }
        catch(Exception $ex)
        {
        	$errors[] = $ex->getMessage();
        }
        $params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	/**
	 * Getting the 404 page
	 * 
	 * @param string $title   The title of the page
	 * @param string $content The html code content
	 * 
	 * @return string The html code of the page
	 */
	public static function show404Page($title, $content)
	{
		header("HTTP/1.0 404 Not Found");
		$html = "<h1>$title</h1>";
		$html .= $content;
		return $html;
	}
	/**
	 * Getting system setting passing to the .page file
	 * 
	 * @param string $type The system setting's type code
	 * 
	 * @return Ambigous <string, multitype:>
	 */
	public function getSystemSettings($type)
	{
		return SystemSettings::getSettings($type);
	}
}
?>