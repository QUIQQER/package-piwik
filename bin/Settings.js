/**
 * piwik settings - project lang
 *
 * @module package/quiqqer/piwik/bin/Settings
 * @author www.pcsg.de (Henning Leutz)
 *
 * @require qui/QUI
 * @require qui/controls/Control
 * @require Locale
 * @require Mustache
 * @require text!package/quiqqer/piwik/bin/Setting.html
 * @require css!package/quiqqer/piwik/bin/Settings.css
 */
define('package/quiqqer/piwik/bin/Settings', [

    'qui/QUI',
    'qui/controls/Control',
    'Locale',
    'Mustache',
    'text!package/quiqqer/piwik/bin/Setting.html',
    'css!package/quiqqer/piwik/bin/Settings.css'

], function (QUI, QUIControl, QUILocale, Mustache, templateSetting) {
    "use strict";

    var lg = 'quiqqer/piwik';

    return new Class({
        Extends: QUIControl,
        Type   : 'package/quiqqer/piwik/bin/Settings',

        Binds: [
            '$onImport',
            '$change'
        ],

        options: {
            Project: false,
            value  : false
        },

        initialize: function (options) {
            this.parent(options);

            this.$Input     = null;
            this.$Container = null;
            this.$imported  = false;

            this.addEvents({
                onImport: this.$onImport
            });
        },

        /**
         * refresh
         *
         * @return {Promise}
         */
        refresh: function () {
            if (!this.getAttribute('Project')) {
                return Promise.resolve();
            }

            var Project = this.getAttribute('Project'),
                Elm     = this.getElm();

            if (typeOf(Project) !== 'classes/projects/Project') {
                return Promise.resolve();
            }

            this.$Container = new Element('div').inject(Elm);

            return Project.getConfig(false, 'langs').then(function (langs) {
                var tplData = [];

                langs.split(',').each(function (lang) {
                    tplData.push({
                        lang         : lang,
                        flag         : URL_BIN_DIR + '16x16/flags/' + lang + '.png',
                        piwikUrlTitle: QUILocale.get(lg, 'piwik.settings.url'),
                        piwikIdTitle : QUILocale.get(lg, 'piwik.settings.id')
                    });
                });

                this.$Container.set({
                    html: Mustache.render(templateSetting, {
                        langs: tplData
                    })
                });

                var list  = this.$Container.getElements('input'),
                    value = this.getAttribute('value');

                list.addEvents({
                    change: this.$change
                });

                if (!this.$imported) {

                    list.each(function (Node) {
                        var lang = Node.get('data-lang');

                        if (!value || !(lang in value)) {
                            return;
                        }

                        if (Node.get('name') === 'url') {
                            Node.value = value[lang].url;
                        }

                        if (Node.get('name') === 'id') {
                            Node.value = value[lang].id;
                        }
                    });

                    this.$imported = true;
                }

            }.bind(this));
        },

        /**
         * event: on import
         */
        $onImport: function () {
            var Elm  = this.getElm();
            Elm.type = 'hidden';

            this.$Input = Elm;

            this.$Elm = new Element('div', {
                styles: {
                    'float': 'left',
                    width  : '100%'
                }
            }).wraps(this.$Input);

            if (this.$Input.value !== '') {
                this.setAttribute('value', JSON.decode(this.$Input.value));
            }

            this.refresh();
        },

        /**
         * Set the project
         *
         * @param {Object} Project - classes/projects/Project
         */
        setProject: function (Project) {
            this.setAttribute('Project', Project);
            this.refresh();
        },

        /**
         * value change
         */
        $change: function () {
            var data = {};

            this.$Container.getElements('input').each(function (Node) {
                var lang = Node.get('data-lang');

                if (!data) {
                    return;
                }

                if (!(lang in data)) {
                    data[lang] = {};
                }

                if (Node.get('name') === 'url') {
                    data[lang].url = Node.value;
                }

                if (Node.get('name') === 'id') {
                    data[lang].id = Node.value;
                }
            });

            this.$Input.value = JSON.encode(data);
        }
    });
});
