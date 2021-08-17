import {Controller} from 'stimulus';

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
        const selected = this.selectorTarget.options[this.selectorTarget.selectedIndex].value;
        this.elementTargets.forEach(t => {
            t.classList.add('atd-cfi__hidden');
            if (t.querySelector(`#${this.prefixValue + selected}`)) {
                t.classList.remove('atd-cfi__hidden');
            }
        });
    }
}