window.ProjectEditor = function(options) {

    // description counter
    var inp_description = $('#inp-description');
    var description_counter = $('#description-counter');
    function refreshDescriptionCounter() {
        var l = inp_description.val().length;
        if(l >= options.config.description_max_length) {
            inp_description.val(inp_description.val().substr(0, options.config.description_max_length));
            l = options.config.description_max_length;
        }
        description_counter.text(l + '/' + options.config.description_max_length);
    }
    refreshDescriptionCounter();
    inp_description.bind('input propertychange', refreshDescriptionCounter);


    // file inputs
    $('.custom-file-input').on('change', function() {
        var el = $(this);
        var name = el.val().split("\\").pop();
        el.siblings('.custom-file-label').addClass('selected').html(name);
        el.closest('.custom-file').addClass('custom-file-selected');
    });
    $('.custom-file-clear').on('click', function(e) {
        e.preventDefault();
        var el = $(this);
        el.siblings('input').val('');
        el.siblings('.custom-file-label').removeClass('selected').html('');
        el.closest('.custom-file').removeClass('custom-file-selected');
    });

    $('.link-delete-file').on('click', function(e) {
        e.preventDefault();
        var el = $(this);

        var team_member_row = el.closest('.team-member-row');
        if(team_member_row.length) {
            var file = 'team_member.' + team_member_row.find('input[name="team_member_id[]"]').val();
        } else {
            var file = 'project.' + el.data('file');
        }
        var text = el.text();
        el.closest('.file-box').append(
            $('<input type="hidden">').attr('name', 'delete_uploads[]').val(file)
        )
        el.closest('.custom-file-controls').text(text)
    })


    // team members
    function updateTeamMembersUI() {
        $('#btn-add-member').toggle(
            $('#team-members').find('.team-member-row').length < $('#team-members').data('max-members')
        );
        $('#team-members-header').toggle(
            $('#team-members').children().length > 0
        );
    }

    $('#btn-add-member').on('click', function(e) {
        e.preventDefault();

        var rows = $('#team-members').find('.team-member-row').length;
        if (rows >= $('#team-members').data('max-members')) {
            updateTeamMembersUI();
            return;
        }

        var row = $('#team-member-template').children(":first").clone(true);
        row.find('.is-valid').removeClass('.is-valid');
        $('#team-members').append(row);
        row.show();
        updateTeamMembersUI();
    })

    $('.btn-remove-member').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.team-member-row').remove();
        updateTeamMembersUI();
    })
    updateTeamMembersUI();



    // submit

    function showGroupError(form_group, message) {
        form_group.find('.form-control,.form-check-input,.custom-file-input').addClass('is-invalid');
        form_group.find('.invalid-feedback').remove();
        form_group.append('<div class="invalid-feedback">' + message + '</div>');
    }

    function hideGroupError(div) {
        div.find('.invalid-feedback').remove();
        div.find('.form-control,.form-check-input,.custom-file-input').removeClass('is-invalid');
    }

    // Mark the tab headers whose panes contain fields with validation errors.
    // Recomputed from scratch each time so the state never gets stale.
    function refreshTabErrors() {
        options.form.find('.tab-pane').each(function () {
            var pane = $(this);
            var pane_id = pane.attr('id');
            if (!pane_id) {
                return;
            }
            options.form.find('.nav-link[href="#' + pane_id + '"]').toggleClass(
                'has-errors',
                pane.find('.is-invalid').length > 0
            );
        });
    }

    options.form.find('.form-control,.form-check-input,.custom-file-input').on('focus', function() {
        setTimeout(function() {
            hideGroupError($(this).closest('div'));
            refreshTabErrors();
        }, 100);
    })

    function getGroupLabel(group) {
        // Standard form groups and checks have a <label>; raw file boxes
        // (file-input partial) have a .file-box-title instead. The
        // .custom-file-label ("Choisir un fichier...") is only a placeholder,
        // not a field label. Returns null when no meaningful label exists
        // (e.g. team member rows).
        var title = group.find('.file-box-title').first();
        if (title.length) {
            return title.text().trim();
        }
        var label = group.find('label').not('.custom-file-label').first();
        if (label.length) {
            return label.text().trim();
        }
        return null;
    }

    function focusField(control) {
        var pane = control.closest('.tab-pane');
        if (pane.length && options.activateTab) {
            options.activateTab(pane.attr('id'));
        }
        if (control.length) {
            control[0].scrollIntoView({ block: 'center' });
            control.focus();
        }
    }

    function displayErrors(errors) {
        var field_errors = [];
        options.form.find('.form-group,.form-check,.custom-file').each(function() {
            var group = $(this);
            var control = group.find('.form-control,.form-check-input,.custom-file-input');
            var name = control.attr('name');
            if(name in errors) {
                showGroupError(group, errors[name][0])
                field_errors.push({
                    label: getGroupLabel(group),
                    message: errors[name][0],
                    control: control.first()
                });
                delete(errors[name]);
            } else {
                hideGroupError(group)
            }
        });
        refreshTabErrors();
        var missed_errors = [];
        for(var k in errors) {
            missed_errors.push(errors[k]);
        }
        displayErrorsSummary(field_errors, missed_errors);
    }

    function displayErrorsSummary(field_errors, missed_errors) {
        var div = $('#project-errors-box');
        if (!field_errors.length && !missed_errors.length) {
            div.remove();
            return;
        }
        if (!div.length) {
            div = $('<div class="alert alert-danger" id="project-errors-box"></div>');
            div.insertBefore(options.form);
        }
        div.empty();
        if (field_errors.length) {
            // Summary list of fields with errors ; clicking an item switches
            // to the tab containing the field and focuses it.
            var ul = $('<ul class="mb-0 pl-3"></ul>');
            $.each(field_errors, function (i, error) {
                // Without a meaningful label, the message itself becomes the
                // link, with no "label : " prefix.
                var link = $('<a href="#"></a>').text(error.label || error.message).on('click', function (e) {
                    e.preventDefault();
                    focusField(error.control);
                });
                var li = $('<li></li>').append(link);
                if (error.label) {
                    li.append(document.createTextNode(' : ' + error.message));
                }
                ul.append(li);
            });
            div.append(ul);
        }
        $.each(missed_errors, function (i, message) {
            div.append($('<div class="mt-1"></div>').text(message));
        });
    }

    function handleUnauthorizedResponse(xhr) {
        if(xhr.status == 401) {
            alert('Veuillez vous reconnecter pour continuer.');
            window.location = '/';
        }
    }

    function submit(extra_data) {
        var data = new FormData(options.form[0]);
        for(var k in extra_data) {
            data.append(k, extra_data[k]);
        }

        $('#controls-bar').hide();

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            url: options.form.attr('action'),
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.location) {
                    window.location = res.location;
                }
            },
            error: function(xhr) {
                handleUnauthorizedResponse(xhr);
                var res = xhr.responseJSON;
                if(res.errors) {
                    displayErrors(res.errors)
                }
                options.onError && options.onError();
            }
        });
    }


    return {
        submit: submit
    }
}
