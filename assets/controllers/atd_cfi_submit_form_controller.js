import {Controller} from 'stimulus';

export default class extends Controller {
    static values = {};
    static targets = ['submitButton'];
    notification;

    connect() {
        if (this.element.querySelector('#recaptcha')) {
            this.initRecaptcha();
        }
    }

    initRecaptcha() {
        this.submitButtonTarget.type = 'button';
        this.submitButtonTarget.addEventListener('click', () => {
            grecaptcha.execute();
        });
    }

    submit(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const recaptchaType = e.target.querySelector('#atd-cfi-recaptcha-type');

        switch (recaptchaType.dataset.type) {
            case 'v3':
                if (typeof atd_cfi !== 'undefined' && atd_cfi.hasOwnProperty('recaptcha_site_key') && typeof grecaptcha !== 'undefined') {
                    grecaptcha.ready(() => {
                        grecaptcha.execute(atd_cfi.recaptcha_site_key, {action: 'submit'}).then((token) => {
                            this.doSubmit(e.target, token);
                        });
                    });
                }
                break;
            case 'v2i':
            case 'v2c':
                this.doSubmit(e.target);
                grecaptcha.reset();
                break;
        }
    }

    doSubmit = (target, token = null) => {
        const buttonOriginalText = this.submitButtonTarget.innerHTML;
        this.submitButtonTarget.innerHTML = 'Please wait...';
        this.submitButtonTarget.disabled = true;

        const formData = new FormData(this.element);

        if (token) {
            formData.append('g-recaptcha-response', token);
        }

        if (this.notification) {
            this.notification.remove();
        }

        this.notification = document.createElement('div');
        this.notification.classList.add('atd-cfi__alert');
        this.element.prepend(this.notification);

        fetch(this.element.action, {
            method: this.element.method.toUpperCase(),
            body: formData
        }).then(r => r.json()).then(json => {
            if (json.hasOwnProperty('success') && json.success === true) {
                this.notification.classList.add('atd-cfi-alert__success');
                this.element.reset();
            } else {
                this.notification.classList.add('atd-cfi-alert__error');
            }

            if (json.hasOwnProperty('data') && json.data.hasOwnProperty('message')) {
                this.notification.innerHTML = json.data.message;
            } else {
                this.notification.textContent = 'Unable to send message. Please contact site admin.';
            }
        }).catch(r => console.log(r)).finally(() => {
            this.notification.scrollIntoView(true);
            this.submitButtonTarget.innerHTML = buttonOriginalText;
            this.submitButtonTarget.disabled = false;
        });
    }
}