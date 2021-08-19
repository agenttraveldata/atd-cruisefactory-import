import {Controller} from 'stimulus';

export default class extends Controller {
    static values = {};
    static targets = [];
    notification;

    submit(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const button = e.currentTarget;
        const buttonOriginalText = button.innerHTML;
        button.innerHTML = 'Please wait...';
        button.disabled = true;

        const formData = new FormData(this.element);

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
            button.innerHTML = buttonOriginalText;
            button.disabled = false;
        });
    }
}