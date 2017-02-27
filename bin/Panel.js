/**
 * @module package/quiqqer/piwik/bin/Panel
 */
define('package/quiqqer/piwik/bin/Panel', [

    'qui/QUI',
    'qui/controls/desktop/Panel',
    'qui/controls/buttons/Select',
    'Locale',
    'Ajax',

    'css!package/quiqqer/piwik/bin/Panel.css'

], function (QUI, QUIPanel, QUISelect, QUILocale, QUIAjax) {
    "use strict";

    var lg = 'quiqqer/piwik';

    return new Class({

        Extends: QUIPanel,
        Type   : 'package/quiqqer/piwik/bin/Panel',

        Binds: [
            '$onInject'
        ],

        options: {
            project: false
        },

        initialize: function (options) {
            this.parent(options);

            // defaults
            this.setAttributes({
                title: QUILocale.get(lg, 'panel.piwik.title')
            });

            this.addEvents({
                onInject: this.$onInject
            });
        },

        /**
         * event: on create
         */
        $onInject: function () {
            var self = this;

            this.Loader.show();

            require(['Projects'], function (Projects) {
                Projects.getList().then(function (projects) {
                    var keys    = Object.keys(projects);
                    var project = keys[0];

                    self.setAttribute('project', project);

                    if (Object.getLength(projects) === 1) {
                        return self.$loadFrame();
                    }

                    var Select = new QUISelect({
                        name     : 'projectList',
                        showIcons: false,
                        events   : {
                            onChange: function (value) {
                                self.setAttribute('project', value);
                                self.$loadFrame();
                            }
                        }
                    });

                    for (var key in projects) {
                        if (!projects.hasOwnProperty(key)) {
                            continue;
                        }

                        Select.appendChild(
                            key,
                            key
                        );
                    }

                    self.addButton(Select);
                    Select.setValue(self.getAttribute('project'));

                }.bind(this));
            }.bind(this));
        },

        /**
         * load the iframe and the piwik data
         */
        $loadFrame: function () {
            this.Loader.show();

            QUIAjax.get('ajax_project_get_config', function (config) {
                var url   = config['piwik.settings.url'],
                    id    = config['piwik.settings.id'],
                    token = config['piwik.settings.token'];

                this.getContent()
                    .getElements('.quiqqer-piwik-panel-nosettings,iframe')
                    .destroy();

                if (url === '' || id === '') {
                    new Element('div', {
                        'class': 'quiqqer-piwik-panel-nosettings',
                        html   : 'Fehlende Piwik Settings'
                    }).inject(this.getContent());

                    this.Loader.hide();
                    return;
                }

                this.getContent().set('html', '');
                this.getContent().setStyle('background', '#edecec');

                url = url.replace('https://', '')
                    .replace('http://', '');

                var src = 'https://' + url + '/index.php?';

                src = src + Object.toQueryString({
                        module: 'CoreHome',
                        action: 'index',
                        idSite: id,
                        period: 'day',
                        date  : 'yesterday'
                    });

                new Element('iframe', {
                    src   : src,
                    styles: {
                        border: 'none',
                        height: 'calc(100% - 4px)',
                        width : '100%'
                    }
                }).inject(this.getContent());

                this.Loader.hide();
            }.bind(this), {
                project: this.getAttribute('project')
            });

            // piwik.settings.url=""
            // piwik.settings.id=""
            // piwik.settings.token=""
            // piwik.settings.langdata=""

        }
    });
});
