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
    
        if (document.querySelector(formEltSelector).length) this._init(document.querySelector(formEltSelector))
    }

    _init(formElt) {
        this.formElt = formElt
        this.formName = this.formElt.getAttribute('name')
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

    }

    _removeAllError() {
        document.querySelectorAll(".invalid-feedback").forEach((item) => {item.remove()})
        document.querySelectorAll(".form-control").forEach((item) => {item.classList.remove('is-invalid')})
    }

    _getErrorFields(errors) {
        let errorFields = {};
        if (errors['errors'] !== undefined) {
            const errorPath = (errors.path === undefined) ? '_globals' : errors.path
            errorFields[this.formName + errorPath] = errors.errors
        } else {
            for (let [key, error] of Object.entries(errors)) {
                const _errorFields = this._getErrorFields(error)
                errorFields = {...errorFields, ..._errorFields}
            }
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

        for(let [errorFieldName, errors] of Object.entries(errorFields)) {
            //error mapping defined by user
            for (let originMapping in this.options.mapping) {
                let newMapping = this.options.mapping[originMapping];

                if (errorFieldName.endsWith(originMapping)) {
                    errorFieldName = errorFieldName.replace(originMapping, newMapping);
                }
            }

            let fieldElt = document.querySelector("[name='" + errorFieldName + "']")

            if (!fieldElt) {
                fieldElt = document.querySelector("[name='" + errorFieldName + "[]']");
            }

            if (fieldElt) {

                let fieldEltHolder = fieldElt.parentNode;
  
                while ( !fieldEltHolder.classList.contains('mb-3') ) {
                    fieldEltHolder = fieldEltHolder.parentNode
                }

                fieldElt.classList.add('is-invalid')
                
                const htmlErrorList = this._getFormHtmlErrorList(errors)
                fieldEltHolder.insertAdjacentHTML('beforeend', htmlErrorList)
                
            } else if (errorFieldName === 'property_globals') {
                globalErrors.push(errors)
            }
        }

        if (globalErrors.length > 0) {
            const globalHtmlErrorList = this._getFormHtmlErrorList(globalErrors)
            document.querySelector(globalErrorContainer).insertAdjacentHTML( 'beforeend', globalHtmlErrorList);
        }
    }

    _getFormHtmlErrorList(errors) {
        let listError = this._getLiError(errors)

        return listError
    }

    _getLiError(errors) {
        let errorLi = ''

        if (Array.isArray(errors)) {
            errors.forEach((error, i) => {
                errorLi = this._getLiError(error)
            });
        } else {
            errorLi += "<div class=\"invalid-feedback d-block\">" + errors + "</div>"
        }
        return errorLi
    }

    _gotoFirstError() {
        if (document.querySelectorAll('.is-invalid').length > 0) {
            document.querySelector('html, body').scrollTop = document.querySelector('.invalid-feedback').offsetTop - 150;
        }
    }
}
