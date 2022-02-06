import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['selector', 'element'];
    static values = {prefix: String};

    connect() {
        this.toggleElements();
    }

    toggle() {
        this.toggleElements();
    }

    toggleElements = () => {
        if (this.hasSelectorTarget) {
            const selected = this.selectorTarget.options[this.selectorTarget.selectedIndex].value;
            this.elementTargets.forEach(t => {
                t.classList.add('atd-cfi__hidden');
                t.querySelectorAll('input, select').forEach(el => {
                    el.disabled = true;
                });
                if (t.querySelector(`#${this.prefixValue + selected}`)) {
                    t.classList.remove('atd-cfi__hidden');
                    t.querySelectorAll('input, select').forEach(el => {
                        el.disabled = false;
                    });
                }
            });
        }
    }
}
