<?php


class shopCategorystorefrontPluginSettingsAction extends waViewAction
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $vars = [
            'settings'    => $this->plugin()->getSettings(),
            'categories'  => $this->categories(),
            'storefronts' => $this->storefronts()
        ];

        $this->view()->assign($vars);
    }

    /**
     * Retrieve customers categories.
     *
     * @return array
     */
    protected function categories()
    {
        return shopCustomer::getAllCategories();
    }

    /**
     * Retrieve storefronts.
     *
     * @return array
     */
    protected function storefronts()
    {
        return shopHelper::getStorefronts();
    }

    /**
     * Gather view engine.
     *
     * @return waSmarty3View|waView
     */
    protected function view()
    {
        return $this->view;
    }

    /**
     * Get plugin instance.
     *
     * @return waPlugin
     * @throws waException
     */
    private function plugin()
    {
        return wa('shop')->getPlugin('categorystorefront');
    }
}
