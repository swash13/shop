$(function() {
    $('.langs-list a').each(function() {
        $(this).css('background-image', 'url(/assets/images/langs/' + $(this).data('lang') + '.png)');
    })

    $('form .field .langs-list a').click(function() {
        if (!$(this).hasClass('active')) {
            $(this).parents('ul:first').find('a.active').removeClass('active');
            $(this).addClass('active');
            $(this).parents('ul:first').siblings('.langs-field').removeClass('active');
            $(this).parents('ul:first').siblings('.langs-field.lang-' + $(this).data('lang')).addClass('active');
        }
    })

    $('.uploader').each(function() {
        var $this = $(this);

        var swfu = new SWFUpload({
            upload_url: $(this).data('url'),
            flash_url: '/assets/js/swfupload.swf',
            button_placeholder_id: $(this).data('post-name') + '_field',
            file_types: $(this).data('file-types'),
            file_types_description: $(this).data('file-description'),
            button_image_url: '/assets/images/swfupload.png',
            button_width: 32,
            button_height: 32,
            file_size_limit: $(this).data('size-limit'),
            file_post_name: $(this).data('post-name'),
            post_params: $('#' + $(this).data('post-name') + '_post_params').length ? JSON.parse($('#' + $(this).data('post-name') + '_post_params').val()) : null,
            debug: false,

            file_dialog_complete_handler: function () {
                this.startUpload();
            },

            upload_start_handler: function () {
                return true;
            },

            upload_success_handler: function (file, response) {
                $('#' + $this.data('post-name') + '_value').empty();
                $('#' + $this.data('post-name') + '_value').append($(response));

                if ($('#' + $this.data('post-name') + '_post_params').length) {
                    this.setPostParams(JSON.parse($('#' + $this.data('post-name') + '_post_params').val()));
                }

            },

            upload_error_handler: function () {
                var a = 15;
            }
        });
    });
});

