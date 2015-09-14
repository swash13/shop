/*price range*/

 $('#sl2').slider();

	var RGBChange = function() {
	  $('#RGB').css('background', 'rgb('+r.getValue()+','+g.getValue()+','+b.getValue()+')')
	};	
		
/*scroll to top*/

function cart_quantity_up() {
    var $this = $(this);

    $.ajax({
        url: $(this).attr('href'),
        type: 'get',
        data: {
            quantity: 1
        },
        success: function(data) {
            var $tbody = $this.parents('tbody').empty().append(data);
            $('.cart_quantity_down', $tbody).click(cart_quantity_down);
            $('.cart_quantity_up', $tbody).click(cart_quantity_up);
            $('.cart_quantity_delete', $tbody).click(cart_delete_product)
        }
    })
}

function cart_quantity_down() {
    var $this = $(this);

    $.ajax({
        url: $(this).attr('href'),
        type: 'get',
        data: {
            quantity: -1
        },
        success: function(data) {
            var $tbody = $this.parents('tbody').empty().append(data);
            $('.cart_quantity_down', $tbody).click(cart_quantity_down);
            $('.cart_quantity_up', $tbody).click(cart_quantity_up);
            $('.cart_quantity_delete', $tbody).click(cart_delete_product)
        }
    })
}

function cart_delete_product() {
    var $this = $(this);

    $.ajax({
        url: $(this).attr('href'),
        success: function(data) {
            var $tbody = $this.parents('tbody').empty().append(data);
            $('.cart_quantity_down', $tbody).click(cart_quantity_down);
            $('.cart_quantity_up', $tbody).click(cart_quantity_up);
            $('.cart_quantity_delete', $tbody).click(cart_delete_product)
        }
    });
}

$(document).ready(function(){
	$(function () {
		$.scrollUp({
	        scrollName: 'scrollUp',
	        scrollDistance: 300,
	        scrollFrom: 'top',
	        scrollSpeed: 300,
	        easingType: 'linear',
	        animation: 'fade',
	        animationSpeed: 200,
	        scrollTrigger: false,

	        scrollText: '<i class="fa fa-angle-up"></i>',
	        scrollTitle: false,
	        scrollImg: false,
	        activeOverlay: false,
	        zIndex: 2147483647
		});

        $('.btn.btn-default.add-to-cart').click(function (e) {
            e.preventDefault();

            $.ajax({
                url: '/index.php?controller=cart',
                type: 'get',
                data: {
                    action: 'add',
                    quantity: '1',
                    product: $(this).data('id')
                },
                success: function(data) {
                    alert(data);
                }
            })
        });

        $('.cart_quantity_down').click(cart_quantity_down);

        $('.cart_quantity_up').click(cart_quantity_up);

        $('.cart_quantity_delete').click(cart_delete_product);

        $('#order .login-form form').ajaxForm({
            dataType: 'json',
            success: function(response) {
                if (response.errors) {
                    $('#order .login-form span.error').remove();

                    if (response.errors.email) {
                        $('#order .login-form input[name=email]').after($('<span class="error">' + response.errors.email + '</span>'));
                    }

                    if (response.errors.password) {
                        $('#order .login-form input[name=password]').after($('<span class="error">' + response.errors.password + '</span>'));
                    }
                } else {
                    $('#order .auth').remove();
                }
            }
        });

        $('#order .signup-form form').ajaxForm({
            dataType: 'json',
            success: function(response) {
                if (response.errors) {
                    $('#order .signup-form span.error').remove();

                    if (response.errors.name) {
                        $('#order .signup-form input[name=name]').after($('<span class="error">' + response.errors.name + '</span>'));
                    }

                    if (response.errors.email) {
                        $('#order .signup-form input[name=email]').after($('<span class="error">' + response.errors.email + '</span>'));
                    }

                    if (response.errors.phone) {
                        $('#order .signup-form input[name=phone]').after($('<span class="error">' + response.errors.phone + '</span>'));
                    }

                    if (response.errors.password) {
                        $('#order .signup-form input[name=password]').after($('<span class="error">' + response.errors.password + '</span>'));
                    }

                    if (response.errors.confirm) {
                        $('#order .signup-form input[name=confirm]').after($('<span class="error">' + response.errors.confirm + '</span>'));
                    }
                } else {
                    $('#order .auth').remove();
                }
            }
        });

        $('#exists-addresses select').change(function() {
            if ($(this).val()) {
                $('#new-address').hide();
            } else {
                $('#new-address').show();
            }
        })
    });
});
