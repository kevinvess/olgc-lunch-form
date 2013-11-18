function calculateMenus(form) {
    var _form = $(form);

    $('.numeric-only').change(function () {
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });

    // Menus
    _form.find('.menus input[id*="-extra-"]').each(function(){
        $(this).prop('disabled', true);
    });
    _form.find('.menus input').change(function(){
        var input = $(this);
        var menu = input.parents('.section');
        var meal = menu.find('input[id*="-meal"]');
        var extra = menu.find('input[id*="-extra-"]');

        // get menu data
        var days = (menu.attr('data-days') !== '') ? menu.attr('data-days') : 0;
        var mealPrice = (meal.attr('data-price')) ? parseInt(meal.attr('data-price')) : 0;
        var extraPrice = (extra.attr('data-price')) ? parseInt(extra.attr('data-price')) : 0;

        // calculate extra cost
        var extraCost;
        if(extra) {
            var extraCount;

            // if multiple extra fields
            if(extra.length > 1) {
                var mealID = input.attr('id').match(/\d+$/)[0];

                // use extra that matches meal option
                extra.each(function(){
                    var extraID = $(this).attr('id').match(/\d+$/)[0];
                    if(extraID === mealID) {
                        $(this).prop('disabled', false);
                        extraCount = parseFloat(extra.val());
                    } else {
                        $(this).prop('disabled', true);
                    }
                });
            } else {
                menu.find('input[id*="-extra-"]').prop('disabled', false);
                extraCount = parseFloat(extra.val());
            }

            if(isNaN(extraCount))
                extraCount = 0;

            extraCost = extraPrice * extraCount;
        }

        // update totals
        if (input.is('input[type="text"]') && input.val() !== '' || input.is(':checked')) {
            menu.find('input[id$="-subtotal"]').val('$' + (mealPrice + extraCost).toFixed(2));
            menu.find('input[id$="-total"]').val('$' + ((mealPrice + extraCost) * days).toFixed(2));
        }
        else {
            menu.find('input[id$="-subtotal"], input[id$="-total"]').val('$0.00');
            menu.find('input[id*="-extra-"]').val('0');
            menu.find('input[id*="-extra-"]').prop('disabled', true);
        }
    });

    // Total Cost
    _form.find('.menus input').change(function() {
        var totals = [];
        var total = 0;

        // compile totals
        $('.menus input[id$="-total"]').each(function(index){
            totals[index] = $(this).val();
        });

        // calculate totals
        for(var i=0; i < totals.length; i++) {
            totals[i] = totals[i].replace('$', '');
            total += parseFloat(totals[i]);
        }

        // update total listed
        $('#order-total').val('$' + total.toFixed(2));
        $('#purchaseAmount').val(total.toFixed(2));
    });
}
function validateForm(form) {
    // Set variables
    var _form = $(form);
    var email = _form.find('#email');
    var submited = false;

    var emailRegex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var cssObjError = {
      'border-top-color' : '#bf3736',
      'border-right-color' : '#bf3736',
      'border-bottom-color' : '#bf3736',
      'border-left-color' : '#bf3736',
      'background-color' : '#fee2e2'
    };
    var cssObjClear = {
      'border-top-color' : '#ccc',
      'border-right-color' : '#ccc',
      'border-bottom-color' : '#ccc',
      'border-left-color' : '#ccc',
      'background-color' : '#fff'
    };

    // Validate form fields onblur
    $('input.required').focusout(function() {
        if($(this).val() !== '')
            $(this).css(cssObjClear);
        else
            $(this).css(cssObjError);
    });
    email.focusout(function() {
        if(email.val() !== '' && emailRegex.test(email.val()))
            email.css(cssObjClear);
        else
            email.css(cssObjError);
    });

    // Validate form before submiting
    _form.submit(function(e) {
        var hasError = false;
        var y = 0;
        var emailVal = email.val();

        // Popup settings
        var _wrapperHeight = $(document).height();
        var _popup = $('.processing div');

        $('.processing').css("height", _wrapperHeight + "px");
        $(_popup).center();

        // Validate required fields
        $('input.required').each(function(index) {
            if($(this).val() === '') {
                $(this).css(cssObjError);
                
                if(y === 0)
                    y = $(this).offset().top - 26;
                
                hasError = true;
            }
        });
        if(emailVal === '' || !emailRegex.test(emailVal)) {
            email.css(cssObjError);
            hasError = true;
        }

        if(hasError === true) {
            $("html, body").animate({ scrollTop: y }, "slow");
            return false;
        }
        else {
            submited = true;
            $('.processing').fadeIn("fast");
            return true;
        }

        // Prevent multiple submissions
        if(submited) return false;
    });
}
function clearFormFields(o) {
	if (o.clearInputs === null) o.clearInputs = true;
	if (o.clearTextareas === null) o.clearTextareas = true;
	if (o.passwordFieldText === null) o.passwordFieldText = false;
	if (o.addClassFocus === null) o.addClassFocus = false;
	if (!o.filter) o.filter = "default";
	if(o.clearInputs) {
		var inputs = document.getElementsByTagName("input");
		for (var i = 0; i < inputs.length; i++ ) {
			if((inputs[i].type == "text" || inputs[i].type == "password") && inputs[i].className.indexOf(o.filterClass)) {
				inputs[i].valueHtml = inputs[i].value;
				inputs[i].onfocus = function ()	{
					if(this.valueHtml == this.value) this.value = "";
					if(this.fake) {
						inputsSwap(this, this.previousSibling);
						this.previousSibling.focus();
					}
					if(o.addClassFocus && !this.fake) {
						this.className += " " + o.addClassFocus;
						this.parentNode.className += " parent-" + o.addClassFocus;
					}
				};
				inputs[i].onblur = function () {
					if(this.value === "") {
						this.value = this.valueHtml;
						if(o.passwordFieldText && this.type == "password") inputsSwap(this, this.nextSibling);
					}
					if(o.addClassFocus) {
						this.className = this.className.replace(o.addClassFocus, "");
						this.parentNode.className = this.parentNode.className.replace("parent-"+o.addClassFocus, "");
					}
				};
				if(o.passwordFieldText && inputs[i].type == "password") {
					var fakeInput = document.createElement("input");
					fakeInput.type = "text";
					fakeInput.value = inputs[i].value;
					fakeInput.className = inputs[i].className;
					fakeInput.fake = true;
					inputs[i].parentNode.insertBefore(fakeInput, inputs[i].nextSibling);
					inputsSwap(inputs[i], null);
				}
			}
		}
	}
	if(o.clearTextareas) {
		var textareas = document.getElementsByTagName("textarea");
		for(var i=0; i<textareas.length; i++) {
			if(textareas[i].className.indexOf(o.filterClass)) {
				textareas[i].valueHtml = textareas[i].value;
				textareas[i].onfocus = function() {
					if(this.value == this.valueHtml) this.value = "";
					if(o.addClassFocus) {
						this.className += " " + o.addClassFocus;
						this.parentNode.className += " parent-" + o.addClassFocus;
					}
				};
				textareas[i].onblur = function() {
					if(this.value == "") this.value = this.valueHtml;
					if(o.addClassFocus) {
						this.className = this.className.replace(o.addClassFocus, "");
						this.parentNode.className = this.parentNode.className.replace("parent-"+o.addClassFocus, "");
					}
				};
			}
		}
	}
	function inputsSwap(el, el2) {
		if(el) el.style.display = "none";
		if(el2) el2.style.display = "inline";
	}
}
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() - 150 + "px");
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() - 16 + "px");
    return this;
};
/* jQuery DOMdocument.ready */
$(document).ready(function(){
    clearFormFields({
        clearInputs: true,
        clearTextareas: true,
        passwordFieldText: true,
        addClassFocus: "focus",
        filterClass: "readonly"
    });
    calculateMenus('#OrderForm');
    validateForm('#OrderForm');
});