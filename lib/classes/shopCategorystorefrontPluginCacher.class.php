<?php

class shopCategorystorefrontPluginCacher
{
    /**
     * Make cache driver instance.
     *
     * @return waCache
     */
    public static function make()
    {
        return new waCache(new waFileCacheAdapter([]), 'shop.categorystorefront');
    }
}
