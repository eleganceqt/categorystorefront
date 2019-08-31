(function ($, window, document) {

    $(function () {

        const shopCategorystorefrontPluginSettingsAction = {

            dom : {
                $wrapper : $('#categorystorefront-settings-wrapper')
            },

            initialize : function () {

                this.initializeSemanticUI();

                this.addEventListeners();
            },

            initializeSemanticUI : function () {
                this.initializeDropdownComponent();
            },

            initializeDropdownComponent : function () {
                this.initializeStorefrontDropdown();
            },

            initializeStorefrontDropdown : function () {
                this.dom.$wrapper.find('.storefront-dropdown').dropdown({ clearable : true });
            },

            addEventListeners : function () {
                this.dom.$wrapper.on('click', '.submit-bttn:not(.loading)', $.proxy(this.onSave, this));
            },

            onSave : function (event) {

                let that = this;

                let $button = $(event.target);

                $button.addClass('loading');

                let data = {
                    associations : that.serializeTable()
                };

                $.ajax({

                           url : '?plugin=categorystorefront&module=settings&action=save',
                           method : 'POST',
                           data : data,
                           async : true,
                           cache : false,
                           dataType : 'json',
                           error : function (jqXHR, textStatus, errorThrown) {
                               alert('500 - Internal Server Error');
                           },
                           success : function (response, textStatus, jqXHR) {

                               if (response.status === 'ok') {
                                   // ...
                               }

                               if (response.status === 'fail') {
                                   // ...
                               }

                           },
                           complete : function (jqXHR, textStatus) {
                               $button.removeClass('loading');
                           }
                       })
                ;

            },

            serializeTable : function () {

                let $table = this.dom.$wrapper.find('table');

                let params = {};

                $table.find('tbody > tr').each(function (_, element) {

                    let $tr = $(element);

                    let categoryId = $tr.data('category-id');

                    let storefront = $tr.find('.storefront-dropdown').dropdown('get value');

                    if (storefront !== '') {
                        params[categoryId] = storefront
                    }

                });

                return params;
            }
        };

        shopCategorystorefrontPluginSettingsAction.initialize();

    });

}(jQuery, window, document));
