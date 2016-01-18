;(function($, window, document, undefined) {
    "use strict";

    var pluginName = "dclOrgSelector",
        $div = $("<div/>"),
        defaults = {
            useEnvironment:     false,
            selectedOrgs:       [],
            orgNames:           [],
            orgList:            [],
            envOrgList:         [],
            orgEnvList:         [],
            orgDataUrl:         "main.php?menuAction=OrganizationService.GetData",
            environmentDataUrl: "main.php?menuAction=EnvironmentOrgService.GetData",
            eleOrgsLink:        "#orgsLink",
            eleGrid:            "#grid",
            eleDialog:          "#dialog",
            eleSaveOrgs:        "#saveOrganizations",
            eleOrgIds:          "#outage_orgs",
            eleOrgNames:        "#selected_orgs"
        };

    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function() {
            var instance = this;

            $(instance.settings.eleOrgsLink).on("click", function () {
                instance.updateSelectedOrgs();
                $(instance.settings.eleGrid).trigger("reloadGrid");
                $(instance.settings.eleDialog).modal();
            });

            $(instance.settings.eleSaveOrgs).on("click", function() { instance.saveGridSelection(); });

            this.initDialog();
        },
        loadGridSelection: function() {
            var $grid = $(this.settings.eleGrid);
            var gridIds = $grid.jqGrid("getDataIDs");
            var instance = this;

            $.each(gridIds, function (idx, value) {
                if (instance.settings.selectedOrgs[value]) {
                    $grid.jqGrid("setSelection", value);
                }
            });
        },
        saveGridSelection: function() {
            var selectedIds = [];
            $.each(this.settings.selectedOrgs, function (key, value) {
                if (value) {
                    selectedIds.push(key);
                }
            });

            $(this.settings.eleOrgIds).val(selectedIds.join(","));
            this.updateSelectedOrgNames();
        },
        updateSelectedOrgs: function() {
            var instance = this;
            this.settings.selectedOrgs = [];
            var formOrgs = String($(instance.settings.eleOrgIds).val()).split(",");
            $.each(formOrgs, function (idx, value) {
                instance.settings.selectedOrgs[value] = { name: "", selected: true };
            });
        },
        updateEnvOrgList: function(data) {
            var instance = this;
            this.settings.orgEnvList = [];
            this.settings.envOrgList = data;
            $.each(data, function(k, v) {
                $.each(v, function(k1, v1) {
                    instance.settings.orgEnvList[v1] = k;
                });
            });
        },
        htmlEncode: function(val) {
            if (val === undefined)
                return "";

            return $div.text(val).html();
        },
        getOrgData: function() {
            return $.getJSON(this.settings.orgDataUrl, { rows: -1, page: 1, sidx: "name", sord: "asc" });
        },
        getEnvironmentOrgData: function() {
            return $.getJSON(this.settings.environmentDataUrl);
        },
        updateGridSelection: function(data) {
            var instance = this;
            this.settings.orgList = data.rows;
            $.each(data.rows, function (key, value) {
                instance.settings.orgNames[value.id] = value.name;
            });

            this.updateSelectedOrgNames();
        },
        updateSelectedOrgNames: function() {
            var instance = this;
            var formOrgs = String($(instance.settings.eleOrgIds).val()).split(",");
            var selectedOrgNames = [];
            $.each(formOrgs, function (idx, value) {
                selectedOrgNames.push(instance.settings.orgNames[value]);
            });

            selectedOrgNames.sort();

            var html = "";
            $.each(selectedOrgNames, function (key, value) {
                html += '<span class="badge alert-info">' + instance.htmlEncode(value) + "</span>";
            });

            $(instance.settings.eleOrgNames).html(html);
        },
        initDialog: function() {
            var instance = this;

            if (instance.settings.useEnvironment) {
                $.when(instance.getEnvironmentOrgData(), instance.getOrgData()).done(function(envOrg, org) {
                    instance.updateGridSelection(org[0]);
                    instance.initGrid();
                    instance.updateEnvOrgList(envOrg[0]);
                });
            } else {
                $.when(instance.getOrgData()).done(function(org) {
                    instance.updateGridSelection(org[0]);
                    instance.initGrid();
                });
            }
        },
        initGrid: function() {
            var instance = this;
            $(instance.settings.eleGrid).jqGrid({
                data: instance.settings.orgList,
                datatype: "local",
                colNames: [
                    'ID',
                    'Name',
                    'Phone',
                    'Email',
                    'URL'
                ],
                cmTemplate: { title: false },
                colModel: [
                    { name: 'id', index: 'id', width: 35, align: "right" },
                    { name: 'name', index: 'name', width: 100, searchoptions: { sopt: ['cn'] } },
                    { name: 'phone', index: 'phone', width: 55, searchoptions: { sopt: ['cn'] } },
                    { name: 'email', index: 'email', width: 55, searchoptions: { sopt: ['cn'] } },
                    { name: 'url', index: 'url', width: 55, searchoptions: { sopt: ['cn'] } }
                ],
                width: 850,
                height: 480,
                rowNum: 25,
                rowList: [25, 50, 100],
                pager: '#pager',
                sortname: 'name',
                viewrecords: true,
                hidegrid: false,
                multiselect: true,
                ignoreCase: true,
                caption: "Select Organizations",
                gridComplete: function() { instance.loadGridSelection(); },
                onSelectRow: function (rowid, status, e) {
                    instance.settings.selectedOrgs[rowid] = status;
                },
                onSelectAll: function (aRowids, status) {
                    for (var i = 0; i < aRowids.length; i++)
                        instance.settings.selectedOrgs[aRowids[i]] = status;
                }
            })
            .jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false })
            .jqGrid('filterToolbar');
        }
    });

    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);
