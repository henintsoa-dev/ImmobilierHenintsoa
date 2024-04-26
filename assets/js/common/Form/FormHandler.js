import $ from "jquery";

export class FormHandler {
    /**
     *
     * @param {string} formEltSelector
     * @param {Object} options
     */
    constructor(formEltSelector = "form", options = {}) {
        this.options = {
            gotoFirstError: true,
            submitBtn: '#registration_form_save',
            serveurErrorMessage: 'Une erreur sur notre serveur est survenue, nous venons d\'être notifié de cette erreur. Merci de réessayer ultérieurement.',
            globalErrorContainer: '.global-errors',
            mapping: {},
            ...options
        }
        if ($(formEltSelector).length) this._init($(formEltSelector))
    }

    _init(formElt) {
        this.formElt = formElt
        this.formName = this.formElt.attr('name')
        this.preventUnload = true;
    }

    onFormError(error) {
        const {data = null, status} = error
        const {gotoFirstError, serveurErrorMessage} = this.options
        this._removeAllError()

        if (parseInt(status) === 500) {
            this._displayErrors({'property_globals': [serveurErrorMessage]})
            if (gotoFirstError) this._gotoFirstError()
        }

        if (data === null) return // Error

        const {type = null, errors, redirect = null} = data

        if (redirect !== null) {
            this.preventUnload = false
            window.location.replace(redirect);
        }

        if (type === 'validation_error') {

            const detail = data
            const e = new CustomEvent('FORM_VALIDATION_ERROR', {detail})
            window.dispatchEvent(e)

            const errorFields = this._getErrorFields(errors)
            this._removeAllError()
            this._displayErrors(errorFields)
            if (gotoFirstError) this._gotoFirstError()

        }

        if (type === 'meantime_updated') {
            $('body').addClass('show-modal').find('.overlay-modal').fadeIn(500);
            $('.modal-popin.meantime_updated').fadeIn().css("display", "flex").hide().fadeIn(100);
        }


    }

    _removeAllError() {
        $(".invalid-feedback").remove()
        $(".form-control").removeClass('is-invalid')
    }

    _getErrorFields(errors) {
        let errorFields = {};
        if (errors['errors'] !== undefined) {
            const errorPath = (errors.path === undefined) ? '_globals' : errors.path
            errorFields[this.formName + errorPath] = errors.errors
        } else {
            $.each(errors, (k, error) => {
                const _errorFields = this._getErrorFields(error)
                errorFields = {...errorFields, ..._errorFields}
            })
        }
        return errorFields
    }

    onFormSuccess(response) {
        if (response === null) return // Error
        const {success = false, url = null} = response

        if (success && url !== null) {
            this.preventUnload = false

            window.addEventListener("beforeunload", (e) => {
                if (this.preventUnload === true) {
                    // Cancel the event
                    e.preventDefault()
                    e.returnValue = ""
                }
            });

            window.location.replace(url);
        }
    }

    _displayErrors(errorFields) {

        const globalErrors = [],
            {globalErrorContainer} = this.options

        $.each(errorFields, (errorFieldName, errors) => {
            
            //error mapping defined by user
            for (let originMapping in this.options.mapping) {
                let newMapping = this.options.mapping[originMapping];

                if (errorFieldName.endsWith(originMapping)) {
                    errorFieldName = errorFieldName.replace(originMapping, newMapping);
                }
            }

            let fieldElt = $('[name="' + errorFieldName + '"]')

            if (!fieldElt.length) {
                fieldElt = $('[name="' + errorFieldName + '[]"]');
            }

            if (fieldElt.length) {
                const fieldEltHolder = fieldElt.closest('.mb-3')
                fieldElt.addClass('is-invalid')
                const htmlErrorList = this._getFormHtmlErrorList(errors)
                fieldEltHolder.append(htmlErrorList)
            } else if (errorFieldName === 'property_globals') {
                globalErrors.push(errors)
            }
        })

        if (globalErrors.length > 0) {
            const globalHtmlErrorList = this._getFormHtmlErrorList(globalErrors)
            $(globalErrorContainer).html(globalHtmlErrorList)
        }
    }

    _getFormHtmlErrorList(errors) {
        let listError = this._getLiError(errors)

        return listError
    }

    _getLiError(errors) {
        let errorLi = ''

        if (Array.isArray(errors)) {
            $.each(errors, (i, error) => {
                errorLi += this._getLiError(error)
            });
        } else {
            errorLi += "<div class=\"invalid-feedback d-block\">" + errors + "</div>"
        }
        return errorLi
    }

    _gotoFirstError() {
        if ($('.is-invalid').length > 0) {
            $("html, body").animate({
                scrollTop: parseInt($('.invalid-feedback').first().offset().top) - 150
            }, 0);
        }
    }
}
