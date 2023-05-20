jQuery(function($) {

    var csrf_parameter_name = $('[name="csrf_parameter_name"]').attr('content');
    var csrf_token = $('[name="'+csrf_parameter_name+'"]').attr('content');

    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
        if (options.type.toLowerCase() === "post") {
            if (options.data instanceof FormData) {
                options.data.append(csrf_parameter_name, csrf_token);
            } else {
                options.data = options.data || "";
                options.data += options.data ? "&" : "";
                options.data += csrf_parameter_name + "=" + csrf_token;
            }
        }
    });

    $(document).on('submit', 'form', function() {
        if (!$(this).find('[name=' + csrf_parameter_name + ']').length) {
            $('<input>', {type: 'hidden', name: csrf_parameter_name, value: csrf_token}).appendTo($(this));
        }
        return true;
    });

    function send(url, type, params, data_type) {
        if (data_type === undefined) {
            data_type = 'json';
        }
        $('body').addClass('loading');
        return $.ajax({
            url: url,
            type: type,
            data: params,
            dataType: data_type,
        })
            .always(function() {
                $('body').removeClass('loading');
            })
    }

    function show_modal(class_modal) {
        const $modal = $(class_modal).clone();
        $modal.addClass('modal').appendTo('body');
        $('body').addClass('show_modal');
        $('.modal').addClass('fade');
        setTimeout(() => {
            $('.modal').removeClass('fade').addClass('show');
        }, 300);
    }

    function hide_modal() {
        $('.modal').removeClass('show');
        $('body').removeClass('show_modal');
        $('.modal').remove();
    }

    function set_login_form($login_form) {
        $('body').addClass('login_page');
        $('body').find('.content_container .container').html($login_form);
    }

    $(document).on('submit', '.login-form form', function(e) {
        e.preventDefault();
        const $form = $(this);
        send(base_url + '/auth/login', 'POST', $form.serialize())
            .then(function(response) {
                if (response.status === 'error') {
                    $('.login-form').html(response.view);
                } else {
                    show_modal('.authorization_congratulations');
                    setTimeout(() => {
                        hide_modal();
                        $('body').removeClass('login_page');
                        $('body').find('.content_container .container').html(response.view);
                    }, 5 * 1000);
                }
            });
    });

    $(document).on('change', '#change_user', function(e) {
        e.preventDefault();
        const $self = $(this);
        const user_id = parseInt($self.val());
        if (user_id === 0) {
            $res = send(base_url, 'GET', {}, 'html').then(function(response) {
                const $login_form = $(response).find('.login-form');
                set_login_form($login_form);
            });
            return;
        }

        send(base_url + '/auth/change-current-user', 'POST', {change_user_id: user_id})
            .then(function(response) {
                if (response.status && response.status === 'ok') {
                    $('body').removeClass('login_page');
                    $('body').find('.content_container .container').html(response.view);
                }
            });
    });

    $(document).on('click', '.logout', function(e) {
        e.preventDefault();
        const user_id = $(this).attr('data-user-id');
        send(base_url + '/auth/logout', 'POST', {user_id: user_id})
            .then(function(response) {
                if (response.status && response.status === 'ok') {
                    set_login_form(response.view);
                }
            });
    })
})