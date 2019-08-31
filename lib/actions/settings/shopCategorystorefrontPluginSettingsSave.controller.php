<?php

class shopCategorystorefrontPluginSettingsSaveController extends waJsonController
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $inputs = $this->inputs();

        $this->plugin()->saveSettings($inputs);

        $this->cleanupCache();
    }

    /**
     * Return an array of request inputs we'll work with.
     *
     * @return array
     */
    protected function inputs()
    {
        return [
            'associations' => waRequest::post('associations', [], waRequest::TYPE_ARRAY)
        ];
    }

    /**
     * Clean plugin cache on settings save.
     *
     * @return void
     */
    protected function cleanupCache()
    {
        shopCategorystorefrontPluginCacher::make()->deleteAll();

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
