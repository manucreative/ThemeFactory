define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-payment-method',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'mage/url',

], function ($, Component, placeOrderAction, quote, selectPaymentMethodAction,storage, errorProcessor, checkoutData, customerData, url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ThemeFactory_Mpesastk/payment/mpesastk',
            redirectAfterPlaceOrder: true
        },

        validatePayment: function () {
            $('.return-message-payment').html('');
            var self = this;


// if (quote.paymentMethod() && quote.paymentMethod().method === 'themeFactory_mpesastk') {
    // Define modal outside the click function
    var modalContent = $('<div/>').html(
        '<br><input type="text" id="mpesa-phone-number" placeholder="Phone Number"><br>' +
        '<span id="mpesa_phone_error" class="pay_false" style="display: none;"> Please input a valid phone</span>' +
        '<span id="processing" style="display: none;"> You payment is under processing please wait</span>' +
        '<span class="init_error" class="pay_true"></span>'
    );

    var modal = modalContent.modal({
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: 'Please enter Phone No. then enter Mpesa PIN on your phone',
        modalClass: 'contact-modal-class',
        buttons: [{
            text: $.mage.__('Process Mpesa STK'),
            class: 'action primary processButton',
            id: 'processButton',
            click: function () {

                var button = $(this).find('.processButton');
            var originalText = button.text();
            button.text($.mage.__('Processing Payment...')).attr('disabled', true);
            button.data('original-text', originalText);


                var phoneNumber = $('#mpesa-phone-number').val();

                var phoneNumber = phoneNumber;
                var orderId = quote.getQuoteId();
                var totalAmount = quote.totals()['base_grand_total'];
                var customerName = quote.billingAddress().firstname + ' ' + quote.billingAddress().lastname;

                var baseUrl = window.BASE_URL;
                var controllerUrl = 'stkcallback/stkpush/index';
                var serviceUrl = baseUrl + controllerUrl;

                if (!phoneNumber.length) {
                    $('#mpesa_phone_error').show();
                    return false;
                }


                $.ajax({
                    url:serviceUrl,
                    method: 'POST',
                    data: {phoneNumber: phoneNumber, orderId: orderId, totalAmount: totalAmount, customerName: customerName},
                    dataType: 'json',
                     beforeSend: function(){
                        $('.paybill-preload').show();
                        $('#mpesa-phone-number').hide();

                            $('#processing').show();
                            $('#mpesa_phone_error').hide();
                            $('.init_error').hide();
                    },
                    success: function(response) {
                        console.log(response);
                        var obj = response;

                        switch(obj.success) {

                            case 'done':
                                        $('.return-message-payment').removeClass('pay_false').addClass('pay_true').html(obj.message);
                                        modal.modal('closeModal');
                                        $('.paybill-preload').hide();

                                        var modalSuccess = $('<div/>').html(
                                            '<br><div class="pay_true" id="successProcess" style="font-size: 26px; text-alig"></div><br>'+
                                            '<div class="count_down"></div>'
                                        );

                                         var successModal = modalSuccess.modal({
                                            type: 'popup',
                                            responsive: true,
                                            innerScroll: true,
                                            title: 'Transaction is underway, Please wait',
                                             modalClass: 'success-modal-class', 
                                                buttons: [{
                                                    text: $.mage.__('Close'),
                                                    class: 'action primary',
                                                    click: function () {
                                                        successModal.modal('closeModal');
                                                    }
                                                }]
                                        });
                                        successModal.modal('openModal');

                                        var callbackUrl = 'stkcallback/stkpush/paymentConfirmation';
                                        var callUrl = baseUrl + callbackUrl;

                                        var myVar;

                                                var startTime = Date.now();
                                                myVar = setInterval(function () {
                                                    var elapsedTime = Date.now() - startTime;

                                                    // Calculate remaining time
                                                        var remainingTime = Math.max(0, 90000 - elapsedTime);

                                                        // Calculate minutes and seconds
                                                        var minutes = Math.floor(remainingTime / 90000);
                                                        var seconds = Math.floor((remainingTime % 90000) / 1000);

                                                        // Update countdown display
                                                        $('.count_down').html(minutes + ":" + (seconds < 10 ? "0" : "") + seconds);

                                                    if (elapsedTime >= 90000) {
                                                        clearInterval(myVar);
                                                        $('#successProcess')
                                                            .addClass('pay_false')
                                                            .removeClass('pay_true')
                                                            .html('System took too long to respond, please click <a href="#" class="btn btn-success" id="confirmPaymentLink">HERE</a> to recheck your payment if you INPUT your Mpesa PIN');

                                                            $('#confirmPaymentLink').on('click', function(e) {
                                                                e.preventDefault();
                                                                var confirmUrl = 'stkcallback/stkpush/recheckpayment';
                                                                    var callConfirm = baseUrl + confirmUrl;


                                                                            $.ajax({
                                                                                url:callConfirm,
                                                                                method: 'POST',
                                                                                data: {checkout_r_id: obj.checkout_r_id},
                                                                                dataType: 'json',
                                                                                success: function (respond) {
                                                                                    console.log(respond);
                                                                                    var resp = respond;

                                                                                    $('#successProcess').removeClass('pay_false').addClass('pay_true').html(resp.message);
                                                                                    if (resp.success == true) {
                                                                                        if (resp.code == 0) {
                                                                                            return self.placeOrder();
                                                                                        } else {
                                                                                            $('#mpesa-phone-number').removeAttr('disabled');
                                                                                            $('.paybill-preload').hide();
                                                                                        }
                                                                                        // clearInterval(myVar);
                                                                                    }
                                                                                },
                                                                                error: function (xhr, status, error) {
                                                                                    console.error(xhr.responseText);
                                                                                    $('#successProcess').addClass('pay_false').removeClass('pay_true').html('Error: An error occurred during callback'); // Update here
                                                                                }
                                                                            });
                                                            });

                                                    } else {

                                                        $.ajax({
                                                            url:callUrl,
                                                            method: 'POST',
                                                            data: { merchant_r_id: obj.merchant_r_id, checkout_r_id: obj.checkout_r_id, account_id:obj.account_id},
                                                            dataType: 'json',
                                                            success: function (callbackResponse) {
                                                                    console.log(callbackResponse);
                                                                    var resp = callbackResponse;

                                                                    $('#successProcess').removeClass('pay_false').addClass('pay_true').html(resp.message);

                                                                    if (resp.success == true) {
                                                                        console.log("Code:", resp.code);
                                                                        if (resp.code == 0) {
                                                                            // $('#successProcess').removeClass('pay_false').addClass('pay_true').html("Your Order is being processed, please wait");
                                                                            return self.placeOrder();
                                                                        } else {
                                                                            $('#mpesa-phone-number').removeAttr('disabled');
                                                                            $('.paybill-preload').hide();
                                                                        }
                                                                        clearInterval(myVar);
                                                                    }
                                                                },
                                                            error: function (xhr, status, error) {
                                                                console.error(xhr.responseText);
                                                                $('#successProcess').addClass('pay_false').removeClass('pay_true').html('Error: An error occurred during callback'); // Update here
                                                            }
                                                        });
                                                    }
                                                }, 3000);
                                     
                                break;

                            case 'error':
                                $('.return-message-payment').removeClass('pay_true').addClass('pay_'+obj.success).html(obj.message);
                                $('.init_error').removeClass('pay_true').addClass('pay_'+obj.success).html(obj.message);
                                $('#mpesa-phone-number').removeAttr('disabled');
                                $('.paybill-preload').hide();
                                $('#mpesa-phone-number').show();
                                $('#processing').hide();
                                break;

                            default:
                                $('.return-message-payment').removeClass('pay_true').addClass('pay_false').html('An error occurred');
                                 var button = modal.find('#processButton');
                                var originalText = button.data('original-text');
                                button.text(originalText).removeAttr('disabled'); 
                                $('.init_error').show();
                        }

                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        errorProcessor.process(xhr, self.messageContainer);
                        self.isPlaceOrderActionAllowed(true);
                        $('#mpesa-phone-number').removeAttr('disabled');
                        $('.paybill-preload').hide();
                        $('#mpesa-phone-number').show();
                        $('#processing').hide();
                    },

                     complete: function() {
                        button.text(originalText).removeAttr('disabled');
                    },


                });

                return false;
            }
        }]
    });

    modal.modal('openModal');

    // }
       
        },
            afterPlaceOrder: function (data, event) {
                var baseUrl = window.BASE_URL;
                var confirmUrl1 = 'stkcallback/stkpush/complete';
                var completeUrl = baseUrl + confirmUrl1;
                $.mage.redirect(completeUrl);
            }
            
      
    });
});

