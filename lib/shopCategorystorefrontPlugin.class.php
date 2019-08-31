<?php

class shopCategorystorefrontPlugin extends shopPlugin
{
    /**
     * @var string
     */
    const OTHERS_CATEGORY_ID = 'others';

    /**
     * Method for app hook.
     *
     * @event frontend_head
     *
     * @return void
     */
    public function frontendHead()
    {
        if ($this->hasRequiredSettings()) {

            $this->isAuthenticatedUser()
                ? $this->dispatchAuthUser()
                : $this->dispatchOthers();
        }
    }

    /**
     * Check if plugin has required settings to work.
     *
     * @return bool
     */
    public function hasRequiredSettings()
    {
        $settings = $this->pluginSettings();

        return $this->notEmpty($settings['associations']);
    }

    /**
     * Retrieve plugin settings.
     *
     * @return array
     */
    public function pluginSettings()
    {
        $cacher = shopCategorystorefrontPluginCacher::make();

        $key = 'settings';

        if (($settings = $cacher->get($key)) !== null) {
            return $settings;
        }

        $settings = $this->getSettings();

        $cacher->set($key, $settings, 86400); // cache for 24 hours

        return $settings;
    }

    /**
     * Retrieve routes by shop application.
     *
     * @return array
     */
    public function shopRoutes()
    {
        $cacher = shopCategorystorefrontPluginCacher::make();

        $key = 'routes';

        if (($routes = $cacher->get($key)) !== null) {
            return $routes;
        }

        $routes = wa()->getRouting()->getByApp('shop');

        $cacher->set($key, $routes, 86400); // cache for 24 hours

        return $routes;
    }

    /**
     * Retrieve configured customers categories.
     *
     * @return array
     */
    protected function configuredCustomersCategories()
    {
        $cacher = shopCategorystorefrontPluginCacher::make();

        $key = 'categories';

        if (($categories = $cacher->get($key)) !== null) {
            return $categories;
        }

        $customersCategories  = array_keys(shopCustomer::getAllCategories());
        $configuredCategories = array_keys(ifempty(ref($this->pluginSettings()), 'associations', []));

        $categories = array_intersect($customersCategories, $configuredCategories);

        $cacher->set($key, $categories, 86400); // cache for 24 hours

        return $categories;
    }

    /**
     * //@param string $storefront
     *
     * @return string|null
     * @todo mb rename
     * Obtain storefront url.
     *
     */
    protected function obtainStorefrontUrl($storefront)
    {
        $storefront = rtrim($storefront, '/');

        foreach ($this->shopRoutes() as $domain => $routes) {

            foreach ($routes as $route) {

                if (! isset($route['url'])) {
                    continue;
                }

                $routeStorefront = rtrim(rtrim($domain, '/') . '/' . $route['url'], '/.*');

                if ($routeStorefront === $storefront) {
                    return wa()->getRouteUrl('shop/frontend', [], true, $domain, $route['url']);
                }
            }
        }

        return null;
    }

    /**
     * Check if user is authenticated.
     *
     * @return bool
     */
    protected function isAuthenticatedUser()
    {
        return wa()->getUser()->isAuth();
    }

    /**
     * Redirect authenticated user, based on categories he belongs to.
     *
     * For each customers categories, can be configured the only storefront he has access to. If
     * the user tries to access another storefront (he has no access), it will be redirected back.
     *
     * @return void
     */
    protected function dispatchAuthUser()
    {
        $intersect = array_intersect($this->configuredCustomersCategories(), wa()->getUser()->get('categories'));

        $categoryId = ($this->notEmpty($intersect)) ? reset($intersect) : self::OTHERS_CATEGORY_ID;

        $this->redirectByCategory($categoryId);
    }

    /**
     * Redirect 'others' users that has no fulfilled rules.
     *
     * @return void
     */
    protected function dispatchOthers()
    {
        $this->redirectByCategory(self::OTHERS_CATEGORY_ID);
    }

    /**
     * Redirect to configured storefront for given category.
     *
     * @param int|string $categoryId (Possible values: customer category id or keyword for others category (see self::OTHERS_CATEGORY_ID))
     *
     * @return void
     */
    protected function redirectByCategory($categoryId)
    {
        $settings = $this->pluginSettings();

        if ($this->notEmpty($settings['associations'][$categoryId])) {

            $currentStorefront  = $this->currentStorefront();
            $categoryStorefront = $settings['associations'][$categoryId];

//            wa_dumpc($currentStorefront, $categoryStorefront);

            if ($currentStorefront !== $categoryStorefront) {
                $this->redirectUnlessNull($this->obtainStorefrontUrl($categoryStorefront));
            }
        }
    }

    /**
     * Redirect to given url, unless it is null.
     *
     * @param string|null $redirectUrl
     *
     * @return void
     */
    protected function redirectUnlessNull($redirectUrl)
    {
        if ($redirectUrl !== null) {
            wa()->getResponse()->redirect($redirectUrl);
        }
    }

    /**
     * Retrieve current storefront.
     *
     * @return string
     */
    protected function currentStorefront()
    {
        $route  = wa()->getRouting()->getRoute();
        $domain = wa()->getRouting()->getDomain();

        $url = rtrim($domain . '/' . $route['url'], '/*');

        if (strpos($url, '/') !== false) {
            $url .= '/';
        }

        return $url;
    }

    /**
     * Check if variable is not empty.
     *
     * @param mixed $variable
     *
     * @return bool
     */
    private function notEmpty(&$variable)
    {
        return ! empty($variable);
    }
}
