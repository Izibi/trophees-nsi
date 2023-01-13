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
    function toggleTeamMembersHeader() {
        $('#team-members-header').toggle(
            $('#team-members').children().length > 0
        );
    }

    $('#btn-add-member').on('click', function(e) {
        e.preventDefault();
        var row = $('#team-member-template').children(":first").clone(true);
        row.find('.is-valid').removeClass('.is-valid');
        $('#team-members').append(row);
        row.show();
        toggleTeamMembersHeader();
    })

    $('.btn-remove-member').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.team-member-row').remove();
        toggleTeamMembersHeader();
    })
    toggleTeamMembersHeader();



    // submit

    function showGroupError(form_group, message) {
        form_group.find('.form-control,.form-check-input').addClass('is-invalid');
        form_group.find('.invalid-feedback').remove();
        form_group.append('<div class="invalid-feedback">' + message + '</div>');
    }

    function hideGroupError(div) {
        div.find('.invalid-feedback').remove();
        div.find('.form-control,.form-check-input').removeClass('is-invalid');
    }

    options.form.find('.form-control,.form-check-input').on('focus', function() {
        setTimeout(function() {
            hideGroupError($(this).closest('div'));
        }, 100);
    })

    function displayErrors(errors) {
        options.form.find('.form-group,.form-check').each(function() {
            var group = $(this);
            var control = group.find('.form-control,.form-check-input');
            var name = control.attr('name');
            if(name in errors) {
                showGroupError(group, errors[name][0])
                delete(errors[name]);
            } else {
                hideGroupError(group)
            }
        });
        var missed_errors = [];
        for(var k in errors) {
            missed_errors.push(errors[k]);
        }
        displayErrorsAlert(missed_errors);
    }


    function displayErrorsAlert(errors) {
        var div = $('#project-errors-box');
        if(!errors.length) {
            div.remove();
            return;
        }
        if(!div.length) {
            div = $('<div class="alert alert-danger" id="project-errors-box"></div>');
            div.insertBefore(options.form);
        }
        div.html(errors.join('<br>'));
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
                if(res.finalization_errors) {
                    displayErrorsAlert(res.finalization_errors);
                    options.onError && options.onError();
                } else if(res.location) {
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