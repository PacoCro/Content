'use strict';

var nodeDataAttribute = '_gridstack_node';
var suspendAutoSave = false;
var loadedDynamicAssets = { css: [], js: [] };

/**
 * Loads a script file synchronously and caches it.
 */
jQuery.contentGetSyncCachedScript = function (url, options) {
    // Allow user to set any option except for the specified ones
    options = jQuery.extend(options || {}, {
        dataType: 'script',
        url: url,
        cache: true,
        async: false
    });
 
    return jQuery.ajax(options);
};

/**
 * Shows a notification message.
 */
function contentPageShowNotification(title, message, elemId, alertClass) {
    jQuery('#notificationContainer').first().addClass('active');
    zikulaContentSimpleAlert(jQuery('#notificationBox').first(), title, message, elemId, alertClass);
    window.setTimeout(function () {
        jQuery('#notificationContainer').first().removeClass('active');
    }, 3000);
}

/**
 * Returns the content item identifier for a given widget.
 */
function contentPageGetWidgetId(widget) {
    return widget.attr('id').replace('widget', '');
}

/**
 * Dynamically loads asset files.
 */
function contentPageLoadDynamicAssets(type, pathes, jsEntryPoint) {
    if (-1 === jQuery.inArray(type, ['css', 'js'])) {
        return;
    }
    if (pathes.length < 1) {
        return;
    }

    var downloadAsset = function(path) {
        if (-1 < jQuery.inArray(path, loadedDynamicAssets[type])) {
            if ('js' === type) {
                if (pathes.length > 0) {
                    downloadAsset(pathes.shift());
                } else {
                    if (null !== jsEntryPoint && 'function' === typeof window[jsEntryPoint]) {
                        window[jsEntryPoint]();
                    }
                }
            }
            return;
        }

        if ('css' === type) {
            jQuery('<link />')
                .appendTo('head') // first append for IE8 compatibility
                .attr({
                    type: 'text/css', 
                    rel: 'stylesheet',
                    href: path
                })
            ;
            loadedDynamicAssets[type].push(path);
        } else if ('js' === type) {
            jQuery.contentGetSyncCachedScript(path)
                .done(function (script, textStatus) {
                    loadedDynamicAssets[type].push(path);

                    if (pathes.length > 0) {
                        downloadAsset(pathes.shift());
                    } else {
                        if (null !== jsEntryPoint && 'function' === typeof window[jsEntryPoint]) {
                            window[jsEntryPoint]();
                        }
                    }
                })
            ;
        }
    };

    downloadAsset(pathes.shift());
}

/**
 * Initialises the palette for adding new widgets.
 */
function contentPageInitPalette() {
    jQuery('#palette #paletteTabs > li > a, #palette .grid-stack-item').popover({
        container: 'body',
        placement: function (pop, dom_el) {
            return 'bottom';//window.innerWidth < 768 ? 'bottom' : 'right';
        },
        trigger: 'hover focus'
    });
    jQuery('#palette .grid-stack-item').click(function (event) {
        var gridSection, newId, widget;

        jQuery('#paletteModal').modal('hide');
        gridSection = jQuery('#section' + jQuery('#paletteModal').data('section-number'));

        newId = contentPageTempGetRandomInt(1000, 9000);
        widget = jQuery(this).clone();

        widget.data('typeclass', jQuery(this).data('typeclass'));

        jQuery('#widgetDimensions').data('minwidth', widget.data('minwidth'));
        contentPageApplyDimensionConstraints(widget);
        contentPagePreparePaletteEntryForAddition(widget, newId);

        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.addWidget(widget, 0, 0, widget.attr('data-gs-width'), widget.attr('data-gs-height'), true, widget.attr('data-gs-min-width'));

        contentPageInitWidgetEditing(widget, true);

        suspendAutoSave = true;
    });
}

/**
 * Applies dimension constraints to a certain node.
 */
function contentPageApplyDimensionConstraints(widget) {
    var width, height, minWidth;

    width = parseInt(jQuery('#widgetDimensions').data('width'));
    height = parseInt(jQuery('#widgetDimensions').data('height'));
    minWidth = parseInt(jQuery('#widgetDimensions').data('minwidth'));

    if (minWidth > width) {
        width = minWidth;
    }

    widget.attr('data-gs-width', width);
    widget.attr('data-gs-height', height);
    widget.attr('data-gs-min-width', minWidth);
}

/**
 * Returns transformed markup for turning a palette item into a panel.
 */
function contentPagePreparePaletteEntryForAddition(widget, widgetId) {
    // remove popover data attributes
    widget.removeAttr('title');
    widget.removeAttr('data-title');
    widget.removeAttr('data-content');
    widget.removeAttr('data-original-title');
    widget.removeAttr('data-minwidth');
    widget.removeClass('ui-draggable-handle');

    widget.attr('id', 'widget' + widgetId);
    var widgetContentDiv = widget.find('.grid-stack-item-content').first();
    widgetContentDiv.addClass('panel panel-default');
    var widgetTitle = widgetContentDiv.html();

    var panelMarkup = contentPageGetWidgetPanelMarkup(widgetId, widgetTitle);
    widgetContentDiv.html(panelMarkup);
}

/**
 * Returns the actions for a section.
 */
function contentPageGetSectionActions(isFirstSection) {
    var deleteState = isFirstSection ? ' disabled="disabled"' : '';
    var actions = `
        <div class="btn-group btn-group-sm pull-right" role="group">
            <button type="button" class="btn btn-default add-element" title="${Translator.__('Add element')}"><i class="fa fa-plus"></i> ${Translator.__('Add element')}</button>
            <button type="button" class="btn btn-default change-styles" title="${Translator.__('Styling classes')}"><i class="fa fa-paint-brush"></i> ${Translator.__('Styling classes')}</button>
            <button type="button" class="btn btn-default delete-section" title="${Translator.__('Delete section')}"${deleteState}><i class="fa fa-trash-o"></i> ${Translator.__('Delete section')}</button>
        </div>
    `;

    return actions;
}

function contentPageTempGetRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Initialises section actions.
 */
function contentPageInitSectionActions() {
    jQuery('#widgets h4 .add-element').unbind('click').click(function (event) {
        var gridSectionNumber;

        event.preventDefault();
        gridSectionNumber = jQuery(this).parents('.grid-section').first().attr('id').replace('section', '');
        jQuery('#paletteModal').data('section-number', gridSectionNumber).modal('show');
    });
    jQuery('#widgets h4 .change-styles').unbind('click').click(function (event) {
        var gridSection;

        event.preventDefault();
        gridSection = jQuery(this).parents('.grid-section').first();
        gridSection.find('.style-selector-container').toggleClass('hidden');
        gridSection.find('.style-selector-container button').unbind('click').click(function (btnEvent) {
            jQuery(this).parents('.style-selector-container').addClass('hidden');
            contentPageSave();
        });
    });
    jQuery('#widgets h4 .delete-section').unbind('click').click(function (event) {
        var gridSection;
        var hasWidgets;

        event.preventDefault();
        gridSection = jQuery(this).parents('.grid-section').first();
        hasWidgets = gridSection.find('.grid-stack').first().find('.grid-stack-item').length > 0;
        if (
            !confirm(
                hasWidgets
                    ? Translator.__('Do you really want to delete this section including all contained content?')
                    : Translator.__('Do you really want to delete this section?')
            )
        ) {
            return;
        }
        if (hasWidgets) {
            gridSection.find('.grid-stack').first().find('.grid-stack-item').each(function (index) {
                contentPageDeleteWidget(jQuery(this));
            });
        }
        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridSection.remove();

        contentPageSave();
    });
}

/**
 * Adds another grid section to the current page.
 */
function contentPageAddSection(sectionId, sectionNumber, stylingClasses, scrollToSection) {
    var isFirstSection = jQuery('#widgets .grid-section').length < 1;
    jQuery('#widgets').append('<div id="' + sectionId + '" class="grid-section"><h4>' + contentPageGetSectionActions(isFirstSection) + '<i class="fa fa-fw fa-th"></i> ' + Translator.__('Section') + ' ' + sectionNumber + '</h4><div class="style-selector-container hidden">' + jQuery('#sectionStylesContainer').html() + '</div><div class="well"><div class="grid-stack"></div></div></div>');
    if ('' !== stylingClasses) {
        jQuery('#' + sectionId + ' .style-selector-container select').first().val(stylingClasses.split(' '));
    }
    if (true === scrollToSection) {
        var newTop = jQuery('#' + sectionId).offset().top - 150;
        jQuery('html, body').animate({ scrollTop: newTop }, 500);
    }
}

/**
 * Initialises the gridstack for a given section selector.
 */
function contentPageInitSectionGrid(selector, gridOptions) {
    jQuery(selector).gridstack(gridOptions);

    jQuery(selector).on('change', contentPageSave);

    jQuery(selector).on('resizestart', function (event, ui) {
        contentPageHighlightGrids();
    });
    jQuery(selector).on('resizestop', function (event, ui) {
        contentPageUnhighlightGrids();
    });
    jQuery(selector).on('dragstart', function (event, ui) {
        contentPageHighlightGrids();
    });
    jQuery('body').on('dragstop', function (event, ui) {
        contentPageUnhighlightGrids();
    });

    jQuery(selector).on('dropped', function (event, previousWidget, newWidget) {
        contentPageUnhighlightGrids();

        return;
    });
}

/**
 * Handle dynamic toggle of publication interval date fields.
 */
function contentPageToggleContentActiveDates() {
    var hideDates;

    hideDates = !jQuery('#zikulacontentmodule_contentitem_active').prop('checked');
    jQuery('#zikulacontentmodule_contentitem_activeFrom_date').parents('.form-group').toggleClass('hidden', hideDates);
    jQuery('#zikulacontentmodule_contentitem_activeTo_date').parents('.form-group').toggleClass('hidden', hideDates);
}

/**
 * Opens a modal window for creating/editing a widget.
 */
function contentPageInitWidgetEditing(widget, isCreation) {
    var modal;
    var heading;
    var body;
    var parameters;

    modal = jQuery('#contentItemEditingModal');

    // see https://stackoverflow.com/questions/19506672/
    if (
        ((modal.data('bs.modal') || {})._isShown) /* Bootstrap 4 */
    ||
        ((modal.data('bs.modal') || {}).isShown) /* Bootstrap 3 */
    ) {
        return;
    }

    heading = modal.find('.modal-header h4.modal-title').first();
    body = modal.find('.modal-body').first();

    heading.html(widget.find('.panel-heading h3.panel-title span.title').html());
    body.html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></p>');

    jQuery('#btnDeleteContent').toggleClass('hidden', isCreation);
    jQuery('#btnCancelContent').removeClass('hidden');
    modal.modal('show');

    if (isCreation) {
        parameters = { pageId: pageId, type: widget.data('typeclass') };
    } else {
        parameters = { contentItem: contentPageGetWidgetId(widget) };
    }

    jQuery.getJSON(
        Routing.generate('zikulacontentmodule_contentitem_edit', parameters)
    ).done(function(data) {
        var form;
        var formBody;
        var formError;

        body.html(data.form);

        if (jQuery('#furtherPropertiesSection').length > 0) {
            jQuery('#furtherPropertiesContent').addClass('hidden');
            jQuery('#furtherPropertiesSection legend').addClass('pointer').click(function (event) {
                if (jQuery('#furtherPropertiesContent').hasClass('hidden')) {
                    jQuery('#furtherPropertiesContent').removeClass('hidden');
                    jQuery(this).find('i').removeClass('fa-expand').addClass('fa-compress');
                } else {
                    jQuery('#furtherPropertiesContent').addClass('hidden');
                    jQuery(this).find('i').removeClass('fa-compress').addClass('fa-expand');
                }
            });
        }

        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeFrom');
        zikulaContentInitDateField('zikulacontentmodule_contentitem_activeTo');
        jQuery('#zikulacontentmodule_contentitem_active').change(contentPageToggleContentActiveDates);
        contentPageToggleContentActiveDates();
        body.find('input, select, textarea').change(zikulaContentExecuteCustomValidationConstraints);
        zikulaContentExecuteCustomValidationConstraints();

        contentPageInitialiseAssetsAndEntrypoint(data);

        form = body.find('#contentItemEditForm');
        formBody = body.find('#contentItemEditFormBody');
        formError = body.find('#contentItemEditFormError');
        form.on('submit', function (event) {
            event.preventDefault();
            return false;
        });
        jQuery('#btnSaveContent, #btnDeleteContent').unbind('click').click(function (event) {
            var params;
            var action;

            event.preventDefault();

            params = '';
            if ('btnSaveContent' === jQuery(this).attr('id')) {
                if ('undefined' !== typeof CKEDITOR) {
                    // update textarea
                    for (var instanceName in CKEDITOR.instances) {
                        CKEDITOR.instances[instanceName].updateElement();
                    }
                }
                if (isCreation) {
                    params += 'pageId=' + pageId + '&';
                }
                params += 'action=save&';
                action = isCreation ? 'create' : 'update';
            } else if ('btnDeleteContent' === jQuery(this).attr('id')) {
                params += 'action=delete&';
                action = 'delete';
            }

            if ('delete' !== action) {
                // check input validation
                zikulaContentExecuteCustomValidationConstraints();
                if (!form.get(0).checkValidity()) {
                    return;
                }
            } else if ('delete' === action && !confirm(Translator.__('Do you really want to delete this content?'))) {
                return;
            }

            jQuery('#btnCancelContent').addClass('hidden');
            body.html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></p>');

            jQuery.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: params + form.serialize()
            })
            .done(function (data) {
                modal.modal('hide');
                if ('create' === action) {
                    // update ID
                    widget.attr('id', 'widget' + data.id);
                    widget.find('.dropdown-menu .dropdown-header .widget-id').text(data.id);
                } else if ('delete' === action) {
                    contentPageRemoveWidget(widget);
                }
                if ('undefined' !== typeof data.message) {
                    jQuery('#widgetUpdateDoneAlert').remove();
                    contentPageShowNotification(Translator.__('Success'), data.message, 'widgetUpdateDoneAlert', 'success');
                }
                if ('delete' !== action) {
                    suspendAutoSave = false;
                    contentPageSave();
                    contentPageLoadWidgetData(contentPageGetWidgetId(widget), false);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if ('undefined' !== typeof jqXHR.responseJSON) {
                    if (jqXHR.responseJSON.hasOwnProperty('form')) {
                        formBody.html(jqXHR.responseJSON.form);
                    }
                    formError.html(jqXHR.responseJSON.message);
                } else {
                    contentPageShowNotification(Translator.__('Error'), errorThrown, 'widgetUpdateErrorAlert', 'danger');
                }    
            });
        });
        jQuery('#btnCancelContent').unbind('click').click(function (event) {
            event.preventDefault();
            jQuery(this).addClass('hidden');
            if (isCreation) {
                // remove newly created widget
                contentPageRemoveWidget(widget);
            }
        });
    }).fail(function(jqXHR, textStatus) {
        modal.modal('hide');
        if (isCreation) {
            // remove newly created widget
            contentPageRemoveWidget(widget);
        }
        contentPageShowNotification(Translator.__('Error'), Translator.__('Failed loading the data.'), 'widgetUpdateErrorAlert', 'danger');
    });
}

/**
 * Removes a specific widget.
 */
function contentPageRemoveWidget(widget) {
    var grid = widget.parents('.grid-stack').first().data('gridstack');
    grid.removeWidget(widget);
}

/**
 * Opens a modal window for moving/copying a widget.
 */
function contentPageInitWidgetMovingCopying(widget) {
    var modal;
    var heading;
    var body;

    modal = jQuery('#contentItemEditingModal');

    // see https://stackoverflow.com/questions/19506672/
    if (
        ((modal.data('bs.modal') || {})._isShown) /* Bootstrap 4 */
    ||
        ((modal.data('bs.modal') || {}).isShown) /* Bootstrap 3 */
    ) {
        return;
    }

    heading = modal.find('.modal-header h4.modal-title').first();
    body = modal.find('.modal-body').first();

    heading.html(widget.find('.panel-heading h3.panel-title span.title').html());
    body.html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></p>');

    jQuery('#btnDeleteContent').addClass('hidden');
    jQuery('#btnCancelContent').removeClass('hidden');
    modal.modal('show');

    jQuery.getJSON(
        Routing.generate('zikulacontentmodule_contentitem_movecopy', { contentItem: contentPageGetWidgetId(widget) })
    ).done(function(data) {
        var form;
        var formBody;
        var formError;

        body.html(data.form);

        body.find('input, select').change(zikulaContentExecuteCustomValidationConstraints);
        zikulaContentExecuteCustomValidationConstraints();

        form = body.find('#contentItemEditForm');
        formBody = body.find('#contentItemEditFormBody');
        formError = body.find('#contentItemEditFormError');
        form.on('submit', function (event) {
            event.preventDefault();
            return false;
        });
        jQuery('#btnSaveContent').unbind('click').click(function (event) {
            event.preventDefault();

            // check input validation
            zikulaContentExecuteCustomValidationConstraints();
            if (!form.get(0).checkValidity()) {
                return;
            }

            if (pageId === jQuery('#zikulacontentmodule_movecopycontentitem_destinationPage').val()) {
                alert(Translator.__('Destination page must not be the current page.'));
                return;
            }

            var operationType = jQuery('input[type=radio]:checked').first().val();

            jQuery('#btnCancelContent').addClass('hidden');
            body.html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></p>');

            jQuery.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            })
            .done(function (data) {
                modal.modal('hide');
                if ('undefined' !== typeof data.message) {
                    jQuery('#widgetUpdateDoneAlert').remove();
                    contentPageShowNotification(Translator.__('Success'), data.message, 'widgetUpdateDoneAlert', 'success');
                }
                if ('move' === operationType) {
                    contentPageRemoveWidget(widget);
                    contentPageSave();
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                if ('undefined' !== typeof jqXHR.responseJSON) {
                    if (jqXHR.responseJSON.hasOwnProperty('form')) {
                        formBody.html(jqXHR.responseJSON.form);
                    }
                    formError.html(jqXHR.responseJSON.message);
                } else {
                    contentPageShowNotification(Translator.__('Error'), errorThrown, 'widgetUpdateErrorAlert', 'danger');
                }    
            });
        });
        jQuery('#btnCancelContent').unbind('click').click(function (event) {
            event.preventDefault();
            jQuery(this).addClass('hidden');
        });
    }).fail(function(jqXHR, textStatus) {
        modal.modal('hide');
        contentPageShowNotification(Translator.__('Error'), Translator.__('Failed loading the data.'), 'widgetUpdateErrorAlert', 'danger');
    });
}

/**
 * Returns the actions for a widget.
 */
function contentPageGetWidgetActions(widgetId) {
    var actions = `
        <div class="dropdown">
            <a class="dropdown-toggle pull-right" title="${Translator.__('Actions')}" id="dropdownMenu${widgetId}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="sr-only">${Translator.__('Actions')}</span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu${widgetId}">
                <li class="dropdown-header">${Translator.__('Content item')} ID: <span class="widget-id">${widgetId}</span></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">${Translator.__('Basic')}</li>
                <li><a class="edit-item" title="${Translator.__('Edit this element')}"><i class="fa fa-fw fa-pencil"></i> ${Translator.__('Edit')}</a></li>
                <li><a class="delete-item" title="${Translator.__('Delete this element')}"><i class="fa fa-fw fa-trash-o text-danger"></i> ${Translator.__('Delete')}</a></li>
                <li><a class="activate-item" title="${Translator.__('Activate this element')}"><i class="fa fa-fw fa-circle text-danger"></i> ${Translator.__('Activate')}</a></li>
                <li><a class="deactivate-item" title="${Translator.__('Deactivate this element')}"><i class="fa fa-fw fa-circle text-success"></i> ${Translator.__('Deactivate')}</a></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">${Translator.__('Advanced')}</li>
                <li><a class="clone-item" title="${Translator.__('Duplicate this element')}"><i class="fa fa-fw fa-clone"></i> ${Translator.__('Duplicate')}</a></li>
                <li><a class="move-copy-item" title="${Translator.__('Move or copy this element to another page')}"><i class="fa fa-fw fa-long-arrow-right"></i> ${Translator.__('Move/Copy')}</a></li>
            </ul>
        </div>
    `;

    return actions;
}

/**
 * Deletes a widget.
 */
function contentPageDeleteWidget(widget) {
    jQuery.ajax({
        type: 'post',
        url: Routing.generate('zikulacontentmodule_contentitem_edit', { contentItem: contentPageGetWidgetId(widget) }),
        data: { action: 'delete' },
        async: false
    })
    .done(function (data) {
        contentPageRemoveWidget(widget);
        if ('undefined' !== typeof data.message) {
            jQuery('#widgetUpdateDoneAlert').remove();
            contentPageShowNotification(Translator.__('Success'), data.message, 'widgetUpdateDoneAlert', 'success');
        }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        var errorMessage;

        if ('undefined' !== typeof jqXHR.responseJSON) {
            errorMessage = jqXHR.responseJSON.message;
        } else {
            errorMessage = errorThrown;
        }
        jQuery('#widgetUpdateErrorAlert').remove();
        contentPageShowNotification(Translator.__('Error'), errorMessage, 'widgetUpdateErrorAlert', 'danger');
    });
}

/**
 * Initialises widget actions.
 */
function contentPageInitWidgetActions() {
    jQuery('.grid-stack .grid-stack-item a.edit-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageInitWidgetEditing(widget, false);
    });
    jQuery('.grid-stack .grid-stack-item a.delete-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        if (!confirm(Translator.__('Do you really want to delete this content?'))) {
            return;
        }

        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageDeleteWidget(widget);
    });
    jQuery('.grid-stack .grid-stack-item a.activate-item, .grid-stack .grid-stack-item a.deactivate-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        jQuery.ajax({
            method: 'POST',
            url: Routing.generate('zikulacontentmodule_ajax_toggleflag'),
            data: {
                ot: 'contentItem',
                field: 'active',
                id: contentPageGetWidgetId(widget)
            },
            success: function (data) {
                jQuery('#widgetUpdateDoneAlert').remove();
                contentPageShowNotification(Translator.__('Success'), Translator.__('Done! Content saved!'), 'widgetUpdateDoneAlert', 'success');
                contentPageLoadWidgetData(contentPageGetWidgetId(widget), false);
            }
        });
    });
    jQuery('.grid-stack .grid-stack-item a.clone-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        jQuery.ajax({
            method: 'POST',
            url: Routing.generate('zikulacontentmodule_contentitem_duplicate', { contentItem: contentPageGetWidgetId(widget) }),
            data: {pageId: pageId},
            success: function (data) {
                var newWidget;

                newWidget = contentPageCreateNewWidget(data.id);

                jQuery('#widgetUpdateDoneAlert').remove();
                contentPageShowNotification(Translator.__('Success'), data.message, 'widgetUpdateDoneAlert', 'success');

                var grid = widget.parents('.grid-stack').first().data('gridstack');
                grid.addWidget(newWidget, 0, 0, widget.attr('data-gs-width'), widget.attr('data-gs-height'), true, widget.attr('data-gs-min-width'));

                contentPageLoadWidgetData(data.id, true);
            }
        });
    });
    jQuery('.grid-stack .grid-stack-item a.move-copy-item').unbind('click').click(function (event) {
        var widget;

        event.preventDefault();
        widget = jQuery(this).parents('.grid-stack-item').first();
        contentPageInitWidgetMovingCopying(widget);
    });
    jQuery('.grid-stack-item').hover(
        function() {
            jQuery(this).addClass('hovered');
        }, function() {
            jQuery(this).removeClass('hovered');
            jQuery('.dropdown.open').removeClass('open');
        }
    );
}

/**
 * Removes all grid sections from the current page.
 */
function contentPageClear() {
    jQuery('.grid-section').each(function (index) {
        var gridSection = jQuery(this);
        var grid = gridSection.find('.grid-stack').first().data('gridstack');
        grid.destroy();
        gridSection.remove();
    });
}

/**
 * Builds a placeholder widget for a new content item.
 */
function contentPageCreateNewWidget(nodeId) {
    var widgetTitle;
    var widgetPanelClass;
    var widgetMarkup;
    var widget;

    widgetTitle = Translator.__('Content item');
    widgetPanelClass = 'default';
    widgetMarkup = contentPageGetWidgetMarkup(nodeId, widgetTitle, widgetPanelClass);
    widget = jQuery(widgetMarkup);

    return widget;
}

/**
 * Builds a widget.
 */
function contentPageGetWidgetMarkup(nodeId, title, panelClass) {
    var panelMarkup = contentPageGetWidgetPanelMarkup(nodeId, title);

    return '<div id="widget' + nodeId + '"><div class="grid-stack-item-content panel panel-' + panelClass + '">' + panelMarkup + '</div></div>';
}

/**
 * Builds a widget panel.
 */
function contentPageGetWidgetPanelMarkup(nodeId, title) {
    var widgetActions = contentPageGetWidgetActions(nodeId);
    var widgetTitle = '<h3 class="panel-title">' + widgetActions + '<span class="title">' + title + '</span></h3>';
    var widgetContent = '<p></p>';
    widgetContent += '<p><small class="width-note" style="background-color: #ffe"></small></p>';

    return '<div class="panel-heading">' + widgetTitle + '</div><div class="panel-body">' + widgetContent + '</div>';
}

/**
 * Loads content item assets and executes entry point.
 */
function contentPageInitialiseAssetsAndEntrypoint(data) {
    if ('undefined' !== typeof data.assets) {
        if ('undefined' !== typeof data.assets.css) {
            contentPageLoadDynamicAssets('css', data.assets.css, null);
        }
        if ('undefined' !== typeof data.assets.js) {
            var jsEntryPoint = 'undefined' !== typeof data.jsEntryPoint ? data.jsEntryPoint : null;
            contentPageLoadDynamicAssets('js', data.assets.js, jsEntryPoint);
        }
    }
}

/**
 * Updates a widget with it's data.
 */
function contentPageLoadWidgetData(nodeId, openEditForm) {
    var widget;

    widget = jQuery('#widget' + nodeId);

    widget.find('.panel-title .title').html(Translator.__('Loading...'));
    widget.find('.panel-body').html('<p class="text-center"><i class="fa fa-refresh fa-spin fa-4x"></i></p>');
    jQuery.getJSON(Routing.generate('zikulacontentmodule_contentitem_displayediting', {contentItem: nodeId}), function (data) {
        var isActive;
        widget.find('.panel-title .title').html(data.title);
        widget.find('.panel-body').html(data.content + '<p><small class="width-note" style="background-color: #ffe"></small></p>');
        widget.find('.panel').removeClass(function (index, className) {
            return (className.match (/(^|\s)panel-\S+/g) || []).join(' ');
        }).addClass('panel-' + data.panelClass);

        isActive = data.panelClass !== 'danger';
        widget.find('.panel-title .dropdown .dropdown-menu .activate-item').toggleClass('hidden', isActive);
        widget.find('.panel-title .dropdown .dropdown-menu .deactivate-item').toggleClass('hidden', !isActive);

        contentPageInitialiseAssetsAndEntrypoint(data);
        if (true === openEditForm) {
            widget.find('.panel-title .dropdown .dropdown-menu .edit-item').click();
        }
    }).fail(function(jqxhr, textStatus, error) {
        if ('error' === textStatus && 'Not Found' === error) {
            widget.remove();
        }
    });
}

/**
 * Updates grid attributes for all widgets.
 */
function contentPageUpdateAllGridAttributes() {
    if (jQuery('#debugSavedData').length < 1) {
        return;
    }
    _.each(widgetData, function (section) {
        var lastNode = null;
        var widgets = GridStackUI.Utils.sort(section.widgets);
        _.each(widgets, function (node) {
            var widget = jQuery('#widget' + node.id);
            var colOffset = 0;
            if (null !== lastNode && node.y === lastNode.y) {
                colOffset = node.x - (lastNode.x + lastNode.width);
            } else {
                colOffset = node.x;
            }
            contentPageUpdateGridAttributes(widget, colOffset);
            lastNode = node;
        });
    });
}

/**
 * Determines grid attributes for a widget.
 */
function contentPageUpdateGridAttributes(widget, colOffset) {
    if (jQuery('#debugSavedData').length < 1) {
        return;
    }
    var node = widget.data(nodeDataAttribute);
    var gridAttributes = 'col-sm-' + node.width;
    if (colOffset > 0) {
        gridAttributes += ' col-sm-offset-' + colOffset;
    }
    widget.find('small.width-note').text(gridAttributes);
}

/**
 * Loads widget data from serialisation.
 */
function contentPageUnserialiseWidgets(containerId, widgetList) {
    contentPageInitSectionGrid('#' + containerId + ' .grid-stack', gridOptions);
    var grid = jQuery('#' + containerId + ' .grid-stack').data('gridstack');
    var lastNode = null;
    var widgets = GridStackUI.Utils.sort(widgetList);
    _.each(widgets, function (node) {
        var widget;

        widget = contentPageCreateNewWidget(node.id);
        var minWidth = 'undefined' != typeof node.minWidth ? node.minWidth : jQuery('#widgetDimensions').data('minwidth');
        grid.addWidget(widget, node.x, node.y, node.width, /*node.height*/jQuery('#widgetDimensions').data('height'), false, node.minWidth);
        var colOffset = 0;
        if (null !== lastNode && node.y === lastNode.y) {
            colOffset = node.x - (lastNode.x + lastNode.width);
        } else {
            colOffset = node.x;
        }
        contentPageUpdateGridAttributes(widget, colOffset);
        lastNode = node;
    });
    _.each(widgets, function (node) {
        contentPageLoadWidgetData(node.id, false);
    });
}

/**
 * Loads serialised grid and widget data.
 */
function contentPageLoad() {
    var sectionNumber;
    contentPageClear();
    sectionNumber = 0;
    _.each(widgetData, function (section) {
        sectionNumber++;
        contentPageAddSection(section.id, sectionNumber, section.stylingClasses, false);
        contentPageInitSectionActions();
        contentPageUnserialiseWidgets(section.id, section.widgets);
    });
    if (orphanData.length > 0) {
        sectionNumber++;
        contentPageAddSection('section' + sectionNumber, sectionNumber, '', false);
        contentPageInitSectionActions();
        contentPageInitSectionGrid('#section' + sectionNumber + ' .grid-stack', gridOptions);
        _.each(orphanData, function (contentItemId) {
            var newWidget;
            var grid;
            var width;
            var height;
            var minWidth;

            newWidget = contentPageCreateNewWidget(contentItemId);

            grid = jQuery('#section' + sectionNumber + ' .grid-stack').first().data('gridstack');
            width = jQuery('#widgetDimensions').data('width');
            height = jQuery('#widgetDimensions').data('height');
            minWidth = jQuery('#widgetDimensions').data('minwidth');
            grid.addWidget(newWidget, 0, 0, width, height, true, minWidth);

            contentPageLoadWidgetData(contentItemId, false);
        });

    }
    contentPageInitWidgetActions();
}

/**
 * Sorts widget for serialisation.
 */
function contentPageSortWidgetsForSave(nodes) {
    return nodes.sort(function (a, b) {
        var aNode = jQuery(a).data(nodeDataAttribute);
        var bNode = jQuery(b).data(nodeDataAttribute);
        if (aNode.y !== bNode.y) {
            return (aNode.y < bNode.y ? -1 : ((aNode.y > bNode.y) ? 1 : 0));
        }

        return (aNode.x < bNode.x ? -1 : ((aNode.x > bNode.x) ? 1 : 0));
    });
}

/**
 * Collects widget data for serialisation.
 */
function contentPageSerialiseWidgets(elements) {
    elements = contentPageSortWidgetsForSave(elements);

    return _.map(elements, function (widget) {
        widget = jQuery(widget);
        var node = widget.data(nodeDataAttribute);

        return {
            id: contentPageGetWidgetId(widget),
            x: node.x,
            y: node.y,
            width: node.width,
            minWidth: node.minWidth/*,
            height: node.height*/
        };
    });
}

/**
 * Saves serialised grid and widget data.
 */
function contentPageSave() {
    var sectionCounter;
    if (true === suspendAutoSave) {
        return;
    }
    sectionCounter = 0;
    widgetData = _.map(jQuery('#widgets .grid-section'), function (section) {
        section = jQuery(section);
        return {
            id: 'section' + ++sectionCounter,
            stylingClasses: (section.find('.style-selector-container select').first().val() || []).join(' '),
            widgets: contentPageSerialiseWidgets(section.find('.well > .grid-stack > .grid-stack-item:visible').not('.grid-stack-placeholder'))
        }
    });

    jQuery.ajax({
        type: 'post',
        url: Routing.generate('zikulacontentmodule_page_updatelayout', {id: pageId}),
        data: {
            layoutData: widgetData
        }
    })
    .done(function (data) {
        jQuery('#layoutUpdateDoneAlert').remove();
        contentPageShowNotification(Translator.__('Success'), data.message, 'layoutUpdateDoneAlert', 'success');
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        jQuery('#layoutUpdateErrorAlert').remove();
        contentPageShowNotification(Translator.__('Error'), errorThrown, 'layoutUpdateErrorAlert', 'danger');
    });

    if (jQuery('#debugSavedData').length > 0) {
        jQuery('#debugSavedData').text(JSON.stringify(widgetData, null, '    '));
    }

    contentPageUpdateAllGridAttributes();
    contentPageInitWidgetActions();
}

var gridsHighlighted = false;

/**
 * Initialises the grid highlighter.
 */
function contentPageInitGridHiglighter() {
    jQuery('body').prepend('<div id="grid-displayer" class="hidden"><div class="gd-container"><div class="gd-row"></div></div></div>');
}

/**
 * Displays the grid columns for easier orientation.
 */
function contentPageHighlightGrids() {
    var options = {
        amountOfColumns: 12,
        gutterWidth: 18,
        outerLimit: 20, /* surrounding well */
        colour: '#f4f5b4',
        opacity: 0.3,
        zIndex: 999
    };

    var $gdContainer = jQuery('#grid-displayer .gd-container');
    var $gdRow = jQuery('#grid-displayer .gd-row');

    $gdRow.addClass('row').empty();
    for (var i = 0; i < options.amountOfColumns; i++) {
        $gdRow.append('<div class="gd-column col-xs-1">&nbsp;</div>');
    }

    jQuery('#grid-displayer .gd-column').css({
        borderWidth: '0 ' + (options.gutterWidth / 2) + 'px',
        borderStyle: 'solid',
        borderColor: '#fff',
        padding: 0,
        backgroundColor: options.colour,
        outline: '1px solid ' + options.colour,
        opacity: options.opacity
    });

    var firstGridStack = jQuery('.grid-stack').first();

    jQuery('#grid-displayer').css({
        zIndex: options.zIndex,
        left: ((firstGridStack.offset().left + options.outerLimit - 5) + 'px'),
        width: ((firstGridStack.width() - options.outerLimit - 10) + 'px')
    });
    jQuery('#grid-displayer').removeClass('hidden');
    gridsHighlighted = true;
}

/**
 * Removes the grid columns display again.
 */
function contentPageUnhighlightGrids() {
    jQuery('#grid-displayer').addClass('hidden');
    gridsHighlighted = false;
}

/**
 * Initialisation after page has been loaded.
 */
jQuery(document).ready(function () {
    jQuery('.add-section').click(function () {
        var sectionNumber = jQuery('#widgets .grid-section').length + 1;
        contentPageAddSection('section' + sectionNumber, sectionNumber, '', true);
        contentPageInitSectionActions();
        contentPageInitSectionGrid('#section' + sectionNumber + ' .grid-stack', gridOptions);
        contentPageSave();
    });
    jQuery('.exit-page').click(function (event) {
        event.preventDefault();
        window.location = jQuery(this).data('url');
    });
    contentPageInitPalette();

    suspendAutoSave = true;
    contentPageLoad();
    suspendAutoSave = false;
    contentPageInitGridHiglighter();
    contentPageFixWysiwygBehaviour();
});

// repair popup focus editors inside Bootstrap modal
function contentPageFixWysiwygBehaviour() {
    var editor;

    editor = jQuery('#wysiwygEditor').data('default');

    if ('CKEditor' === editor) {
        // https://gist.github.com/james2doyle/65d06029bfd128dd5ecc
        jQuery.fn.modal.Constructor.prototype.enforceFocus = function() {
            var modal_this = this;
            jQuery(document).on('focusin.modal', function (e) {
                if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                    && !jQuery(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                    && !jQuery(e.target.parentNode).hasClass('cke_dialog_ui_input_text')
                ) {
                    modal_this.$element.focus()
                }
            })
        };
    } else if ('Summernote' === editor) {
        // https://stackoverflow.com/questions/21786258/summernote-modals-locked-within-pure-bootstrap-modals
        jQuery(document).on('show.bs.modal', '.modal', function (event) {
            var zIndex = 100000 + (10 * jQuery('.modal:visible').length);
            jQuery(this).css('z-index', zIndex);
            setTimeout(function () {
                jQuery('.modal-backdrop').not('.modal-stack').first().css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        }).on('hidden.bs.modal', '.modal', function (event) {
            jQuery('.modal:visible').length && jQuery('body').addClass('modal-open');
        });
    } else if ('TinyMce' === editor) {
        // https://stackoverflow.com/questions/18111582/tinymce-4-links-plugin-modal-in-not-editable
        jQuery(document).on('focusin', function(e) {
            if (jQuery(e.target).closest('.mce-window').length || jQuery(e.target).closest('.moxman-window').length) {
                e.stopImmediatePropagation();
            }
        });
    }
}
