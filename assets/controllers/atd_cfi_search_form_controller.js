import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {};
    static targets = ['dropDown'];
    apiPath = '/wp-json/atd/cfi/v1/search/options';
    defaultValues = {};
    formData;

    connect() {
        this.dropDownTargets.forEach(e => {
            if (!this.defaultValues.hasOwnProperty(e.name)) {
                this.defaultValues[e.name] = [];
            }

            if (e.value !== '') {
                this.defaultValues[e.name].push(e.value);
            }
        });

        this.formData = new FormData(this.element);
        const hasUrlParams = window.location.search !== ''
            ? window.location.search
            : window.location.hash !== ''
                ? window.location.hash
                : null;
        if (hasUrlParams) {
            const urlSearchParams = new URLSearchParams(hasUrlParams.replace(/^#/, '?'));
            urlSearchParams.forEach((v, k) => {
                if (v && this.formData.has(k)) {
                    this.formData.set(k, v);
                }
            });
            this.update(null, this.formData);
        } else {
            this.update(this.formData);
        }
    }

    populateDropDowns = (d) => {
        Object.entries(d).forEach(([key, value]) => {
            if (key.match(/month$/)) {
                this.sortAndPopulateDropDown(`${key}_from`, value);
                this.sortAndPopulateDropDown(`${key}_to`, value);
                return;
            }

            this.sortAndPopulateDropDown(key, value);
        });
    }

    sortAndPopulateDropDown = (key, value) => {
        const el = this.element.querySelector(`#${key}`);
        const numericKeys = ['atd_cf_month_from', 'atd_cf_month_to', 'atd_cf_duration'];

        if (el) {
            el.innerHTML = '';
            el.options.add(new Option(`Any ${this.snakeToWords(key.slice(6))}...`, ''));
            if (this.element.querySelector(`#${key}_block`)) {
                el.disabled = "disabled";
            }

            Object.entries(value).sort(numericKeys.includes(key) ? this.compareNumericValues : this.compareKeyStrings).forEach(([id, name]) => {
                el.options.add(new Option(name, id, false,
                    this.formData.has(key) && this.formData.get(key) === id
                ));
            });
        }
    }

    compareKeyStrings = ([, a], [, b]) => {
        return a.localeCompare(b);
    }

    compareNumericValues = (a, b) => {
        return a > b;
    }

    update(e, formData = null) {
        if (!formData) {
            this.formData = new FormData(this.element);
        }

        fetch(`${this.apiPath}?${new URLSearchParams(this.formData)}`).then(r => r.json()).then(this.populateDropDowns);
    }

    reset(e) {
        const departureType = this.formData.get('atd_cf_departure_type');
        this.formData = new FormData();
        if (departureType) {
            this.formData.set('atd_cf_departure_type', departureType);
        }

        fetch(`${this.apiPath}?${new URLSearchParams(this.formData)}`).then(r => r.json()).then(this.populateDropDowns);
    }

    snakeToWords = (e) => {
        return this.capitalizeFirstLetter(e.replace(/_([a-z])/g, (g) => ` ${g[1].toUpperCase()}`));
    }

    capitalizeFirstLetter = (s) => {
        return s[0].toUpperCase() + s.slice(1);
    }
}
