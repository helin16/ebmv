<?php
class ProductListShowCase extends TTemplateControl
{
    private $title;
    private $limit;
    private $dataFunc;
    /**
     * (non-PHPdoc)
     * @see TPage::render()
     */
    public function onPreRender($param)
    {
        parent::onPreRender($param);
        $clientManger = $this->getPage()->getClientScript();
        $clientManger->registerPradoScript('ajax');
        $cScripts = FrontEndPageAbstract::getLastestJS(get_class($this));
        if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
            $clientManger->registerScriptFile('productListShowCaseJs', $this->publishAsset($lastestJs));
        if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
            $clientManger->registerStyleSheetFile('productListShowCaseCss', $this->publishAsset($lastestCss));
    }
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
    public function onLoad($param)
    {
        $page = $this->getPage();
        if(!$page->IsPostBack || !$page->IsCallback)
        {
            $page->getClientScript()->registerEndScript('productListShow_page_' . $this->getId(), $this->_getEndJs());
        }
    }
    /**
     * Getting The end javascript
     *
     * @return string
     */
    protected function _getEndJs()
    {
        $controlVarName = 'productListShowJs_' . $this->getId();
        $js = $controlVarName . ' = new ProductListShowCaseJs();';
        return $js;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    public function getDataFunc()
    {
        return $this->dataFunc;
    }
    public function setDataFunc($dataFunc)
    {
        $this->dataFunc = $dataFunc;
        return $this;
    }
    
}