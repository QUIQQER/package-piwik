/**
 * @module package/quiqqer/piwik/bin/Panel
 */
define('package/quiqqer/piwik/bin/Panel', [

    'qui/QUI',
    'qui/controls/desktop/Panel',
    'qui/controls/buttons/Select',
    'Locale',
    'Ajax',
    'Users',

    'css!package/quiqqer/piwik/bin/Panel.css'

], function (QUI, QUIPanel, QUISelect, QUILocale, QUIAjax, Users) {
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

            var User = Users.getUserBySession(),
                Prom = Promise.resolve();

            if (!User.isLoaded()) {
                Prom = User.load();
            }

            Prom.then(function () {
                QUIAjax.get([
                    'ajax_project_get_config',
                    'package_quiqqer_piwik_ajax_md5'
                ], function (config, pass) {

                    var url   = config['piwik.settings.url'],
                        id    = config['piwik.settings.id'],
                        token = config['piwik.settings.token'];

                    this.getContent()
                        .getElements('.quiqqer-piwik-panel-nosettings,iframe')
                        .destroy();

                    if (url === '' || id === '') {
                        new Element('div', {
                            'class': 'quiqqer-piwik-panel-nosettings',
                            html   : QUILocale.get(lg, 'panel.error.settings.missing')
                        }).inject(this.getContent());

                        this.Loader.hide();
                        return;
                    }

                    this.getContent().set('html', '');
                    this.getContent().setStyle('background', '#edecec');

                    var frameParams = {
                        module: 'CoreHome',
                        action: 'index',
                        idSite: id,
                        period: 'day',
                        date  : 'yesterday'
                    };

                    var now                = new Date().getTime(),
                        opened             = parseInt(QUI.Storage.remove('piwik-opened')),
                        usersPiwikLogin    = User.getAttribute('quiqqer.piwik.login'),
                        usersPiwikPassword = User.getAttribute('quiqqer.piwik.pass');

                    if (!opened) {
                        opened = 0;
                    }

                    if (!usersPiwikLogin && !usersPiwikPassword) {
                        QUI.getMessageHandler().then(function (MH) {
                            MH.addInformation(QUILocale.get(lg, 'panel.notice.userdata.missing'));
                        });
                    }

                    if (usersPiwikPassword && usersPiwikLogin && opened + 7200 < now) {
                        frameParams.module   = 'Login';
                        frameParams.action   = 'logme';
                        frameParams.login    = usersPiwikLogin;
                        frameParams.password = pass;
                    }

                    // session storage
                    QUI.Storage.set('piwik-opened', now);

                    url = url.replace('https://', '').replace('http://', '');

                    var src = '//' + url + '/index.php?' + Object.toQueryString(frameParams);

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
                    'package': 'quiqqer/piwik',
                    project  : this.getAttribute('project'),
                    str      : User.getAttribute('quiqqer.piwik.pass')
                });
            }.bind(this));
        }
    });
});
