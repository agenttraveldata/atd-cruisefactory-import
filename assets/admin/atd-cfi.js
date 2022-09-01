jQuery(function () {
    jQuery('#atd-cfi-verify-xml').on('click', function () {
        var notification = jQuery(this).closest('.inside').find('.atd-cfi-xml-verification');

        wp.ajax.post('atd_cfi_verify_xml', {
            verify_xml: atd_cfi.verify_xml,
            key: jQuery('#' + atd_cfi.atd_cf_xml_key_field).val()
        }).done(function (r) {
            if (r.hasOwnProperty('services')) {
                if (r.services !== false) {
                    notification.removeClass('atd-cfi-not-verified').addClass('atd-cfi-verified').html('Verified');
                }
            }
        }).fail(function () {
            notification.removeClass('atd-cfi-verified').addClass('atd-cfi-not-verified').html('Not Verified');
        });
    });

    var recaptchaNotification;
    jQuery('#atd-cfi-recaptcha-save').on('click', function () {
        var saveButton = jQuery(this);
        var data = {save_recaptcha: atd_cfi.save_recaptcha};
        data[atd_cfi.recaptcha_type_field] = jQuery('.atd-cfi-recaptcha-type:is(:checked)').val();
        data[atd_cfi.recaptcha_site_field] = jQuery('#atd_cfi_recaptcha_site_key').val();
        data[atd_cfi.recaptcha_secret_field] = jQuery('#atd_cfi_recaptcha_secret_key').val();
        wp.ajax.post('atd_cfi_save_recaptcha_keys', data).done(function (r) {
            if (r.hasOwnProperty('message')) {
                if (recaptchaNotification) {
                    recaptchaNotification.remove();
                }

                recaptchaNotification = document.createElement('span');
                recaptchaNotification.innerHTML = r.message;
                saveButton.parent()[0].insertBefore(recaptchaNotification, saveButton.nextSibling);
                setTimeout(function () {
                    jQuery(recaptchaNotification).fadeOut(2000);
                }, 2000);
            }
        });
    });

    var optionsNotification;
    jQuery('#atd-cfi-options-save').on('click', function () {
        var saveButton = jQuery(this);
        var data = {save_options: atd_cfi.save_options};
        jQuery.find('.atd-cfi-options').map(function (el) {
            switch (el.type) {
                case 'checkbox':
                    if (el.checked) {
                        data[el.id] = '1';
                    }
                    break;
                default:
                    data[el.id] = el.value;
            }
        });

        wp.ajax.post('atd_cfi_save_options', data).done(function (r) {
            if (r.hasOwnProperty('message')) {
                if (optionsNotification) {
                    optionsNotification.remove();
                }

                optionsNotification = document.createElement('span');
                optionsNotification.innerHTML = r.message;
                saveButton.parent()[0].insertBefore(optionsNotification, saveButton.nextSibling);
                setTimeout(function () {
                    jQuery(optionsNotification).fadeOut(2000);
                }, 2000);
            }
        });
    });

    var capabilityNotification;
    jQuery('#atd-cfi-capability-save').on('click', function () {
        var saveButton = jQuery(this);
        var data = {save_min_capability: atd_cfi.save_min_capability};
        data[atd_cfi.min_capability_field] = jQuery('#' + atd_cfi.min_capability_field).val();
        wp.ajax.post('atd_cfi_save_minimum_capability', data).done(function (r) {
            if (r.hasOwnProperty('message')) {
                if (capabilityNotification) {
                    capabilityNotification.remove();
                }

                capabilityNotification = document.createElement('span');
                capabilityNotification.innerHTML = r.message;
                saveButton.parent()[0].insertBefore(capabilityNotification, saveButton.nextSibling);
                setTimeout(function () {
                    jQuery(capabilityNotification).fadeOut(2000);
                }, 2000);
            }
        });
    });

    jQuery('#atd-cfi-increment-import').on('click', function () {
        var button = jQuery(this);
        button.html('<span class="spinner is-active"></span> Importing...');

        wp.ajax.post('atd_cfi_import_xml', {
            xml_import: atd_cfi.xml_import
        }).done(function (r) {
            if (r.hasOwnProperty('success')) {
                if (r.success === true) {
                    button.html('Completed Successfully.');
                } else {
                    button.html('Failed import.');
                }
            }
        }).fail(function () {
            button.html('Error importing!');
        });
    });

    var atdTableBody = jQuery('#atd-cfi-services tbody');
    if (atdTableBody.length) {
        wp.ajax.post('atd_cfi_get_feeds', {
            get_feeds: atd_cfi.get_feeds
        }).done(function (r) {
            if (r.hasOwnProperty('feeds')) {
                atdTableBody.empty();

                jQuery.each(r.feeds, function (k, feed) {
                    var feedName = feed.name.replace(/([A-Z])/g, " $1").trim() + 's';
                    var lastUpdate = null;
                    if (feed.last_updated) {
                        lastUpdate = new Date(feed.last_updated.date);
                    }
                    atdTableBody.append('<tr><td class="row-title">' + feedName + '</td><td>' + (lastUpdate ? lastUpdate.toLocaleString() : 'Never') + '</td></tr>');
                });
            }
        });
    }
});