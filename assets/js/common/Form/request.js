import axios from "axios"
import {FormHandler} from "./FormHandler.js"

export const postData = () => {

    /**
     * The form handler.
     *
     * @var {FormHandler}
     */
    const formHandler = new FormHandler('#property-form');

    const
        registrationForm = document.getElementById("property-form"),
        data = new FormData(registrationForm)
    ;
                
    axios({
            method: "post",
            url: window.location.href,
            data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                "Content-Type": "multipart/form-data"
            }
        }
    )
    .then(response => {
        const {data = null} = response;

        formHandler.onFormSuccess(data);
    })
    .catch(error => {
        const {response = null} = error;

        formHandler.onFormError(response);
    })
}
